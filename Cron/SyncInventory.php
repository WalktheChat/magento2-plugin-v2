<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Cron;

/**
 * Class SyncInventory
 *
 * @package Walkthechat\Walkthechat\Cron
 */
class SyncInventory
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $varDirectory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Locking file (locked)
     *
     * @var string
     */
    const LOCK_FILE_NAME_LOCKED = 'walkthechat_inventory.lock';

    /**
     * Locking file (unlocked)
     *
     * @var string
     */
    const LOCK_FILE_NAME_UNLOCKED = 'walkthechat_inventory';

    /**
     * @var \Walkthechat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * @var \Walkthechat\Walkthechat\Model\ProductService
     */
    protected $productService;

    /**
     * @var \Walkthechat\Walkthechat\Api\InventoryRepositoryInterface
     */
    protected $inventoryRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Walkthechat\Walkthechat\Service\ProductsRepository
     */
    protected $queueProductRepository;

    /**
     * SyncInventory constructor.
     *
     * @param \Magento\Framework\App\State                              $state
     * @param \Magento\Framework\Filesystem                             $filesystem
     * @param \Magento\Framework\App\Filesystem\DirectoryList           $directoryList
     * @param \Psr\Log\LoggerInterface                                  $logger
     * @param \Walkthechat\Walkthechat\Helper\Data                      $helper
     * @param \Walkthechat\Walkthechat\Model\ProductService             $productService
     * @param \Walkthechat\Walkthechat\Api\InventoryRepositoryInterface $inventoryRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder              $searchCriteriaBuilder
     * @param \Walkthechat\Walkthechat\Service\ProductsRepository       $queueProductRepository
     */
    public function __construct(
        \Magento\Framework\App\State                                $state,
        \Magento\Framework\Filesystem                               $filesystem,
        \Magento\Framework\App\Filesystem\DirectoryList             $directoryList,
        \Psr\Log\LoggerInterface                                    $logger,
        \Walkthechat\Walkthechat\Helper\Data                        $helper,
        \Walkthechat\Walkthechat\Model\ProductService               $productService,
        \Walkthechat\Walkthechat\Api\InventoryRepositoryInterface   $inventoryRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder                $searchCriteriaBuilder,
        \Walkthechat\Walkthechat\Service\ProductsRepository         $queueProductRepository
    ) {
        $this->state                    = $state;
        $this->filesystem               = $filesystem;
        $this->directoryList            = $directoryList;
        $this->logger                   = $logger;
        $this->helper                   = $helper;
        $this->productService           = $productService;
        $this->inventoryRepository      = $inventoryRepository;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
        $this->queueProductRepository   = $queueProductRepository;
    }

    /**
     * Process items from queue
     *
     * @throws \Magento\Framework\Exception\CronException
     * @throws \Exception
     */
    public function execute()
    {
        $varDirectory = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
        $syncStatus = (int)$this->helper->getInventorySyncStatus();

        if (!$this->helper->isEnabledProductSync() || $this->isCronLocked($varDirectory) || !$syncStatus) {
            return;
        }

        try {
            $isLocked = $this->lockCron($varDirectory);

            if ($isLocked) {
                $this->initAreaCode();

                if ($syncStatus == \Walkthechat\Walkthechat\Api\Data\InventoryInterface::STATUS_GENERATE) {
                    $this->inventoryRepository->truncate();
                    $this->inventoryRepository->bulkSave($this->productService->prepareInventoryData());

                    $this->helper->setInventorySyncStatus(\Walkthechat\Walkthechat\Api\Data\InventoryInterface::STATUS_PROCESS);
                } elseif ($syncStatus == \Walkthechat\Walkthechat\Api\Data\InventoryInterface::STATUS_PROCESS) {
                    $searchCriteria = $this->searchCriteriaBuilder->create();
                    $searchCriteria->setPageSize(500);

                    for ($i = 1; $i <= $this->helper->getInventoryBatch(); $i++) {
                        $searchCriteria->setCurrentPage($i);

                        $ids = [];
                        $data = [];

                        foreach ($this->inventoryRepository->getList($searchCriteria)->getItems() as $item) {
                            $ids[] = $item->getId();
                            $data[$item->getWalkthechatId()][] = [
                                'id' => $item->getProductId(),
                                'inventoryQuantity' => $item->getQty(),
                                'visibility' => (bool)$item->getVisibility()
                            ];
                        }

                        $batch = [];
                        foreach ($data as $id => $variants) {
                            $batch[] = [
                                'id' => $id,
                                'variants' => $variants
                            ];
                        }

                        $this->queueProductRepository->updateInventory(['products'  => $batch]);

                        $this->inventoryRepository->bulkDelete($ids);

                        if ($this->inventoryRepository->getList($searchCriteria)->getTotalCount() == 0) {
                            $this->helper->setInventorySyncStatus(\Walkthechat\Walkthechat\Api\Data\InventoryInterface::STATUS_IDLE);
                            break;
                        }
                    }
                }
            }
        } catch (\Magento\Framework\Exception\FileSystemException $fileSystemException) {
            throw new \Magento\Framework\Exception\CronException(
                __('Unable to lock the cron. Please check your "var" folder permissions.')
            );
        } catch (\Exception $exception) {
            $this->logger->error(
                "WalkTheChat | Internal error occurred: {$exception->getMessage()}",
                $exception->getTrace()
            );
        } finally {
            $this->unlockCron($varDirectory);
        }
    }

    /**
     * Check if cron is locked
     * If file doesn't exists then trying to create it
     *
     * @param \Magento\Framework\Filesystem\Directory\WriteInterface $folderManager
     *
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function isCronLocked(\Magento\Framework\Filesystem\Directory\WriteInterface $folderManager)
    {
        if ($folderManager->isExist(self::LOCK_FILE_NAME_LOCKED)) {
            return true;
        }

        if (!$folderManager->isExist(self::LOCK_FILE_NAME_UNLOCKED)) {
            $folderManager->writeFile(self::LOCK_FILE_NAME_UNLOCKED, '');
        }

        return false;
    }

    /**
     * Locks the cron
     *
     * @param \Magento\Framework\Filesystem\Directory\WriteInterface $folderManager
     *
     * @return bool
     */
    protected function lockCron(\Magento\Framework\Filesystem\Directory\WriteInterface $folderManager)
    {
        try {
            $folderManager->renameFile(self::LOCK_FILE_NAME_UNLOCKED, self::LOCK_FILE_NAME_LOCKED);
        } catch (\Magento\Framework\Exception\FileSystemException $e) {
            return false;
        }

        return true;
    }

    /**
     * Unlocks the cron
     *
     * @param \Magento\Framework\Filesystem\Directory\WriteInterface $folderManager
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CronException
     */
    protected function unlockCron(\Magento\Framework\Filesystem\Directory\WriteInterface $folderManager)
    {
        try {
            $folderManager->renameFile(self::LOCK_FILE_NAME_LOCKED, self::LOCK_FILE_NAME_UNLOCKED);
        } catch (\Magento\Framework\Exception\FileSystemException $e) {
            throw new \Magento\Framework\Exception\CronException(__('Unable to unlock the walkthechat invenotyr.'));
        }

        return true;
    }

    /**
     * Initialize area code
     */
    protected function initAreaCode()
    {
        try {
            $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_CRONTAB);
        } catch (\Exception $exception) {
            // if area code was already set, then just continue work...
        }
    }
}

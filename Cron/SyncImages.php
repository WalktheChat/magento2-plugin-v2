<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Cron;

/**
 * Class SyncImages
 *
 * @package Walkthechat\Walkthechat\Cron
 */
class SyncImages
{
    /**
     * Locking file (locked)
     *
     * @var string
     */
    const SYNC_IMAGES_LOCK_FILE_NAME_LOCKED = 'walkthechat_sync_images.lock';

    /**
     * Locking file (locked)
     *
     * @var string
     */
    const SYNC_IMAGES_LOCK_FILE_NAME_UNLOCKED = 'walkthechat_sync_images';

    /**
     * How many images would be proceed per cron request
     *
     * @var int
     */
    const ITEMS_PER_PROCESS = 20;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \Walkthechat\Walkthechat\Model\QueueService
     */
    protected $queueService;

    /**
     * @var \Walkthechat\Walkthechat\Model\QueueRepository
     */
    protected $queueRepository;

    /**
     * @var \Walkthechat\Walkthechat\Api\Data\QueueInterfaceFactory
     */
    protected $queueFactory;

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
     * @var \Walkthechat\Walkthechat\Api\ImageSyncRepositoryInterface
     */
    protected $imageSyncRepository;

    /**
     * @var \Walkthechat\Walkthechat\Api\ContentMediaRepositoryInterface
     */
    protected $contentMediaRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    protected $searchCriteria;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroupBuilder
     */
    protected $filterGroupBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Walkthechat\Walkthechat\Model\Template\Filter
     */
    protected $filter;

    /**
     * @var \Walkthechat\Walkthechat\Model\ImageService
     */
    protected $imageService;

    /**
     * @var \Walkthechat\Walkthechat\Api\Data\ImageSyncInterfaceFactory
     */
    protected $imageSyncFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Walkthechat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
     */
    protected $configurableProductType;

    /**
     * SyncImages constructor.
     * @param \Magento\Framework\App\State $state
     * @param \Walkthechat\Walkthechat\Model\QueueService $queueService
     * @param \Walkthechat\Walkthechat\Model\QueueRepository $queueRepository
     * @param \Walkthechat\Walkthechat\Api\Data\QueueInterfaceFactory $queueFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Walkthechat\Walkthechat\Api\ImageSyncRepositoryInterface $imageSyncRepository
     * @param \Walkthechat\Walkthechat\Api\ContentMediaRepositoryInterface $contentMediaRepository
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Walkthechat\Walkthechat\Model\Template\Filter $filter
     * @param \Walkthechat\Walkthechat\Model\ImageService $imageService
     * @param \Walkthechat\Walkthechat\Api\Data\ImageSyncInterfaceFactory $imageSyncFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Walkthechat\Walkthechat\Helper\Data $helper
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProductType
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        \Walkthechat\Walkthechat\Model\QueueService $queueService,
        \Walkthechat\Walkthechat\Model\QueueRepository $queueRepository,
        \Walkthechat\Walkthechat\Api\Data\QueueInterfaceFactory $queueFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Psr\Log\LoggerInterface $logger,
        \Walkthechat\Walkthechat\Api\ImageSyncRepositoryInterface $imageSyncRepository,
        \Walkthechat\Walkthechat\Api\ContentMediaRepositoryInterface $contentMediaRepository,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Walkthechat\Walkthechat\Model\Template\Filter $filter,
        \Walkthechat\Walkthechat\Model\ImageService $imageService,
        \Walkthechat\Walkthechat\Api\Data\ImageSyncInterfaceFactory $imageSyncFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProductType
    ) {
        $this->state                    = $state;
        $this->queueService             = $queueService;
        $this->queueRepository          = $queueRepository;
        $this->queueFactory             = $queueFactory;
        $this->filesystem               = $filesystem;
        $this->directoryList            = $directoryList;
        $this->logger                   = $logger;
        $this->imageSyncRepository      = $imageSyncRepository;
        $this->contentMediaRepository   = $contentMediaRepository;
        $this->searchCriteria           = $searchCriteria;
        $this->filterGroupBuilder       = $filterGroupBuilder;
        $this->filterBuilder            = $filterBuilder;
        $this->filter                   = $filter;
        $this->imageService             = $imageService;
        $this->imageSyncFactory         = $imageSyncFactory;
        $this->productRepository        = $productRepository;
        $this->helper                   = $helper;
        $this->configurableProductType  = $configurableProductType;
    }

    /**
     * Process items from queue
     *
     * @throws \Magento\Framework\Exception\CronException
     * @throws \Exception
     */
    public function execute()
    {
        $varDirectory = $this->filesystem->getDirectoryWrite(
            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR
        );

        try {
            if ($this->isCronLocked($varDirectory)) {
                return;
            }

            $isLocked = $this->lockCron($varDirectory);

            if ($isLocked) {
                $this->initAreaCode();

                $filterGroup = $this->filterGroupBuilder
                    ->addFilter(
                        $this->filterBuilder
                            ->setField('image_data')
                            ->setConditionType('eq')
                            ->setValue('')
                            ->create()
                    )
                    ->create();

                $this->searchCriteria->setFilterGroups([$filterGroup])->setPageSize(self::ITEMS_PER_PROCESS);

                $images = $this->imageSyncRepository->getList($this->searchCriteria)->getItems();

                foreach ($images as $image) {
                    $product = $this->productRepository->getById($image->getProductId());
                    $response = $this->imageService->addImage($product->getSku(), $image->getImageId());

                    if ($response) {
                        $model = $this->imageSyncFactory->create()->load($image->getId());
                        $model->setImageData(json_encode($response));

                        $this->imageSyncRepository->save($model);

                        $filterGroup1 = $this->filterGroupBuilder
                            ->addFilter(
                                $this->filterBuilder
                                    ->setField('image_data')
                                    ->setConditionType('eq')
                                    ->setValue('')
                                    ->create()
                            )
                            ->create();

                        $ids = [$product->getId()];

                        if ($product->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                            $children = $product->getTypeInstance()->getUsedProducts($product);
                            foreach ($children as $child) {
                                $ids[] = $child->getId();
                            }
                        } else {
                            foreach ($this->configurableProductType->getParentIdsByChild($product->getId()) as $parentId) {
                                $parent = $this->productRepository->getById($parentId);
                                $ids[] = $parent->getId();

                                $children = $parent->getTypeInstance()->getUsedProducts($parent);
                                foreach ($children as $child) {
                                    $ids[] = $child->getId();
                                }
                            }
                        }

                        $filterGroup2 = $this->filterGroupBuilder
                            ->addFilter(
                                $this->filterBuilder
                                    ->setField('product_id')
                                    ->setConditionType('in')
                                    ->setValue($ids)
                                    ->create()
                            )
                            ->create();

                        $this->searchCriteria->setFilterGroups([$filterGroup1, $filterGroup2]);

                        if (!$this->imageSyncRepository->getList($this->searchCriteria)->getTotalCount()) {
                            $walkTheChatId = $this->helper->getWalkTheChatAttributeValue($product);

                            if ($walkTheChatId) {
                                $this->addProductToQueue($product->getId(), $walkTheChatId);
                            } else {
                                foreach ($this->configurableProductType->getParentIdsByChild($product->getId()) as $parentId) {
                                    $parent = $this->productRepository->getById($parentId);
                                    $parentWalkTheChatId = $this->helper->getWalkTheChatAttributeValue($parent);

                                    if ($parentWalkTheChatId) {
                                        $this->addProductToQueue($parentId, $parentWalkTheChatId);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Magento\Framework\Exception\FileSystemException $fileSystemException) {
            throw new \Magento\Framework\Exception\CronException(
                __('Unable to lock the cron. Please check your "var" folder permissions.')
            );
        } catch (\Exception $exception) {
            echo $exception->getMessage();
            $this->logger->error(
                "WalkTheChat | Internal error occurred: {$exception->getMessage()}",
                $exception->getTrace()
            );
        } finally {
            $this->unlockCron($varDirectory);
        }
    }

    /**
     * Add product to queue
     *
     * @param int $productId
     * @param int $walkTheChatId
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    protected function addProductToQueue($productId, $walkTheChatId)
    {
        if (!$this->queueService->isDuplicate(
            $productId,
            \Walkthechat\Walkthechat\Model\Action\Update::ACTION,
            'product_id'
        )) {
            $model = $this->queueFactory->create();

            $model->setProductId($productId);
            $model->setWalkthechatId($walkTheChatId);
            $model->setAction(\Walkthechat\Walkthechat\Model\Action\Update::ACTION);

            $this->queueRepository->save($model);
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
        if ($folderManager->isExist(self::SYNC_IMAGES_LOCK_FILE_NAME_LOCKED)) {
            return true;
        }

        if (!$folderManager->isExist(self::SYNC_IMAGES_LOCK_FILE_NAME_UNLOCKED)) {
            $folderManager->writeFile(self::SYNC_IMAGES_LOCK_FILE_NAME_UNLOCKED, '');
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
            $folderManager->renameFile(self::SYNC_IMAGES_LOCK_FILE_NAME_UNLOCKED, self::SYNC_IMAGES_LOCK_FILE_NAME_LOCKED);
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
            $folderManager->renameFile(self::SYNC_IMAGES_LOCK_FILE_NAME_LOCKED, self::SYNC_IMAGES_LOCK_FILE_NAME_UNLOCKED);
        } catch (\Magento\Framework\Exception\FileSystemException $e) {
            throw new \Magento\Framework\Exception\CronException(__('Unable to unlock the walkthechat sync images.'));
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

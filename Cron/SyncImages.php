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
     * @var \Walkthechat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\ProductFactory
     */
    protected $resourceProduct;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $configurable;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

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
     * @param \Walkthechat\Walkthechat\Helper\Data $helper
     * @param \Magento\Catalog\Model\ResourceModel\ProductFactory $resourceProduct
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
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
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Magento\Catalog\Model\ResourceModel\ProductFactory $resourceProduct,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
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
        $this->helper                   = $helper;
        $this->resourceProduct          = $resourceProduct;
        $this->configurable             = $configurable;
        $this->productCollectionFactory  = $productCollectionFactory;
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

        if ($this->isCronLocked($varDirectory)) {
            return;
        }

        try {
            $isLocked = $this->lockCron($varDirectory);

            if ($isLocked) {
                $this->initAreaCode();

                $store = $this->helper->getStore();

                $filterGroup = $this->filterGroupBuilder
                    ->addFilter(
                        $this->filterBuilder
                            ->setField('image_data')
                            ->setConditionType('eq')
                            ->setValue('')
                            ->create()
                    )
                    ->create();

                $this->searchCriteria->setFilterGroups([$filterGroup])->setPageSize($this->helper->getImageQueueBatch());

                $images = $this->imageSyncRepository->getList($this->searchCriteria)->getItems();

                $productsIds = [];
                $urls = [];
                foreach ($images as $image) {
                    $productsIds[] = $image->getProductId();
                    $urls[] = $image->getImageUrl();
                }

                if (count($urls)) {
                    $productCollection = $this->productCollectionFactory->create();
                    $productCollection->addAttributeToSelect('walkthechat_id')
                        ->addFieldToFilter('entity_id', ['in' => $productsIds]);
                    
                    $products = [];
                    foreach ($productCollection as $product) {
                        $products[$product->getId()] = [
                            'sku'               => $product->getSku(),
                            'type_id'           => $product->getTypeId(),
                            'walkthechat_id'    => $product->getWalkthechatId()
                        ];
                    }
                    
                    unset($filterGroup, $productsIds, $productCollection);
                    
                    $serviceData = $this->imageService->addImages($urls);
                    
                    foreach ($images as $image) {
                        $i = array_search($image->getImageUrl(), $urls);
                        
                        if ($i !== false && isset($serviceData[$i])) {
                            $model = $this->imageSyncFactory->create()->load($image->getId());
                            $model->setImageData(json_encode($serviceData[$i]));
                            
                            $this->imageSyncRepository->save($model);
                            
                            unset($model);
                            
                            $ids = [$image->getProductId()];
                            
                            if ($products[$image->getProductId()]['type_id'] === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                                $children = $this->configurable->getChildrenIds($image->getProductId());
                                $ids = array_merge($ids, $children[0]);
                                
                                unset($children);
                            } else {
                                $parents = $this->configurable->getParentIdsByChild($image->getProductId());
                                
                                foreach ($parents as $parentId) {
                                    $children = $this->configurable->getChildrenIds($parentId);
                                    $children[0][] = $parentId;
                                    $ids = array_merge($ids, $children[0]);
                                    
                                    unset($children);
                                }
                                
                                unset($parents);
                            }
                            
                            $filterGroup1 = $this->filterGroupBuilder
                                ->addFilter(
                                    $this->filterBuilder
                                        ->setField('image_data')
                                        ->setConditionType('eq')
                                        ->setValue('')
                                        ->create()
                                )
                                ->create();
                            
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
                                if ($products[$image->getProductId()]['walkthechat_id']) {
                                    $this->addProductToQueue($image->getProductId(), $products[$image->getProductId()]['walkthechat_id']);
                                } else {
                                    $parents = $this->configurable->getParentIdsByChild($image->getProductId());
                                    
                                    foreach ($parents as $parentId) {
                                        $parentWalkTheChatId = $this->resourceProduct->create()->getAttributeRawValue($parentId, 'walkthechat_id', $store);
                                        
                                        if ($parentWalkTheChatId) {
                                            $this->addProductToQueue($parentId, $parentWalkTheChatId);
                                        }
                                        
                                        unset($parentWalkTheChatId);
                                    }
                                    
                                    unset($parents);
                                }
                            }
                            
                            unset($filterGroup1, $filterGroup2);
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

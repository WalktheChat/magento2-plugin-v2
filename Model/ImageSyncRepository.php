<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 Walkthechat
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model;

/**
 * Class ImageSyncRepository
 *
 * @package Walkthechat\Walkthechat\Model
 */
class ImageSyncRepository implements \Walkthechat\Walkthechat\Api\ImageSyncRepositoryInterface
{
    /**
     * @var \Walkthechat\Walkthechat\Model\ResourceModel\ImageSync\CollectionFactory
     */
    protected $imageSyncCollectionFactory;

    /**
     * @var \Walkthechat\Walkthechat\Api\Data\ImageSyncSearchResultsInterfaceFactory
     */
    protected $imageSyncSearchResultsInterfaceFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var \Walkthechat\Walkthechat\Model\ResourceModel\ImageSync
     */
    protected $imageSyncResource;

    /**
     * ImageSyncRepository constructor.
     *
     * @param \Walkthechat\Walkthechat\Model\ResourceModel\ImageSync\CollectionFactory $imageSyncCollectionFactory
     * @param \Walkthechat\Walkthechat\Api\Data\ImageSyncSearchResultsInterfaceFactory $imageSyncSearchResultsInterfaceFactory
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface   $collectionProcessor
     * @param \Walkthechat\Walkthechat\Model\ResourceModel\ImageSync                   $imageSyncResource
     */
    public function __construct(
        \Walkthechat\Walkthechat\Model\ResourceModel\ImageSync\CollectionFactory $imageSyncCollectionFactory,
        \Walkthechat\Walkthechat\Api\Data\ImageSyncSearchResultsInterfaceFactory $imageSyncSearchResultsInterfaceFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \Walkthechat\Walkthechat\Model\ResourceModel\ImageSync $imageSyncResource
    ) {
        $this->imageSyncCollectionFactory             = $imageSyncCollectionFactory;
        $this->imageSyncSearchResultsInterfaceFactory = $imageSyncSearchResultsInterfaceFactory;
        $this->collectionProcessor                    = $collectionProcessor;
        $this->imageSyncResource                      = $imageSyncResource;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Walkthechat\Walkthechat\Api\Data\ImageSyncInterface $imageSync)
    {
        try {
            $this->imageSyncResource->save($imageSync);
        } catch (\Magento\Framework\Exception\AlreadyExistsException $alreadyExistsException) {
            // if image is already exists in table then just ignore saving
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __('Could not save the image in image sync table: %1', $exception->getMessage()),
                $exception
            );
        }

        return $imageSync;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Walkthechat\Walkthechat\Model\ResourceModel\ImageSync\Collection $collection */
        $collection = $this->imageSyncCollectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var \Walkthechat\Walkthechat\Api\Data\ImageSyncSearchResultsInterface $searchResults */
        $searchResults = $this->imageSyncSearchResultsInterfaceFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByProductIds(array $productIds)
    {
        /** @var \Walkthechat\Walkthechat\Model\ResourceModel\ImageSync\Collection $collection */
        $collection = $this->imageSyncCollectionFactory->create();

        $collection->addFieldToFilter('product_id', ['in' => $productIds]);

        /** @var \Walkthechat\Walkthechat\Model\ImageSync $item */
        foreach ($collection as $item) {
            $this->imageSyncResource->delete($item);
        }

        return true;
    }
}

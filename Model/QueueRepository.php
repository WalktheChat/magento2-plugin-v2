<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model;

/**
 * Class QueueRepository
 *
 * @package Walkthechat\Walkthechat\Model
 */
class QueueRepository implements \Walkthechat\Walkthechat\Api\QueueRepositoryInterface
{
    /**
     * @var \Walkthechat\Walkthechat\Model\ResourceModel\Queue
     */
    protected $resource;

    /**
     * @var \Walkthechat\Walkthechat\Model\ResourceModel\Queue\CollectionFactory
     */
    protected $queueCollectionFactory;

    /**
     * @var \Walkthechat\Walkthechat\Api\Data\QueueSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var \Walkthechat\Walkthechat\Api\Data\QueueInterfaceFactory
     */
    protected $queueFactory;

    /**
     * QueueRepository constructor.
     *
     * @param \Walkthechat\Walkthechat\Model\ResourceModel\Queue                     $resource
     * @param \Walkthechat\Walkthechat\Model\ResourceModel\Queue\CollectionFactory   $queueCollectionFactory
     * @param \Walkthechat\Walkthechat\Api\Data\QueueSearchResultsInterfaceFactory   $searchResultsFactory
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
     * @param \Walkthechat\Walkthechat\Api\Data\QueueInterfaceFactory                $queueFactory
     */
    public function __construct(
        \Walkthechat\Walkthechat\Model\ResourceModel\Queue $resource,
        \Walkthechat\Walkthechat\Model\ResourceModel\Queue\CollectionFactory $queueCollectionFactory,
        \Walkthechat\Walkthechat\Api\Data\QueueSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \Walkthechat\Walkthechat\Api\Data\QueueInterfaceFactory $queueFactory
    ) {
        $this->resource               = $resource;
        $this->queueCollectionFactory = $queueCollectionFactory;
        $this->searchResultsFactory   = $searchResultsFactory;
        $this->collectionProcessor    = $collectionProcessor;
        $this->queueFactory           = $queueFactory;
    }

    /**
     * @param int $id
     *
     * @return \Walkthechat\Walkthechat\Api\Data\QueueInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id)
    {
        /** @var \Walkthechat\Walkthechat\Api\Data\QueueInterface $emptyModel */
        $emptyModel = $this->queueFactory->create();

        /** @var \Walkthechat\Walkthechat\Api\Data\QueueInterface $queue */
        $queue = $this->resource->load($emptyModel, $id);

        if (!$queue->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Queue with id "%1" does not exist.', $id));
        }

        return $queue;
    }

    /**
     * @param \Walkthechat\Walkthechat\Api\Data\QueueInterface $queue
     *
     * @return \Walkthechat\Walkthechat\Api\Data\QueueInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Walkthechat\Walkthechat\Api\Data\QueueInterface $queue)
    {
        try {
            $this->resource->save($queue);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __('Could not save the queue: %1', $exception->getMessage()),
                $exception
            );
        }

        return $queue;
    }

    /**
     * @param \Walkthechat\Walkthechat\Api\Data\QueueInterface $queue
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Walkthechat\Walkthechat\Api\Data\QueueInterface $queue)
    {
        try {
            $this->resource->delete($queue);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__('Could not delete the queue: %1',
                $exception->getMessage()));
        }

        return true;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     *
     * @return \Walkthechat\Walkthechat\Api\Data\QueueSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \Walkthechat\Walkthechat\Model\ResourceModel\Queue\Collection $collection */
        $collection = $this->queueCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var \Walkthechat\Walkthechat\Api\Data\QueueSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();

        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function bulkSave(array $data)
    {
        try {
            if (!$data) {
                return [];
            }

            $tableName = $this->resource->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\Queue::TABLE_NAME);

            return $this->resource->getConnection()->insertMultiple($tableName, $data);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__('Unable to save some items of queue'));
        }
    }
}

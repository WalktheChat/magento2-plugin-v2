<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model;

/**
 * Class InventoryRepository
 *
 * @package Walkthechat\Walkthechat\Model
 */
class InventoryRepository implements \Walkthechat\Walkthechat\Api\InventoryRepositoryInterface
{
    /**
     * @var \Walkthechat\Walkthechat\Model\ResourceModel\Inventory
     */
    protected $resource;

    /**
     * @var \Walkthechat\Walkthechat\Model\ResourceModel\Inventory\CollectionFactory
     */
    protected $inventoryCollectionFactory;

    /**
     * @var \Walkthechat\Walkthechat\Api\Data\InventorySearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * InventoryRepository constructor.
     *
     * @param \Walkthechat\Walkthechat\Model\ResourceModel\Inventory $resource
     * @param \Walkthechat\Walkthechat\Model\ResourceModel\Inventory\CollectionFactory   $inventoryCollectionFactory
     * @param \Walkthechat\Walkthechat\Api\Data\InventorySearchResultsInterfaceFactory   $searchResultsFactory
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        \Walkthechat\Walkthechat\Model\ResourceModel\Inventory $resource,
        \Walkthechat\Walkthechat\Model\ResourceModel\Inventory\CollectionFactory $inventoryCollectionFactory,
        \Walkthechat\Walkthechat\Api\Data\InventorySearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->inventoryCollectionFactory = $inventoryCollectionFactory;
        $this->searchResultsFactory   = $searchResultsFactory;
        $this->collectionProcessor    = $collectionProcessor;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     *
     * @return \Walkthechat\Walkthechat\Api\Data\InventorySearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \Walkthechat\Walkthechat\Model\ResourceModel\Inventory\Collection $collection */
        $collection = $this->inventoryCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var \Walkthechat\Walkthechat\Api\Data\InventorySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();

        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function truncate()
    {
        $tableName = $this->resource->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\Inventory::TABLE_NAME);

        $this->resource->getConnection()->truncateTable($tableName);
    }

    /**
     * {@inheritdoc}
     */
    public function bulkSave(array $data)
    {
        if ($data) {
            try {
                $tableName = $this->resource->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\Inventory::TABLE_NAME);
                $this->resource->getConnection()->insertMultiple($tableName, $data);
            } catch (\Exception $exception) {
                throw new \Magento\Framework\Exception\CouldNotSaveException(__('Unable to save some items of inventory'));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function bulkDelete(array $data)
    {
        if ($data) {
            $tableName = $this->resource->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\Inventory::TABLE_NAME);
            $conditions = $this->resource->getConnection()->quoteInto('entity_id IN (?)', $data);
            $this->resource->getConnection()->delete($tableName, $conditions);
        }
    }
}

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
 * Class ProductRepository
 *
 * @package Walkthechat\Walkthechat\Model
 */
class ProductRepository implements \Walkthechat\Walkthechat\Api\ProductRepositoryInterface
{
    /**
     * @var \Walkthechat\Walkthechat\Model\ResourceModel\Product
     */
    protected $resource;

    /**
     * @var \Walkthechat\Walkthechat\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Walkthechat\Walkthechat\Api\Data\ProductSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var \Walkthechat\Walkthechat\Api\Data\ProductInterfaceFactory
     */
    protected $productFactory;

    /**
     * ProductRepository constructor.
     *
     * @param \Walkthechat\Walkthechat\Model\ResourceModel\Product                     $resource
     * @param \Walkthechat\Walkthechat\Model\ResourceModel\Product\CollectionFactory   $productCollectionFactory
     * @param \Walkthechat\Walkthechat\Api\Data\ProductSearchResultsInterfaceFactory   $searchResultsFactory
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface 	   $collectionProcessor
     * @param \Walkthechat\Walkthechat\Api\Data\ProductInterfaceFactory                $queueFactory
     */
    public function __construct(
        \Walkthechat\Walkthechat\Model\ResourceModel\Product $resource,
        \Walkthechat\Walkthechat\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Walkthechat\Walkthechat\Api\Data\ProductSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \Walkthechat\Walkthechat\Api\Data\ProductInterfaceFactory $productFactory
    ) {
        $this->resource                 = $resource;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->searchResultsFactory     = $searchResultsFactory;
        $this->collectionProcessor      = $collectionProcessor;
        $this->productFactory           = $productFactory;
    }

    /**
     * @param int $id
     *
     * @return \Walkthechat\Walkthechat\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id)
    {
        /** @var \Walkthechat\Walkthechat\Api\Data\ProductInterface $emptyModel */
        $emptyModel = $this->productFactory->create();

        /** @var \Walkthechat\Walkthechat\Api\Data\ProductInterface $queue */
        $product = $this->resource->load($emptyModel, $id);

        if (!$product->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Product with id "%1" does not exist.', $id));
        }

        return $product;
    }
	
	/**
     * @param int $id
     *
     * @return \Walkthechat\Walkthechat\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByProductId($id)
    {
        $product = $this->productFactory->create();
        $product->load($id, 'product_id');

        if (!$product->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Product with product_id "%1" does not exist.', $id));
        }

        return $product;
    }

    /**
     * @param \Walkthechat\Walkthechat\Api\Data\ProductInterface $product
     *
     * @return \Walkthechat\Walkthechat\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Walkthechat\Walkthechat\Api\Data\ProductInterface $product)
    {
        try {
            $this->resource->save($product);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __('Could not save the product: %1', $exception->getMessage()),
                $exception
            );
        }

        return $product;
    }

    /**
     * @param \Walkthechat\Walkthechat\Api\Data\ProductInterface $queue
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Walkthechat\Walkthechat\Api\Data\ProductInterface $product)
    {
        try {
            $this->resource->delete($product);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__('Could not delete the product: %1',
                $exception->getMessage()));
        }

        return true;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     *
     * @return \Walkthechat\Walkthechat\Api\Data\ProductSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \Walkthechat\Walkthechat\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->productCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var \Walkthechat\Walkthechat\Api\Data\ProductSearchResultsInterface $searchResults */
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

            $tableName = $this->resource->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\Product::TABLE_NAME);

            return $this->resource->getConnection()->insertMultiple($tableName, $data);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__('Unable to save some items of product'));
        }
    }
}

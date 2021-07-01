<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model\ResourceModel\Product\Grid;

/**
 * Class Collection
 *
 * @package Walkthechat\Walkthechat\Model\ResourceModel\Product\Grid
 */
class Collection extends \Walkthechat\Walkthechat\Model\ResourceModel\Product\Collection
    implements \Magento\Framework\Api\Search\SearchResultInterface
{
    /**
     * @var \Magento\Framework\Api\Search\AggregationInterface
     */
    protected $aggregations;

    /**
     * @var mixed|null
     */
    protected $mainTable;

    /**
     * @var mixed
     */
    protected $resourceModel;

    /**
     * @var string
     */
    protected $model;
    
    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * {@inheritdoc}
     *
     * @param \Magento\Eav\Api\AttributeRepositoryInterface        $attributeRepository
     * @param mixed|null                                           $mainTable
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $eventPrefix
     * @param mixed                                                $eventObject
     * @param mixed                                                $resourceModel
     * @param string                                               $model
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->_eventPrefix  = $eventPrefix;
        $this->_eventObject  = $eventObject;
        $this->mainTable     = $mainTable;
        $this->resourceModel = $resourceModel;
        $this->model         = $model;

        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_init($this->model, $this->resourceModel);
        $this->setMainTable($this->mainTable);
    }

    /**
     * @return \Magento\Framework\Api\Search\AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param \Magento\Framework\Api\Search\AggregationInterface $aggregations
     *
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;

        return $this;
    }

    /**
     * Retrieve all ids for collection
     * Backward compatibility with EAV collection
     *
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }
	
	protected function _initSelect()
	{
		parent::_initSelect();
		
		$attrId = $this->attributeRepository->get('catalog_product', 'name')->getId();
		
		$this->getSelect()
            ->joinLeft(
                ['catalog_product_entity' => $this->getTable('catalog_product_entity')],
                'main_table.product_id = catalog_product_entity.entity_id',
                ['sku']
            )
            ->joinLeft(
                ['catalog_product_entity_varchar' => $this->getTable('catalog_product_entity_varchar')],
                'main_table.product_id = catalog_product_entity_varchar.entity_id',
                ['product_name' => 'value']
            )
            ->where('catalog_product_entity_varchar.store_id = 0')
            ->where('catalog_product_entity_varchar.attribute_id = ' . $attrId);
		
		$this->addFilterToMap('entity_id', 'main_table.entity_id');
		$this->addFilterToMap('sku', 'catalog_product_entity.sku');
		$this->addFilterToMap('product_name', 'catalog_product_entity_varchar.value');
		
		return $this;
	}
}

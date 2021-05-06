<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Ui\DataProvider;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\Store;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Catalog\Model\Product\Visibility ;

/**
 * Class ProductExportDataProvider
 * @package Walkthechat\Walkthechat\Ui\DataProvider
 */
class ProductExportDataProvider extends \Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider
{
    /**
     * @var Visibility
     */
    protected $productVisibility;

    /**
     * @var \Walkthechat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * ProductExportDataProvider constructor.
     * @param $name
     * @param $primaryFieldName
     * @param $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param Visibility $productVisibility
     * @param \Walkthechat\Walkthechat\Helper\Data $helper
     * @param array $addFieldStrategies
     * @param array $addFilterStrategies
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $modifiersPool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        Visibility $productVisibility,
        \Walkthechat\Walkthechat\Helper\Data $helper,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = [],
        PoolInterface $modifiersPool = null
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $collectionFactory, $addFieldStrategies, $addFilterStrategies, $meta, $data, $modifiersPool);

        $this->productVisibility    = $productVisibility;
        $this->helper               = $helper;

        $store = $this->helper->getStore();

        $this->collection->setStoreId($store->getId())
            ->addAttributeToFilter('type_id', ['in' => [\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE, \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE]])
            ->addAttributeToFilter(
                [
                    [
                        'attribute' => \Walkthechat\Walkthechat\Helper\Data::ATTRIBUTE_CODE,
                        ['null' => true]
                    ],
                    [
                        'attribute' => \Walkthechat\Walkthechat\Helper\Data::ATTRIBUTE_CODE,
                        'eq' => ''
                    ]
                ],
                null,
                'left'
            )
            ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
            ->setVisibility($this->productVisibility->getVisibleInSiteIds())
            ->addStoreFilter($store);

        $this->collection->getSelect()
            ->joinLeft(
                ['link_table' => $this->collection->getResource()->getTable('catalog_product_super_link')],
                'link_table.product_id = e.entity_id',
                ['product_id']
            )
            ->where('link_table.product_id IS NULL');
    }
}

<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model;

/**
 * Class ProductService
 *
 * @package Walkthechat\Walkthechat\Model
 */
class ProductService
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItem
     */
    protected $stockItem;

    /**
     * @var \Walkthechat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * @var \Walkthechat\Walkthechat\Model\QueueService
     */
    protected $queueService;

    /**
     * @var \Walkthechat\Walkthechat\Api\QueueRepositoryInterface
     */
    protected $queueRepository;

    /**
     * @var \Magento\CatalogRule\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $configurable;

    /**
     * ProductService constructor.
     *
     * @param \Magento\Catalog\Model\ProductRepository                      $productRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder                  $searchCriteriaBuilder
     * @param \Walkthechat\Walkthechat\Helper\Data                          $helper
     * @param \Magento\CatalogInventory\Api\StockStateInterface             $stockItem
     * @param \Walkthechat\Walkthechat\Model\QueueService                   $queueService
     * @param \Walkthechat\Walkthechat\Api\QueueRepositoryInterface         $queueRepository
     * @param \Magento\CatalogRule\Model\RuleFactory                        $ruleFactory
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable  $configurable
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Magento\CatalogInventory\Api\StockStateInterface $stockItem,
        \Walkthechat\Walkthechat\Model\QueueService $queueService,
        \Walkthechat\Walkthechat\Api\QueueRepositoryInterface $queueRepository,
        \Magento\CatalogRule\Model\RuleFactory $ruleFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
    ) {
        $this->productRepository     = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->stockItem             = $stockItem;
        $this->helper                = $helper;
        $this->queueService          = $queueService;
        $this->queueRepository       = $queueRepository;
        $this->ruleFactory           = $ruleFactory;
        $this->configurable          = $configurable;
    }

    /**
     * Get All Synced Products
     *
     * @return mixed
     */
    public function getSyncedProducts()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(
                \Walkthechat\Walkthechat\Helper\Data::ATTRIBUTE_CODE,
                '',
                'neq'
            )
            ->create();

        return $this->productRepository->getList($searchCriteria);
    }

    /**
     * Get all products available for export
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getAllForExport()
    {
        $configurableProductsSearchCriteria = $this
            ->searchCriteriaBuilder
            ->addFilter('type_id', \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE)
            ->create();

        $configurableProducts = $this->productRepository->getList($configurableProductsSearchCriteria);

        $ignoreSimpleIds = [];

        foreach ($configurableProducts->getItems() as $configurableProduct) {
            foreach ($configurableProduct->getTypeInstance()->getUsedProducts($configurableProduct) as $child) {
                $ignoreSimpleIds[] = $child->getId();
            }
        }

        array_unique($ignoreSimpleIds);

        $simpleProductsSearchCriteria = $this
            ->searchCriteriaBuilder
            ->addFilter(
                'type_id',
                [
                    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
                    \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
                ],
                'in'
            )
            ->addFilter('entity_id', $ignoreSimpleIds, 'nin')
            ->create();

        $simpleProducts = $this->productRepository->getList($simpleProductsSearchCriteria);

        return array_merge($simpleProducts->getItems(), $configurableProducts->getItems());
    }

    /**
     * Process passed products
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface[]     $products
     * @param \Magento\Framework\Message\ManagerInterface|null $messageManager
     *
     * @return array
     */
    public function processProductsExport(
        array $products,
        \Magento\Framework\Message\ManagerInterface $messageManager = null
    ) {
        $productsProceed = 0;
        $bulkData        = [];

        foreach ($products as $product) {
            $isSupportedProductType = in_array($product->getTypeId(), [
                \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
                \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
                \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE,
            ]);

            if (!$isSupportedProductType) {
                ++$productsProceed;

                continue;
            }

            $walkTheChatAttributeValue = $this->helper->getWalkTheChatAttributeValue($product);

            if ($walkTheChatAttributeValue) {
                // don't add to queue twice when exporting
                if (!$this->queueService->isDuplicate(
                    $product->getId(),
                    \Walkthechat\Walkthechat\Model\Action\Update::ACTION,
                    'product_id'
                )
                ) {
                    $bulkData[] = [
                        'product_id' => $product->getId(),
                        'walkthechat_id' => $walkTheChatAttributeValue,
                        'action'     => \Walkthechat\Walkthechat\Model\Action\Update::ACTION,
                    ];
                }
            } else {
                // don't add to queue twice when exporting
                if (!$this->queueService->isDuplicate(
                    $product->getId(),
                    \Walkthechat\Walkthechat\Model\Action\Add::ACTION,
                    'product_id'
                )
                ) {
                    $bulkData[] = [
                        'product_id' => $product->getId(),
                        'action'     => \Walkthechat\Walkthechat\Model\Action\Add::ACTION,
                    ];
                }
            }
        }

        try {
            $this->queueRepository->bulkSave($bulkData);
        } catch (\Magento\Framework\Exception\CouldNotSaveException $exception) {
            if (null !== $messageManager) {
                $messageManager->addWarningMessage(
                    __('Error occurred when tried to added products to the queue. Please retry again or contact the administrator.')
                );
            }
        }

        if ($productsProceed && null !== $messageManager) {
            $messageManager->addWarningMessage(
                __(
                    '%1 product(s) can not be exported. Supported product types: Simple, Virtual and Configurable',
                    $productsProceed
                )
            );
        }

        return $bulkData;
    }

    /**
     * Process product delete action
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface[]     $products
     * @param \Magento\Framework\Message\ManagerInterface|null $messageManager
     *
     * @return array
     */
    public function processProductDelete(
        array $products,
        \Magento\Framework\Message\ManagerInterface $messageManager = null
    ) {
        $bulkData = [];

        foreach ($products as $product) {
            $walkTheChatAttributeValue = $this->helper->getWalkTheChatAttributeValue($product);

            if ($walkTheChatAttributeValue
                && !$this->queueService->isDuplicate(
                    $product->getId(),
                    \Walkthechat\Walkthechat\Model\Action\Delete::ACTION,
                    'product_id'
                )) {
                $bulkData[] = [
                    'product_id'     => $product->getId(),
                    'walkthechat_id' => $walkTheChatAttributeValue,
                    'action'         => \Walkthechat\Walkthechat\Model\Action\Delete::ACTION,
                ];
            }
        }

        try {
            $this->queueRepository->bulkSave($bulkData);
        } catch (\Magento\Framework\Exception\CouldNotSaveException $exception) {
            if (null !== $messageManager) {
                $messageManager->addWarningMessage(
                    __('Error occurred when tried to added products to the queue. Please retry again or contact the administrator.')
                );
            }
        }

        return $bulkData;
    }

    /**
     * Get all products available for delete
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getAllForDelete()
    {
        $allSynchronizedProductsSearchCriteria = $this
            ->searchCriteriaBuilder
            ->addFilter('walkthechat_id', true, 'notnull')
            ->create();

        return $this->productRepository->getList($allSynchronizedProductsSearchCriteria)->getItems();
    }

    /**
     * Prepare product data for API
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param bool                           $isNew
     * @param array                          $imagesData
     * @param array                          $mediaContentData
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function prepareProductData($product, $isNew = true, array $imagesData = [], array $mediaContentData = [])
    {
        $rule =  $this->ruleFactory->create();

        $mainPrice        = $this->helper->convertPrice($product->getPrice());
        $mainSpecialPrice = null;
        if (!$product->getSpecialFromDate() && !$product->getSpecialToDate()) {
            $mainSpecialPrice = $this->helper->convertPrice($product->getSpecialPrice());
        }
        $mainRulePrice = $this->helper->convertPrice($rule->calcProductPriceRule($product, $product->getPrice()));

        if (($mainRulePrice && !$mainSpecialPrice) || ($mainRulePrice && $mainRulePrice < $mainSpecialPrice)) {
            $mainSpecialPrice = $mainRulePrice;
        }

        if ($mainPrice == $mainSpecialPrice) {
            $mainSpecialPrice = null;
        }

        $productVisibility =
            $product->getVisibility() != \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE
            && !$product->isDisabled();

        $data = [
            'manageInventory'       => true,
            'visibility'            => $productVisibility,
            'displayPrice'          => $mainSpecialPrice ? $mainSpecialPrice : $mainPrice,
            'displayCompareAtPrice' => $mainSpecialPrice ? $mainPrice : null,
            'images'                => $imagesData['main'] ?? [],
            'variants'              => []
        ];

        // if is "update" action - don't update the title and description
        if ($isNew) {
            $data['title'] = [
                'en' => $product->getName(),
            ];

            $data['bodyHtml'] = [
                'en' => isset($mediaContentData['content']) && $mediaContentData['content'] ? $mediaContentData['content'] : $product->getDescription(),
            ];
        }

        if ($product->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $configurableOptions = $product->getTypeInstance()->getConfigurableOptions($product);

            $data['variantOptions'] = [];

            // send all available options in configurable product
            foreach ($configurableOptions as $option) {
                foreach ($option as $variation) {
                    $data['variantOptions'][] = $variation['attribute_code'];

                    break;
                }
            }

            /** @var \Magento\Catalog\Model\Product[] $children */
            $children = $product->getTypeInstance()->getUsedProducts($product);

            if ($children) {
                $k = 0;
                foreach ($children as $child) {
                    $childPrice			= $this->helper->convertPrice($child->getPrice());
                    $childSpecialPrice = null;
                    if (!$child->getSpecialFromDate() && !$child->getSpecialToDate()) {
                        $childSpecialPrice = $this->helper->convertPrice($child->getSpecialPrice());
                    }
                    $childRulePrice = $this->helper->convertPrice($rule->calcProductPriceRule($child, $child->getPrice()));

                    if (($childRulePrice && !$childSpecialPrice) || ($childRulePrice && $childRulePrice < $childSpecialPrice)) {
                        $childSpecialPrice = $childRulePrice;
                    }

                    if ($childPrice == $childSpecialPrice) {
                        $childSpecialPrice = null;
                    }

                    $data['variants'][$k] = [
                        'id'                => $child->getId(),
                        'inventoryQuantity' => $this->stockItem->getStockQty($child->getId()),
                        'weight'            => $child->getWeight(),
                        'requiresShipping'  => true,
                        'sku'               => $child->getSku(),
                        'price'             => $childSpecialPrice ? $childSpecialPrice : $childPrice,
                        'compareAtPrice'    => $childSpecialPrice ? $childPrice : null,
                        'visibility'        => $child->isDisabled() ? false : true,
                        'taxable'           => (bool)$child->getTaxClassId(),
                    ];

                    $imageData = $imagesData['children'][$child->getId()] ?? [];

                    // add images to each variant
                    if ($imageData && is_array($imageData)) {
                        $data['variants'][$k]['images'] = $imageData;
                    }

                    // if is "update" action - don't update the title
                    if ($isNew) {
                        $data['variants'][$k]['title'] = [
                            'en' => $child->getName(),
                        ];
                    }

                    // add available options for current variant
                    foreach ($data['variantOptions'] as $n => $attributeCode) {
                        $data['variants'][$k]['options'][] = [
                            'id'       => $attributeCode,
                            'name'     => [
                                'en' => $child->getResource()->getAttribute($attributeCode)->getFrontend()->getLabel($child),
                            ],
                            'position' => $n,
                            'value'    => [
                                'en' => $child->getAttributeText($attributeCode),
                            ],
                        ];
                    }

                    ++$k;
                }
            }
        } else {
            $variant = [
                'id'                => $product->getId(),
                'inventoryQuantity' => $this->stockItem->getStockQty($product->getId()),
                'weight'            => $product->getWeight(),
                'requiresShipping'  => true,
                'sku'               => $product->getSku(),
                'price'             => $mainSpecialPrice ? $mainSpecialPrice : $mainPrice,
                'compareAtPrice'    => $mainSpecialPrice ? $mainPrice : null,
                'visibility'        => $productVisibility,
                'taxable'           => (bool)$product->getTaxClassId(),
            ];

            if ($isNew) {
                $variant['title'] = ['en' => $product->getName()];
            }

            $data['variants'][] = $variant;
        }

        return $data;
    }

    /**
     * @param $productSku
     * @param $stockId
     * @return int
     */
    private function _getProductSaleableQty($productSku, $stockId)
    {
        try{
            $qty = $this->getProductSalableQty->execute($productSku, $stockId);
        } catch(Exception $exception){
            $qty = 0;
        }
        return $qty;
    }

    /**
     * Prepare inventory data
     *
     * @return array
     */
    public function prepareInventoryData()
    {
        $data = [];
        $products = $this->getSyncedProducts();

        foreach ($products->getItems() as $product) {
            $walkthechatId = $this->helper->getWalkTheChatAttributeValue($product);

            if ($product->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $children = $this->configurable->getChildrenIds($product->getId());
                foreach ($children[0] as $id) {
                    $data[] = [
                        'product_id' => $id,
                        'walkthechat_id' => $walkthechatId,
                        'qty' => $this->stockItem->getStockQty($id)
                    ];
                }
            } else {
                $data[] = [
                    'product_id' => $product->getId(),
                    'walkthechat_id' => $walkthechatId,
                    'qty' => $this->stockItem->getStockQty($product->getId())
                ];
            }
        }

        return $data;
    }
}

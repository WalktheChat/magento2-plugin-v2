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
    private $queueService;
    /**
     * @var \Walkthechat\Walkthechat\Api\QueueRepositoryInterface
     */
    private $queueRepository;

    /**
     * ProductService constructor.
     *
     * @param \Magento\Catalog\Model\ProductRepository                  $productRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder              $searchCriteriaBuilder
     * @param \Walkthechat\Walkthechat\Helper\Data                          $helper
     * @param \Magento\CatalogInventory\Api\StockStateInterface         $stockItem
     * @param \Walkthechat\Walkthechat\Model\QueueService                   $queueService
     * @param \Walkthechat\Walkthechat\Api\QueueRepositoryInterface         $queueRepository
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Magento\CatalogInventory\Api\StockStateInterface $stockItem,
        \Walkthechat\Walkthechat\Model\QueueService $queueService,
        \Walkthechat\Walkthechat\Api\QueueRepositoryInterface $queueRepository
    ) {
        $this->productRepository     = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->stockItem             = $stockItem;
        $this->helper                = $helper;
        $this->queueService          = $queueService;
        $this->queueRepository       = $queueRepository;
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
            $walkTheChatAttributeValue = $this->helper->getWalkTheChatAttributeValue($product);

            // temporary solution (null filter doesn't work for custom attributes)
            if (!$walkTheChatAttributeValue) {
                $isSupportedProductType = in_array($product->getTypeId(), [
                    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
                    \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
                    \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE,
                ]);

                if (!$isSupportedProductType) {
                    ++$productsProceed;

                    continue;
                }

                // don't add to queue twice when exporting
                if (!$this->queueService->isDuplicate(
                    $product->getId(),
                    \Walkthechat\Walkthechat\Model\Action\Add::ACTION,
                    'product_id'
                )) {
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
        $mainPrice        = $this->helper->convertPrice($product->getPrice());
        $mainSpecialPrice = $this->helper->convertPrice($product->getSpecialPrice());

        $productVisibility =
            $product->getVisibility() != \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE
            && !$product->isDisabled();

        $data = [
            'manageInventory'       => true,
            'visibility'            => $productVisibility,
            'displayPrice'          => $mainSpecialPrice ? $mainSpecialPrice : $mainPrice,
            'displayCompareAtPrice' => $mainSpecialPrice ? $mainPrice : null,
            'images'                => $imagesData['main'] ?? [],
            'variants'              => [
                [
                    'id'                => $product->getId(),
                    'inventoryQuantity' => $this->stockItem->getStockQty($product->getId()),
                    'weight'            => $product->getWeight(),
                    'requiresShipping'  => true,
                    'sku'               => $product->getSku(),
                    'price'             => $mainSpecialPrice ? $mainSpecialPrice : $mainPrice,
                    'compareAtPrice'    => $mainSpecialPrice ? $mainPrice : null,
                    'visibility'        => $productVisibility,
                    'taxable'           => (bool)$product->getTaxClassId(),
                ],
            ],
        ];

        // if is "update" action - don't update the title and description
        if ($isNew) {
            $data['title'] = [
                'en' => $product->getName(),
            ];

            $data['bodyHtml'] = [
                'en' => isset($mediaContentData['content']) && $mediaContentData['content'] ? $mediaContentData['content'] : $product->getDescription(),
            ];

            $data['variants'][0]['title'] = [
                'en' => $product->getName(),
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
                // if product is configurable - remove main product variant
                $data['variants'] = [];

                $k = 0;
                foreach ($children as $child) {
                    if ($child->isDisabled()) {
                        continue;
                    }
					
					$childPrice			= $this->helper->convertPrice($child->getPrice());
					$childSpecialPrice = $this->helper->convertPrice($child->getSpecialPrice());

                    $data['variants'][$k] = [
                        'id'                => $child->getId(),
                        'inventoryQuantity' => $this->stockItem->getStockQty($child->getId()),
                        'weight'            => $child->getWeight(),
                        'requiresShipping'  => true,
                        'sku'               => $child->getSku(),
                        'price'             => $childSpecialPrice ? $childSpecialPrice : $childPrice,
						'compareAtPrice'    => $childSpecialPrice ? $childPrice : null,
                        'visibility'        => $child->getVisibility() != \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE,
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
        }

        return $data;
    }
}

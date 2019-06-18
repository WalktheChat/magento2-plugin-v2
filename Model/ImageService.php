<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 Walkthechat
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model;

/**
 * Class ImageService
 *
 * @package Walkthechat\Walkthechat\Model
 */
class ImageService
{
    /**
     * @var \Walkthechat\Walkthechat\Service\ImagesRepository
     */
    protected $requestImagesRepository;

    /**
     * @var \Walkthechat\Walkthechat\Api\ImageSyncRepositoryInterface
     */
    protected $imageSyncRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    protected $searchCriteria;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroup
     */
    protected $filterGroup;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * ImageService constructor.
     *
     * @param \Walkthechat\Walkthechat\Service\ImagesRepository           $requestImagesRepository
     * @param \Walkthechat\Walkthechat\Api\ImageSyncRepositoryInterface   $imageSyncRepository
     * @param \Magento\Framework\Api\SearchCriteriaInterface          $searchCriteria
     * @param \Magento\Framework\Api\Search\FilterGroup               $filterGroup
     * @param \Magento\Framework\Api\FilterBuilder                    $filterBuilder
     */
    public function __construct(
        \Walkthechat\Walkthechat\Service\ImagesRepository $requestImagesRepository,
        \Walkthechat\Walkthechat\Api\ImageSyncRepositoryInterface $imageSyncRepository,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Api\FilterBuilder $filterBuilder
    ) {
        $this->requestImagesRepository = $requestImagesRepository;
        $this->imageSyncRepository     = $imageSyncRepository;
        $this->searchCriteria          = $searchCriteria;
        $this->filterGroup             = $filterGroup;
        $this->filterBuilder           = $filterBuilder;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     *
     * @return array
     * @throws \Zend_Http_Client_Exception
     */
    public function addImages(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        /** @var \Magento\Catalog\Model\Product $product */

        $imagesData = [
            'main'           => [],
            'children'       => [],
            '_syncImageData' => [],
        ];

        foreach ($product->getMediaGalleryImages() as $galleryImage) {
            $response = $this->requestImagesRepository->create($galleryImage->getPath());

            if (is_array($response) && isset($response[0])) {
                $imagesData['main'][] = $response[0];

                $imagesData['_syncImageData'][] = [
                    'product_id' => $product->getId(),
                    'image_id'   => $galleryImage->getId(),
                    'image_data' => json_encode($response[0]),
                ];
            }
        }

        if ($product->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            /** @var \Magento\Catalog\Model\Product[] $children */
            $children = $product->getTypeInstance()->getUsedProducts($product);

            if ($children) {
                foreach ($children as $child) {
                    foreach ($child->getMediaGalleryImages() as $galleryImage) {
                        $response = $this->requestImagesRepository->create($galleryImage->getPath());

                        if (is_array($response) && isset($response[0])) {
                            $imagesData['children'][$child->getId()][] = $response[0];

                            $imagesData['_syncImageData'][] = [
                                'product_id' => $child->getId(),
                                'image_id'   => $galleryImage->getId(),
                                'image_data' => json_encode($response[0]),
                            ];
                        }
                    }
                }
            }
        }

        return $imagesData;
    }

    /**
     * Update images in product
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $mainProduct
     *
     * @return array
     * @throws \Zend_Http_Client_Exception
     */
    public function updateImages(\Magento\Catalog\Api\Data\ProductInterface $mainProduct)
    {
        /** @var \Magento\Catalog\Model\Product[] $products */
        $products = [$mainProduct];

        if ($mainProduct->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            /** @var \Magento\Catalog\Model\Product[] $children */
            $children   = $mainProduct->getTypeInstance()->getUsedProducts($mainProduct);
            $productIds = [$mainProduct->getId()];

            foreach ($children as $child) {
                $products[]   = $child;
                $productIds[] = $child->getId();
            }

            if (!$productIds) {
                return [];
            }

            $this->filterGroup->setFilters([
                $this->filterBuilder
                    ->setField('product_id')
                    ->setConditionType('in')
                    ->setValue($productIds)
                    ->create(),
            ]);
        } else {
            $this->filterGroup->setFilters([
                $this->filterBuilder
                    ->setField('product_id')
                    ->setConditionType('eq')
                    ->setValue($mainProduct->getId())
                    ->create(),
            ]);
        }

        $this->searchCriteria->setFilterGroups([$this->filterGroup]);

        $savedImages = $this
            ->imageSyncRepository
            ->getList($this->searchCriteria)
            ->getItems();

        $imagesData = [
            'main'           => [],
            'children'       => [],
            '_syncImageData' => [],
        ];

        // if product is configurable then first will be parent and others are children
        foreach ($products as $k => $product) {
            $isMainProduct = !$k;

            foreach ($product->getMediaGalleryImages() as $productGalleryImage) {
                $isFound = false;

                // check if product image exist in saved images
                foreach ($savedImages as $image) {
                    if ($productGalleryImage->getId() == $image->getImageId()) {
                        $isFound = true;

                        $savedImageData = json_decode($image->getImageData(), true);

                        if ($isMainProduct) {
                            $imagesData['main'][] = $savedImageData;
                        } else {
                            $imagesData['children'][$product->getId()][] = $savedImageData;
                        }

                        break;
                    }
                }

                // upload product image to CDN if wasn't found in saved images
                if (!$isFound) {
                    $response = $this->requestImagesRepository->create($productGalleryImage->getPath());

                    if (is_array($response) && isset($response[0])) {
                        if ($isMainProduct) {
                            $imagesData['main'][] = $response[0];
                        } else {
                            $imagesData['children'][$product->getId()][] = $response[0];
                        }

                        $imagesData['_syncImageData'][] = [
                            'product_id' => $product->getId(),
                            'image_id'   => $productGalleryImage->getId(),
                            'image_data' => json_encode($response[0]),
                        ];
                    }
                }
            }
        }

        return $imagesData;
    }
}

<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    WalktheChat <info@walkthechat.com>
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
     * @var \Walkthechat\Walkthechat\Api\ContentMediaRepositoryInterface
     */
    protected $contentMediaRepository;

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
     * @var \Walkthechat\Walkthechat\Model\Template\Filter
     */
    protected $filter;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Catalog\Model\Product\Gallery\GalleryManagement
     */
    protected $galleryManagement;

    /**
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    protected $catalogProductMediaConfig;

    /**
     * ImageService constructor.
     * @param \Walkthechat\Walkthechat\Service\ImagesRepository $requestImagesRepository
     * @param \Walkthechat\Walkthechat\Api\ImageSyncRepositoryInterface $imageSyncRepository
     * @param \Walkthechat\Walkthechat\Api\ContentMediaRepositoryInterface $contentMediaRepository
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param Template\Filter $filter
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Catalog\Model\Product\Gallery\GalleryManagement $galleryManagement
     * @param \Magento\Catalog\Model\Product\Media\Config $catalogProductMediaConfig
     */
    public function __construct(
        \Walkthechat\Walkthechat\Service\ImagesRepository $requestImagesRepository,
        \Walkthechat\Walkthechat\Api\ImageSyncRepositoryInterface $imageSyncRepository,
        \Walkthechat\Walkthechat\Api\ContentMediaRepositoryInterface $contentMediaRepository,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Walkthechat\Walkthechat\Model\Template\Filter $filter,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Catalog\Model\Product\Gallery\GalleryManagement $galleryManagement,
        \Magento\Catalog\Model\Product\Media\Config $catalogProductMediaConfig
    ) {
        $this->requestImagesRepository = $requestImagesRepository;
        $this->imageSyncRepository     = $imageSyncRepository;
        $this->contentMediaRepository  = $contentMediaRepository;
        $this->searchCriteria          = $searchCriteria;
        $this->filterGroup             = $filterGroup;
        $this->filterBuilder           = $filterBuilder;
        $this->filter                  = $filter;
        $this->filesystem              = $filesystem;
        $this->galleryManagement       = $galleryManagement;
        $this->catalogProductMediaConfig = $catalogProductMediaConfig;
    }

    /**
     * @return mixed
     */
    public function getSyncedImages()
    {
        $this->filterGroup->setFilters([
            $this->filterBuilder
                ->setField('image_data')
                ->setConditionType('neq')
                ->setValue('')
                ->create(),
        ]);

        $this->searchCriteria->setFilterGroups([$this->filterGroup]);

        return $this->imageSyncRepository
            ->getList($this->searchCriteria);
    }

    /**
     * @return mixed
     */
    public function getExportedImages()
    {
        $this->searchCriteria->setFilterGroups([]);

        return $this->imageSyncRepository
            ->getList($this->searchCriteria);
    }

    /**
     * Add image to wtc
     *
     * @param string $sku
     * @param int $id
     * @return bool|mixed
     */
    public function addImage(string $sku, int $id)
    {
        $galleryImage = $this->galleryManagement->get($sku, $id);
        $directory = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);

        $response = $this->requestImagesRepository->create($directory->getAbsolutePath($this->catalogProductMediaConfig->getMediaPath($galleryImage->getFile())));

        if (is_array($response) && isset($response[0])) {
            return $response[0];
        }

        return false;
    }

    /**
     * Prepare product images
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $mainProduct
     *
     * @return array
     * @throws \Zend_Http_Client_Exception
     */
    public function prepareImages(\Magento\Catalog\Api\Data\ProductInterface $mainProduct)
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

                        if ($image->getImageData()) {
                            $savedImageData = json_decode($image->getImageData(), true);

                            if ($isMainProduct) {
                                $imagesData['main'][] = $savedImageData;
                            } else {
                                $imagesData['children'][$product->getId()][] = $savedImageData;
                            }
                        }

                        break;
                    }
                }

                if (!$isFound) {
                    $imagesData['_syncImageData'][] = [
                        'product_id' => $product->getId(),
                        'image_id'   => $productGalleryImage->getId(),
                        'image_data' => ''
                    ];
                }
            }
        }

        return $imagesData;
    }

    /**
     * Add content media
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $mainProduct
     *
     * @return array
     * @throws \Zend_Http_Client_Exception
     */
    public function addContentMedia(\Magento\Catalog\Api\Data\ProductInterface $mainProduct)
    {
        $contentMediaData = [
            'content' => $mainProduct->getDescription(),
            'syncMedia' => []
        ];

        if (preg_match_all(\Magento\Framework\Filter\Template::CONSTRUCTION_PATTERN, $contentMediaData['content'], $constructions, PREG_SET_ORDER)) {
            $mediaPath = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();

            foreach ($constructions as $construction) {
                if (isset($construction[1]) && $construction[1] == 'media') {
                    $imagePath = $this->filter->mediaDirective($construction);

                    $this->filterGroup->setFilters([
                        $this->filterBuilder
                            ->setField('image_path')
                            ->setConditionType('eq')
                            ->setValue($imagePath)
                            ->create(),
                    ]);

                    $this->searchCriteria->setFilterGroups([$this->filterGroup]);

                    $items = $this->contentMediaRepository
                        ->getList($this->searchCriteria)
                        ->getItems();

                    if ($items) {
                        $item = reset($items);
                        $imageData = json_decode($item['image_data']);

                        $contentMediaData['content'] = str_replace($construction[0], $imageData->url, $contentMediaData['content']);
                    } else {
                        $response = $this->requestImagesRepository->create($mediaPath . $imagePath);

                        if (is_array($response) && isset($response[0])) {
                            $contentMediaData['syncMedia'][] = [
                                'image_path' => $imagePath,
                                'image_data' => json_encode($response[0]),
                            ];

                            $contentMediaData['content'] =
                                str_replace($construction[0], $response[0]['url'], $contentMediaData['content']);
                        }
                    }
                }
            }
        }

        return $contentMediaData;
    }
}

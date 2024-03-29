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
     * @var \Magento\Framework\Api\Search\FilterGroupBuilder
     */
    protected $filterGroupBuilder;

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
     * @var \Walkthechat\Walkthechat\Model\ProductService
     */
    protected $productService;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;
    
    /**
     * @var \Walkthechat\Walkthechat\Api\Data\ImageSyncInterfaceFactory
     */
    protected $imageSyncFactory;
    
    /**
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
     */
    protected $configurableProductType;
    
    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $configurable;
    
    /**
     * ImageService constructor.
     * @param \Walkthechat\Walkthechat\Service\ImagesRepository $requestImagesRepository
     * @param \Walkthechat\Walkthechat\Api\ImageSyncRepositoryInterface $imageSyncRepository
     * @param \Walkthechat\Walkthechat\Api\ContentMediaRepositoryInterface $contentMediaRepository
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param Template\Filter $filter
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Catalog\Model\Product\Gallery\GalleryManagement $galleryManagement
     * @param \Magento\Catalog\Model\Product\Media\Config $catalogProductMediaConfig
     * @param \Walkthechat\Walkthechat\Model\ProductService $productService
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Walkthechat\Walkthechat\Api\Data\ImageSyncInterfaceFactory $imageSyncFactory
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProductType
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
     */
    public function __construct(
        \Walkthechat\Walkthechat\Service\ImagesRepository $requestImagesRepository,
        \Walkthechat\Walkthechat\Api\ImageSyncRepositoryInterface $imageSyncRepository,
        \Walkthechat\Walkthechat\Api\ContentMediaRepositoryInterface $contentMediaRepository,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Walkthechat\Walkthechat\Model\Template\Filter $filter,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Catalog\Model\Product\Gallery\GalleryManagement $galleryManagement,
        \Magento\Catalog\Model\Product\Media\Config $catalogProductMediaConfig,
        \Walkthechat\Walkthechat\Model\ProductService $productService,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Walkthechat\Walkthechat\Api\Data\ImageSyncInterfaceFactory $imageSyncFactory,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProductType,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
    ) {
        $this->requestImagesRepository = $requestImagesRepository;
        $this->imageSyncRepository     = $imageSyncRepository;
        $this->contentMediaRepository  = $contentMediaRepository;
        $this->searchCriteria          = $searchCriteria;
        $this->filterGroup             = $filterGroup;
        $this->filterGroupBuilder      = $filterGroupBuilder;
        $this->filterBuilder           = $filterBuilder;
        $this->filter                  = $filter;
        $this->filesystem              = $filesystem;
        $this->galleryManagement       = $galleryManagement;
        $this->catalogProductMediaConfig = $catalogProductMediaConfig;
        $this->productService          = $productService;
        $this->productRepository       = $productRepository;
        $this->imageSyncFactory        = $imageSyncFactory;
        $this->configurableProductType = $configurableProductType;
        $this->configurable            = $configurable;
    }

    /**
     * @return mixed
     */
    public function getSyncedImages()
    {
        $productIds = [];

        foreach ($this->productService->getSyncedProducts()->getItems() as $product) {
            $productIds[] = $product->getId();

            if ($product->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $children = $product->getTypeInstance()->getUsedProducts($product);

                foreach ($children as $child) {
                    $productIds[] = $child->getId();
                }
            }
        }

        $filterGroup1 = $this->filterGroupBuilder
            ->addFilter(
                $this->filterBuilder
                    ->setField('product_id')
                    ->setConditionType('in')
                    ->setValue($productIds)
                    ->create()
            )
            ->create();

        $filterGroup2 = $this->filterGroupBuilder
            ->addFilter(
                $this->filterBuilder
                    ->setField('image_data')
                    ->setConditionType('neq')
                    ->setValue('')
                    ->create()
            )
            ->create();

        $this->searchCriteria->setFilterGroups([$filterGroup1, $filterGroup2]);

        return $this->imageSyncRepository
            ->getList($this->searchCriteria);
    }

    /**
     * @return mixed
     */
    public function getExportedImages()
    {
        $productIds = [];

        foreach ($this->productService->getSyncedProducts()->getItems() as $product) {
            $productIds[] = $product->getId();

            if ($product->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $children = $product->getTypeInstance()->getUsedProducts($product);

                foreach ($children as $child) {
                    $productIds[] = $child->getId();
                }
            }
        }

        $this->filterGroup->setFilters([
            $this->filterBuilder
                ->setField('product_id')
                ->setConditionType('in')
                ->setValue($productIds)
                ->create(),
        ]);

        $this->searchCriteria->setFilterGroups([$this->filterGroup]);

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
     * Get image url using the SKU and ID in the gallery
     *
     * @param string $sku
     * @param int $id
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageUrl(string $sku, int $id)
    {
        $galleryImage = $this->galleryManagement->get($sku, $id);
        $directory = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);

        return $this->catalogProductMediaConfig->getMediaUrl($galleryImage->getFile());
    }
    
    /**
     * Add images to wtc
     *
     * @param array $urls
     * @return mixed
     */
    public function addImages(array $urls)
    {
        $response = $this->requestImagesRepository->backgroundUpload($urls);
        
        return $response;
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
            $imgPosition = 0;

            foreach ($product->getMediaGalleryImages() as $productGalleryImage) {
                $isFound = false;

                // check if product image exist in saved images
                foreach ($savedImages as $image) {
                    if ($productGalleryImage->getId() == $image->getImageId()) {
                        $isFound = true;

                        if ($image->getImageData()) {
                            $savedImageData = json_decode($image->getImageData(), true);

                            if ($product->getThumbnail() == $productGalleryImage->getFile()) {
                                $savedImageData['position'] = 0;
                            } else {
                                $imgPosition++;
                                $savedImageData['position'] = $imgPosition;
                            }

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
                        'image_url'  => $productGalleryImage->getUrl(),
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
    
    /**
     * @return mixed
     */
    public function updateImagesWithEmptyUrl()
    {    
        $filterGroup = $this->filterGroupBuilder
            ->addFilter(
                $this->filterBuilder
                    ->setField('image_url')
                    ->setConditionType('null')
                    ->create()
            )
            ->create();
                
        $this->searchCriteria->setFilterGroups([$filterGroup]);
                
        $images = $this->imageSyncRepository
            ->getList($this->searchCriteria)
            ->getItems();
        
        $imagesUrls = [];
        
        foreach ($images as $image) {
            if (!isset($imagesUrls[$image->getImageId()])) {
                try {
                    $product = $this->productRepository->getById($image->getProductId());
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $this->imageSyncRepository->deleteByProductIds([$image->getProductId()]);
                    continue;
                }
                
                foreach ($product->getMediaGalleryImages() as $productGalleryImage) {
                    $imagesUrls[$productGalleryImage->getId()] = $productGalleryImage->getUrl();
                }
            }
            
            $model = $this->imageSyncFactory->create()->load($image->getId());
            $model->setImageUrl($imagesUrls[$productGalleryImage->getId()]);
            
            $this->imageSyncRepository->save($model);
        }
    }
    
    /**
     * Reset product images data
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     *
     * @return array
     * @throws \Zend_Http_Client_Exception
     */
    public function resetImagesData(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        if (in_array($product->getTypeId(), [\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE, \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL])) {
            $isPartOfConfigurable = false;
            
            foreach ($this->configurableProductType->getParentIdsByChild($product->getId()) as $parentId) {
                $isPartOfConfigurable = true;
                
                $parent = $this->productRepository->getById($parentId);
                
                $this->resetImagesData($parent);
            }
            
            if (!$isPartOfConfigurable) {
                $this->filterGroup->setFilters([
                    $this->filterBuilder
                    ->setField('product_id')
                    ->setConditionType('eq')
                    ->setValue($product->getId())
                    ->create(),
                ]);
            } else {
                return;
            }
        } elseif ($product->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $children = $this->configurable->getChildrenIds($product->getId());
            $ids = array_merge([$product->getId()], $children[0]);
            
            $this->filterGroup->setFilters([
                $this->filterBuilder
                ->setField('product_id')
                ->setConditionType('in')
                ->setValue($ids)
                ->create(),
            ]);
        }
        
        $this->searchCriteria->setFilterGroups([$this->filterGroup]);
        
        $images = $this->imageSyncRepository->getList($this->searchCriteria)->getItems();
        
        foreach ($images as $image) {
            $model = $this->imageSyncFactory->create()->load($image->getId());
            $model->setImageData('');
            try {
                $model->setImageUrl($this->getImageUrl($product->getSku(), $image->getImageId()));
            } catch (\Exception $e) {
                /** @todo : some images can be deleted, log the execption. Bypassed at the moment */
            }

            $this->imageSyncRepository->save($model);
        }
    }
}

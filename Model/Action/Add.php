<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model\Action;

/**
 * Class Add
 *
 * @package Walkthechat\Walkthechat\Model\Action
 */
class Add extends \Walkthechat\Walkthechat\Model\Action\AbstractAction
{
    /**
     * Action name
     *
     * @string
     */
    const ACTION = 'add';

    /**
     * @var \Walkthechat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Walkthechat\Walkthechat\Service\ProductsRepository
     */
    protected $queueProductRepository;

    /**
     * @var \Walkthechat\Walkthechat\Service\ImagesRepository
     */
    protected $requestImagesRepository;

    /**
     * @var \Walkthechat\Walkthechat\Model\ImageService
     */
    protected $imageService;

    /**
     * @var \Walkthechat\Walkthechat\Model\ProductService
     */
    protected $productService;

    /**
     * @var \Walkthechat\Walkthechat\Api\Data\ContentMediaInterfaceFactory
     */
    protected $contentMediaFactory;

    /**
     * @var \Walkthechat\Walkthechat\Api\ContentMediaRepositoryInterface
     */
    protected $contentMediaRepository;

    /**
     * Add constructor.
     * @param \Walkthechat\Walkthechat\Helper\Data $helper
     * @param \Walkthechat\Walkthechat\Api\Data\ImageSyncInterfaceFactory $imageSyncFactory
     * @param \Walkthechat\Walkthechat\Api\ImageSyncRepositoryInterface $imageSyncRepository
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Walkthechat\Walkthechat\Service\ProductsRepository $queueProductRepositoryFactory
     * @param \Walkthechat\Walkthechat\Service\ImagesRepository $requestImagesRepository
     * @param \Walkthechat\Walkthechat\Model\ImageService $imageService
     * @param \Walkthechat\Walkthechat\Model\ProductService $productService
     * @param \Walkthechat\Walkthechat\Api\Data\ContentMediaInterfaceFactory $contentMediaFactory
     * @param \Walkthechat\Walkthechat\Api\ContentMediaRepositoryInterface $contentMediaRepository
     */
    public function __construct(
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Walkthechat\Walkthechat\Api\Data\ImageSyncInterfaceFactory $imageSyncFactory,
        \Walkthechat\Walkthechat\Api\ImageSyncRepositoryInterface $imageSyncRepository,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Walkthechat\Walkthechat\Service\ProductsRepository $queueProductRepositoryFactory,
        \Walkthechat\Walkthechat\Service\ImagesRepository $requestImagesRepository,
        \Walkthechat\Walkthechat\Model\ImageService $imageService,
        \Walkthechat\Walkthechat\Model\ProductService $productService,
        \Walkthechat\Walkthechat\Api\Data\ContentMediaInterfaceFactory $contentMediaFactory,
        \Walkthechat\Walkthechat\Api\ContentMediaRepositoryInterface $contentMediaRepository
    ) {
        $this->helper                  = $helper;
        $this->productRepository       = $productRepository;
        $this->queueProductRepository  = $queueProductRepositoryFactory;
        $this->requestImagesRepository = $requestImagesRepository;
        $this->imageService            = $imageService;
        $this->productService          = $productService;
        $this->contentMediaFactory     = $contentMediaFactory;
        $this->contentMediaRepository  = $contentMediaRepository;

        parent::__construct(
            $imageSyncFactory,
            $imageSyncRepository
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Zend_Http_Client_Exception
     */
    public function execute(\Walkthechat\Walkthechat\Api\Data\QueueInterface $queueItem)
    {
        $product            = $this->productRepository->getById($queueItem->getProductId(), false, $this->helper->getStore()->getId());
        $imagesData         = $this->imageService->prepareImages($product);
        $contentMediaData   = $this->imageService->addContentMedia($product);

        $data          = $this->productService->prepareProductData($product, true, $imagesData, $contentMediaData);
        $walkTheChatId = $this->queueProductRepository->create($data);

        if (!$walkTheChatId) {
            return false;
        }

        $product->setWalkthechatId($walkTheChatId);

        $this->productRepository->save($product);

        if ($imagesData['_syncImageData']) {
            $this->saveImagesToSyncTable($imagesData['_syncImageData']);
        }
        if ($contentMediaData['syncMedia']) {
            foreach ($contentMediaData['syncMedia'] as $item) {
                /** @var \Walkthechat\Walkthechat\Model\ContentMedia $model */
                $model = $this->contentMediaFactory->create();
                $model->setData($item);

                $this->contentMediaRepository->save($model);
            }
        }

        return true;
    }
}

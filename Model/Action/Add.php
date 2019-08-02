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
     * {@inheritdoc}
     *
     * @param \Walkthechat\Walkthechat\Helper\Data                $helper
     * @param \Magento\Catalog\Model\ProductRepository        $productRepository
     * @param \Walkthechat\Walkthechat\Service\ProductsRepository $queueProductRepositoryFactory
     * @param \Walkthechat\Walkthechat\Service\ImagesRepository   $requestImagesRepository
     * @param \Walkthechat\Walkthechat\Model\ImageService         $imageService
     * @param \Walkthechat\Walkthechat\Model\ProductService       $productService
     */
    public function __construct(
        \Walkthechat\Walkthechat\Api\Data\ImageSyncInterfaceFactory $imageSyncFactory,
        \Walkthechat\Walkthechat\Api\ImageSyncRepositoryInterface $imageSyncRepository,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Walkthechat\Walkthechat\Service\ProductsRepository $queueProductRepositoryFactory,
        \Walkthechat\Walkthechat\Service\ImagesRepository $requestImagesRepository,
        \Walkthechat\Walkthechat\Model\ImageService $imageService,
        \Walkthechat\Walkthechat\Model\ProductService $productService
    ) {
        $this->productRepository       = $productRepository;
        $this->queueProductRepository  = $queueProductRepositoryFactory;
        $this->requestImagesRepository = $requestImagesRepository;
        $this->imageService            = $imageService;
        $this->productService          = $productService;

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
        $product    = $this->productRepository->getById($queueItem->getProductId());
        $imagesData = $this->imageService->addImages($product);

        $data          = $this->productService->prepareProductData($product, true, $imagesData);
        $walkTheChatId = $this->queueProductRepository->create($data);

        if (!$walkTheChatId) {
            return false;
        }

        $product->setWalkthechatId($walkTheChatId);

        $this->productRepository->save($product);

        if ($imagesData['_syncImageData']) {
            $this->saveImagesToSyncTable($imagesData['_syncImageData']);
        }

        return true;
    }
}

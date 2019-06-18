<?php
/**
 * @package   WalktheChat\Walkthechat
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Model\Action;

/**
 * Class Add
 *
 * @package WalktheChat\Walkthechat\Model\Action
 */
class Add extends \WalktheChat\Walkthechat\Model\Action\AbstractAction
{
    /**
     * Action name
     *
     * @string
     */
    const ACTION = 'add';

    /**
     * @var \WalktheChat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \WalktheChat\Walkthechat\Service\ProductsRepository
     */
    protected $queueProductRepository;

    /**
     * @var \WalktheChat\Walkthechat\Service\ImagesRepository
     */
    protected $requestImagesRepository;

    /**
     * @var \WalktheChat\Walkthechat\Model\ImageService
     */
    protected $imageService;

    /**
     * @var \WalktheChat\Walkthechat\Model\ProductService
     */
    protected $productService;

    /**
     * {@inheritdoc}
     *
     * @param \WalktheChat\Walkthechat\Helper\Data                $helper
     * @param \Magento\Catalog\Model\ProductRepository        $productRepository
     * @param \WalktheChat\Walkthechat\Service\ProductsRepository $queueProductRepositoryFactory
     * @param \WalktheChat\Walkthechat\Service\ImagesRepository   $requestImagesRepository
     * @param \WalktheChat\Walkthechat\Model\ImageService         $imageService
     * @param \WalktheChat\Walkthechat\Model\ProductService       $productService
     */
    public function __construct(
        \WalktheChat\Walkthechat\Api\Data\ImageSyncInterfaceFactory $imageSyncFactory,
        \WalktheChat\Walkthechat\Api\ImageSyncRepositoryInterface $imageSyncRepository,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \WalktheChat\Walkthechat\Service\ProductsRepository $queueProductRepositoryFactory,
        \WalktheChat\Walkthechat\Service\ImagesRepository $requestImagesRepository,
        \WalktheChat\Walkthechat\Model\ImageService $imageService,
        \WalktheChat\Walkthechat\Model\ProductService $productService
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
    public function execute(\WalktheChat\Walkthechat\Api\Data\QueueInterface $queueItem)
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

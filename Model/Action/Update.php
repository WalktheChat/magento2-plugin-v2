<?php
/**
 * @package   WalktheChat\Walkthechat
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Model\Action;

/**
 * Class Update
 *
 * @package WalktheChat\Walkthechat\Model\Action
 */
class Update extends \WalktheChat\Walkthechat\Model\Action\AbstractAction
{
    /**
     * Action name
     *
     * @string
     */
    const ACTION = 'update';

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $orderRepository;

    /**
     * @var \WalktheChat\Walkthechat\Service\ProductsRepository
     */
    protected $queueProductRepository;

    /**
     * @var \WalktheChat\Walkthechat\Service\OrdersRepository
     */
    protected $queueOrderRepository;

    /**
     * @var \WalktheChat\Walkthechat\Model\ImageService
     */
    protected $imageService;

    /**
     * @var \WalktheChat\Walkthechat\Model\OrderService
     */
    protected $orderService;

    /**
     * @var \WalktheChat\Walkthechat\Model\ProductService
     */
    protected $productService;

    /**
     * @var \WalktheChat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * {@inheritdoc}
     *
     * @param \Magento\Catalog\Model\ProductRepository        $productRepository
     * @param \Magento\Sales\Model\OrderRepository            $orderRepository
     * @param \WalktheChat\Walkthechat\Service\ProductsRepository $queueProductRepository
     * @param \WalktheChat\Walkthechat\Service\OrdersRepository   $queueOrderRepository
     * @param \WalktheChat\Walkthechat\Model\ImageService         $imageService
     * @param \WalktheChat\Walkthechat\Model\OrderService         $orderService
     * @param \WalktheChat\Walkthechat\Model\ProductService       $productService
     * @param \WalktheChat\Walkthechat\Helper\Data                $helper
     */
    public function __construct(
        \WalktheChat\Walkthechat\Api\Data\ImageSyncInterfaceFactory $imageSyncFactory,
        \WalktheChat\Walkthechat\Api\ImageSyncRepositoryInterface $imageSyncRepository,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \WalktheChat\Walkthechat\Service\ProductsRepository $queueProductRepository,
        \WalktheChat\Walkthechat\Service\OrdersRepository $queueOrderRepository,
        \WalktheChat\Walkthechat\Model\ImageService $imageService,
        \WalktheChat\Walkthechat\Model\OrderService $orderService,
        \WalktheChat\Walkthechat\Model\ProductService $productService,
        \WalktheChat\Walkthechat\Helper\Data $helper
    ) {
        $this->productRepository      = $productRepository;
        $this->orderRepository        = $orderRepository;
        $this->queueProductRepository = $queueProductRepository;
        $this->queueOrderRepository   = $queueOrderRepository;
        $this->imageService           = $imageService;
        $this->orderService           = $orderService;
        $this->productService         = $productService;
        $this->helper                 = $helper;

        parent::__construct(
            $imageSyncFactory,
            $imageSyncRepository
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Http_Client_Exception
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute(\WalktheChat\Walkthechat\Api\Data\QueueInterface $queueItem)
    {
        if ($queueItem->getProductId()) {
            $product    = $this->productRepository->getById($queueItem->getProductId());
            $imagesData = $this->imageService->updateImages($product);
            $data       = $this->productService->prepareProductData($product, false, $imagesData);

            $data['id'] = $queueItem->getWalkthechatId() ?? $this->helper->getWalkTheChatAttributeValue($product);

            $this->queueProductRepository->update($data);

            if (isset($imagesData['_syncImageData']) && $imagesData['_syncImageData']) {
                $this->saveImagesToSyncTable($imagesData['_syncImageData']);
            }
        } elseif ($queueItem->getOrderId()) {
            $order = $this->orderRepository->get($queueItem->getOrderId());
            $data  = $this->orderService->prepareOrderData($order);

            $this->queueOrderRepository->update($data);
        }

        return true;
    }
}

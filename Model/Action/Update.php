<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 Walkthechat
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model\Action;

/**
 * Class Update
 *
 * @package Walkthechat\Walkthechat\Model\Action
 */
class Update extends \Walkthechat\Walkthechat\Model\Action\AbstractAction
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
     * @var \Walkthechat\Walkthechat\Service\ProductsRepository
     */
    protected $queueProductRepository;

    /**
     * @var \Walkthechat\Walkthechat\Service\OrdersRepository
     */
    protected $queueOrderRepository;

    /**
     * @var \Walkthechat\Walkthechat\Model\ImageService
     */
    protected $imageService;

    /**
     * @var \Walkthechat\Walkthechat\Model\OrderService
     */
    protected $orderService;

    /**
     * @var \Walkthechat\Walkthechat\Model\ProductService
     */
    protected $productService;

    /**
     * @var \Walkthechat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * {@inheritdoc}
     *
     * @param \Magento\Catalog\Model\ProductRepository        $productRepository
     * @param \Magento\Sales\Model\OrderRepository            $orderRepository
     * @param \Walkthechat\Walkthechat\Service\ProductsRepository $queueProductRepository
     * @param \Walkthechat\Walkthechat\Service\OrdersRepository   $queueOrderRepository
     * @param \Walkthechat\Walkthechat\Model\ImageService         $imageService
     * @param \Walkthechat\Walkthechat\Model\OrderService         $orderService
     * @param \Walkthechat\Walkthechat\Model\ProductService       $productService
     * @param \Walkthechat\Walkthechat\Helper\Data                $helper
     */
    public function __construct(
        \Walkthechat\Walkthechat\Api\Data\ImageSyncInterfaceFactory $imageSyncFactory,
        \Walkthechat\Walkthechat\Api\ImageSyncRepositoryInterface $imageSyncRepository,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Walkthechat\Walkthechat\Service\ProductsRepository $queueProductRepository,
        \Walkthechat\Walkthechat\Service\OrdersRepository $queueOrderRepository,
        \Walkthechat\Walkthechat\Model\ImageService $imageService,
        \Walkthechat\Walkthechat\Model\OrderService $orderService,
        \Walkthechat\Walkthechat\Model\ProductService $productService,
        \Walkthechat\Walkthechat\Helper\Data $helper
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
    public function execute(\Walkthechat\Walkthechat\Api\Data\QueueInterface $queueItem)
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

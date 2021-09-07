<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Observer;

/**
 * Class SalesOrderCreditmemoRefund
 *
 * @package Walkthechat\Walkthechat\Observer
 */
class SalesOrderCreditmemoRefund implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Walkthechat\Walkthechat\Api\Data\QueueInterfaceFactory
     */
    protected $queueFactory;

    /**
     * @var \Walkthechat\Walkthechat\Model\QueueRepository
     */
    protected $queueRepository;

    /**
     * @var \Walkthechat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * @var \Walkthechat\Walkthechat\Model\QueueService
     */
    protected $queueService;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
     */
    protected $configurableProductType;

    /**
     * CatalogProductSaveAfter constructor.
     *
     * @param \Walkthechat\Walkthechat\Api\Data\QueueInterfaceFactory $queueFactory
     * @param \Walkthechat\Walkthechat\Model\QueueRepository          $queueRepository
     * @param \Walkthechat\Walkthechat\Helper\Data                    $helper
     * @param \Walkthechat\Walkthechat\Model\QueueService             $queueService
     * @param \Magento\Catalog\Model\ProductRepository                $productRepository
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProductType
     */
    public function __construct(
        \Walkthechat\Walkthechat\Api\Data\QueueInterfaceFactory $queueFactory,
        \Walkthechat\Walkthechat\Model\QueueRepository $queueRepository,
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Walkthechat\Walkthechat\Model\QueueService $queueService,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProductType
    ) {
        $this->queueFactory             = $queueFactory;
        $this->queueRepository          = $queueRepository;
        $this->helper                   = $helper;
        $this->queueService             = $queueService;
        $this->productRepository        = $productRepository;
        $this->configurableProductType  = $configurableProductType;
    }

    /**
     * Add item to queue once order is placed
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isEnabledOrderSync()) {
            $order = $observer->getEvent()->getCreditmemo()->getOrder();

            foreach ($order->getAllItems() as $item) {
                $product       = $item->getProduct();
                $walkTheChatId = $this->helper->getWalkTheChatAttributeValue($product);

                if (!$walkTheChatId) {
                    $parentIds = $this->configurableProductType->getParentIdsByChild($product->getId());

                    if (count($parentIds)) {
                        $product = $this->productRepository->getById($parentIds[0]);
                        $walkTheChatId = $this->helper->getWalkTheChatAttributeValue($product);
                    }
                }

                if (
                    $walkTheChatId
                    && !$this->queueService->isDuplicate(
                        $product->getId(),
                        \Walkthechat\Walkthechat\Model\Action\Update::ACTION,
                        'product_id'
                    )
                ) {
                    /** @var \Walkthechat\Walkthechat\Api\Data\QueueInterface $model */
                    $model = $this->queueFactory->create();

                    $model->setProductId($product->getId());
                    $model->setWalkthechatId($walkTheChatId);
                    $model->setAction(\Walkthechat\Walkthechat\Model\Action\Update::ACTION);

                    $this->queueRepository->save($model);
                }
            }
        }
    }
}

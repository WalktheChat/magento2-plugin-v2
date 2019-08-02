<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Observer;

/**
 * Class SalesOrderSaveAfter
 *
 * @package Walkthechat\Walkthechat\Observer
 */
class SalesOrderSaveAfter implements \Magento\Framework\Event\ObserverInterface
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
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * CatalogProductSaveAfter constructor.
     *
     * @param \Walkthechat\Walkthechat\Api\Data\QueueInterfaceFactory $queueFactory
     * @param \Walkthechat\Walkthechat\Model\QueueRepository          $queueRepository
     * @param \Walkthechat\Walkthechat\Helper\Data                    $helper
     * @param \Walkthechat\Walkthechat\Model\QueueService             $queueService
     * @param \Magento\Framework\Registry                         $registry
     * @param \Magento\Sales\Api\OrderRepositoryInterface         $orderRepository
     */
    public function __construct(
        \Walkthechat\Walkthechat\Api\Data\QueueInterfaceFactory $queueFactory,
        \Walkthechat\Walkthechat\Model\QueueRepository $queueRepository,
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Walkthechat\Walkthechat\Model\QueueService $queueService,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->queueFactory    = $queueFactory;
        $this->queueRepository = $queueRepository;
        $this->helper          = $helper;
        $this->queueService    = $queueService;
        $this->registry        = $registry;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Add item to queue once order is updated
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isEnabledOrderSync()) {
            $order = $observer->getEvent()->getOrder();

            // is shipment was called then fetch order from shipment instance
            if (!$order instanceof \Magento\Sales\Api\Data\OrderInterface) {
                $shipment = $observer->getEvent()->getShipment();

                if ($shipment instanceof \Magento\Sales\Api\Data\ShipmentInterface) {
                    $order = $this->orderRepository->get($shipment->getOrderId());
                }
            }

            if (
                $order instanceof \Magento\Sales\Api\Data\OrderInterface
                && !$this->registry->registry('walkthechat_omit_update_action')
                && $order->getWalkthechatId()
                && !$this->queueService->isDuplicate(
                    $order->getEntityId(),
                    \Walkthechat\Walkthechat\Model\Action\Update::ACTION,
                    'order_id'
                )
            ) {
                /** @var \Walkthechat\Walkthechat\Api\Data\QueueInterface $model */
                $model = $this->queueFactory->create();

                $model->setOrderId($order->getEntityId());
                $model->setAction(\Walkthechat\Walkthechat\Model\Action\Update::ACTION);

                $this->queueRepository->save($model);
            }
        }
    }
}

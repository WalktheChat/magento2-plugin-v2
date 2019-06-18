<?php
/**
 * @package   WalktheChat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Observer;

/**
 * Class SalesOrderSaveAfter
 *
 * @package WalktheChat\Walkthechat\Observer
 */
class SalesOrderSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \WalktheChat\Walkthechat\Api\Data\QueueInterfaceFactory
     */
    protected $queueFactory;

    /**
     * @var \WalktheChat\Walkthechat\Model\QueueRepository
     */
    protected $queueRepository;

    /**
     * @var \WalktheChat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * @var \WalktheChat\Walkthechat\Model\QueueService
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
     * @param \WalktheChat\Walkthechat\Api\Data\QueueInterfaceFactory $queueFactory
     * @param \WalktheChat\Walkthechat\Model\QueueRepository          $queueRepository
     * @param \WalktheChat\Walkthechat\Helper\Data                    $helper
     * @param \WalktheChat\Walkthechat\Model\QueueService             $queueService
     * @param \Magento\Framework\Registry                         $registry
     * @param \Magento\Sales\Api\OrderRepositoryInterface         $orderRepository
     */
    public function __construct(
        \WalktheChat\Walkthechat\Api\Data\QueueInterfaceFactory $queueFactory,
        \WalktheChat\Walkthechat\Model\QueueRepository $queueRepository,
        \WalktheChat\Walkthechat\Helper\Data $helper,
        \WalktheChat\Walkthechat\Model\QueueService $queueService,
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
                    \WalktheChat\Walkthechat\Model\Action\Update::ACTION,
                    'order_id'
                )
            ) {
                /** @var \WalktheChat\Walkthechat\Api\Data\QueueInterface $model */
                $model = $this->queueFactory->create();

                $model->setOrderId($order->getEntityId());
                $model->setAction(\WalktheChat\Walkthechat\Model\Action\Update::ACTION);

                $this->queueRepository->save($model);
            }
        }
    }
}

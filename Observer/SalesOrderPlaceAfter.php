<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Observer;

/**
 * Class SalesOrderPlaceAfter
 *
 * @package Walkthechat\Walkthechat\Observer
 */
class SalesOrderPlaceAfter implements \Magento\Framework\Event\ObserverInterface
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
     * CatalogProductSaveAfter constructor.
     *
     * @param \Walkthechat\Walkthechat\Api\Data\QueueInterfaceFactory $queueFactory
     * @param \Walkthechat\Walkthechat\Model\QueueRepository          $queueRepository
     * @param \Walkthechat\Walkthechat\Helper\Data                    $helper
     * @param \Walkthechat\Walkthechat\Model\QueueService             $queueService
     * @param \Magento\Framework\Registry                         $registry
     */
    public function __construct(
        \Walkthechat\Walkthechat\Api\Data\QueueInterfaceFactory $queueFactory,
        \Walkthechat\Walkthechat\Model\QueueRepository $queueRepository,
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Walkthechat\Walkthechat\Model\QueueService $queueService,
        \Magento\Framework\Registry $registry
    ) {
        $this->queueFactory    = $queueFactory;
        $this->queueRepository = $queueRepository;
        $this->helper          = $helper;
        $this->queueService    = $queueService;
        $this->registry        = $registry;
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
            $order = $observer->getEvent()->getOrder();

            if (
                $order instanceof \Magento\Sales\Api\Data\OrderInterface
                && !$this->registry->registry('walkthechat_omit_update_action')
                && !$this->queueService->isDuplicate(
                    $order->getId(),
                    \Walkthechat\Walkthechat\Model\Action\Update::ACTION,
                    'order_id'
                )
            ) {
                foreach ($order->getAllItems() as $item) {
                    $product       = $item->getProduct();
                    $walkTheChatId = $this->helper->getWalkTheChatAttributeValue($product);

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
}

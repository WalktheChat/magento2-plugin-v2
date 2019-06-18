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
 * Class SalesOrderPlaceAfter
 *
 * @package WalktheChat\Walkthechat\Observer
 */
class SalesOrderPlaceAfter implements \Magento\Framework\Event\ObserverInterface
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
     * CatalogProductSaveAfter constructor.
     *
     * @param \WalktheChat\Walkthechat\Api\Data\QueueInterfaceFactory $queueFactory
     * @param \WalktheChat\Walkthechat\Model\QueueRepository          $queueRepository
     * @param \WalktheChat\Walkthechat\Helper\Data                    $helper
     * @param \WalktheChat\Walkthechat\Model\QueueService             $queueService
     * @param \Magento\Framework\Registry                         $registry
     */
    public function __construct(
        \WalktheChat\Walkthechat\Api\Data\QueueInterfaceFactory $queueFactory,
        \WalktheChat\Walkthechat\Model\QueueRepository $queueRepository,
        \WalktheChat\Walkthechat\Helper\Data $helper,
        \WalktheChat\Walkthechat\Model\QueueService $queueService,
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
                    \WalktheChat\Walkthechat\Model\Action\Update::ACTION,
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
                            \WalktheChat\Walkthechat\Model\Action\Update::ACTION,
                            'product_id'
                        )
                    ) {
                        /** @var \WalktheChat\Walkthechat\Api\Data\QueueInterface $model */
                        $model = $this->queueFactory->create();

                        $model->setProductId($product->getId());
                        $model->setWalkthechatId($walkTheChatId);
                        $model->setAction(\WalktheChat\Walkthechat\Model\Action\Update::ACTION);

                        $this->queueRepository->save($model);
                    }
                }
            }
        }
    }
}

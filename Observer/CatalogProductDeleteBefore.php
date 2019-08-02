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
 * Class CatalogProductDeleteBefore
 *
 * @package Walkthechat\Walkthechat\Observer
 */
class CatalogProductDeleteBefore implements \Magento\Framework\Event\ObserverInterface
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
     * CatalogProductSaveAfter constructor.
     *
     * @param \Walkthechat\Walkthechat\Api\Data\QueueInterfaceFactory $queueFactory
     * @param \Walkthechat\Walkthechat\Model\QueueRepository          $queueRepository
     * @param \Walkthechat\Walkthechat\Helper\Data                    $helper
     * @param \Walkthechat\Walkthechat\Model\QueueService             $queueService
     */
    public function __construct(
        \Walkthechat\Walkthechat\Api\Data\QueueInterfaceFactory $queueFactory,
        \Walkthechat\Walkthechat\Model\QueueRepository $queueRepository,
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Walkthechat\Walkthechat\Model\QueueService $queueService
    ) {
        $this->queueFactory    = $queueFactory;
        $this->queueRepository = $queueRepository;
        $this->helper          = $helper;
        $this->queueService    = $queueService;
    }

    /**
     * Add item to queue once product is deleted
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isEnabledProductSync()) {
            $product = $observer->getProduct();

            if ($product instanceof \Magento\Catalog\Model\Product) {
                $walkTheChatId = $this->helper->getWalkTheChatAttributeValue($product);

                if (
                    $walkTheChatId
                    && !$this->queueService->isDuplicate(
                        $walkTheChatId,
                        \Walkthechat\Walkthechat\Model\Action\Delete::ACTION,
                        \Walkthechat\Walkthechat\Helper\Data::ATTRIBUTE_CODE
                    )
                ) {
                    /** @var \Walkthechat\Walkthechat\Api\Data\QueueInterface $model */
                    $model = $this->queueFactory->create();

                    $model->setWalkthechatId($walkTheChatId);
                    $model->setAction(\Walkthechat\Walkthechat\Model\Action\Delete::ACTION);

                    $this->queueRepository->save($model);
                }
            }
        }
    }
}

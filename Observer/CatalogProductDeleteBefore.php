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
 * Class CatalogProductDeleteBefore
 *
 * @package WalktheChat\Walkthechat\Observer
 */
class CatalogProductDeleteBefore implements \Magento\Framework\Event\ObserverInterface
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
     * CatalogProductSaveAfter constructor.
     *
     * @param \WalktheChat\Walkthechat\Api\Data\QueueInterfaceFactory $queueFactory
     * @param \WalktheChat\Walkthechat\Model\QueueRepository          $queueRepository
     * @param \WalktheChat\Walkthechat\Helper\Data                    $helper
     * @param \WalktheChat\Walkthechat\Model\QueueService             $queueService
     */
    public function __construct(
        \WalktheChat\Walkthechat\Api\Data\QueueInterfaceFactory $queueFactory,
        \WalktheChat\Walkthechat\Model\QueueRepository $queueRepository,
        \WalktheChat\Walkthechat\Helper\Data $helper,
        \WalktheChat\Walkthechat\Model\QueueService $queueService
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
                        \WalktheChat\Walkthechat\Model\Action\Delete::ACTION,
                        \WalktheChat\Walkthechat\Helper\Data::ATTRIBUTE_CODE
                    )
                ) {
                    /** @var \WalktheChat\Walkthechat\Api\Data\QueueInterface $model */
                    $model = $this->queueFactory->create();

                    $model->setWalkthechatId($walkTheChatId);
                    $model->setAction(\WalktheChat\Walkthechat\Model\Action\Delete::ACTION);

                    $this->queueRepository->save($model);
                }
            }
        }
    }
}

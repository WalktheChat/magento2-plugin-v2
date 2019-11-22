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
 * Class CatalogProductAttributeUpdateBefore
 *
 * @package Walkthechat\Walkthechat\Observer
 */
class CatalogProductAttributeUpdateBefore implements \Magento\Framework\Event\ObserverInterface
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
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
     */
    protected $configurableProductType;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Walkthechat\Walkthechat\Model\QueueService
     */
    protected $queueService;

    /**
     * CatalogProductSaveAfter constructor.
     *
     * @param \Walkthechat\Walkthechat\Api\Data\QueueInterfaceFactory                        $queueFactory
     * @param \Walkthechat\Walkthechat\Model\QueueRepository                                 $queueRepository
     * @param \Walkthechat\Walkthechat\Helper\Data                                           $helper
     * @param \Magento\Framework\Registry                                                $registry
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProductType
     * @param \Magento\Catalog\Api\ProductRepositoryInterface                            $productRepository
     * @param \Walkthechat\Walkthechat\Model\QueueService                                    $queueService
     */
    public function __construct(
        \Walkthechat\Walkthechat\Api\Data\QueueInterfaceFactory $queueFactory,
        \Walkthechat\Walkthechat\Model\QueueRepository $queueRepository,
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Magento\Framework\Registry $registry,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProductType,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Walkthechat\Walkthechat\Model\QueueService $queueService
    ) {
        $this->queueFactory            = $queueFactory;
        $this->queueRepository         = $queueRepository;
        $this->helper                  = $helper;
        $this->registry                = $registry;
        $this->configurableProductType = $configurableProductType;
        $this->productRepository       = $productRepository;
        $this->queueService            = $queueService;
    }

    /**
     * Add items to queue when mass attributes update is executed
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isEnabledProductSync()) {
			$productIds = $observer->getProductIds();
			
			foreach ($productIds as $id) {
				$product = $this->productRepository->getById($id);
                $walkTheChatId = $this->helper->getWalkTheChatAttributeValue($product);

                if (!$this->registry->registry('walkthechat_omit_update_action')) {
                    // add main product to queue
                    if ($walkTheChatId) {
                        $this->addProductToQueue($product->getId(), $walkTheChatId);
                    }

                    // if product is a child of exported configurable product - add parent to queue
                    if (in_array($product->getTypeId(), [
                        \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
                        \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
                    ])) {
                        foreach ($this->configurableProductType->getParentIdsByChild($product->getId()) as $parentId) {
                            $parent = $this->productRepository->getById($parentId);

                            $parentWalkTheChatId = $this->helper->getWalkTheChatAttributeValue($parent);

                            if ($parentWalkTheChatId) {
                                $this->addProductToQueue($parentId, $parentWalkTheChatId);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Add product to queue
     *
     * @param int $productId
     * @param int $walkTheChatId
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    protected function addProductToQueue($productId, $walkTheChatId)
    {
        if (!$this->queueService->isDuplicate(
            $productId,
            \Walkthechat\Walkthechat\Model\Action\Update::ACTION,
            'product_id'
        )) {
            /** @var \Walkthechat\Walkthechat\Api\Data\QueueInterface $model */
            $model = $this->queueFactory->create();

            $model->setProductId($productId);
            $model->setWalkthechatId($walkTheChatId);
            $model->setAction(\Walkthechat\Walkthechat\Model\Action\Update::ACTION);

            $this->queueRepository->save($model);
        }
    }
}

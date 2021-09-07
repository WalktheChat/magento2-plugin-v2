<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Setup;

/**
 * Class UpgradeData
 *
 * @package Walkthechat\Walkthechat\Setup
 */
class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
	/**
     * @var \Walkthechat\Walkthechat\Model\ProductService
     */
	protected $productService;
	
	/**
     * @var \Walkthechat\Walkthechat\Api\ProductRepositoryInterface
     */
    protected $syncProductRepository;
    
	/**
     * @var \Walkthechat\Walkthechat\Api\Data\ProductInterfaceFactory
     */
	protected $syncProductFactory;
	
	/**
     * @var \Walkthechat\Walkthechat\Model\QueueService
     */
    protected $queueService;

    /**
     * UpgradeData constructor.
     *
     * @param \Walkthechat\Walkthechat\Model\ProductService $productService
	 * @param \Walkthechat\Walkthechat\Api\ProductRepositoryInterface $syncProductRepository
	 * @param \Walkthechat\Walkthechat\Api\Data\ProductInterfaceFactory $syncProductFactory
	 * @param \Walkthechat\Walkthechat\Model\QueueService $queueService
	 */
	public function __construct(
		\Walkthechat\Walkthechat\Model\ProductService $productService,
		\Walkthechat\Walkthechat\Api\ProductRepositoryInterface $syncProductRepository,
		\Walkthechat\Walkthechat\Api\Data\ProductInterfaceFactory $syncProductFactory,
		\Walkthechat\Walkthechat\Model\QueueService $queueService
	) {
		$this->productService        = $productService;
		$this->syncProductRepository = $syncProductRepository;
        $this->syncProductFactory    = $syncProductFactory;
        $this->queueService          = $queueService;
	}

	/**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface   $context
     *
     * @return void
     */
	public function upgrade(
		\Magento\Framework\Setup\ModuleDataSetupInterface $setup, 
		\Magento\Framework\Setup\ModuleContextInterface $context
	) {
	    if (version_compare($context->getVersion(), '1.6.1', '<')) {
	        $this->queueService->deleteNotExisting();
	    }
	    
		if (version_compare($context->getVersion(), '1.6.2', '<')) {
			foreach ($this->productService->getSyncedProducts()->getItems() as $product) {
				try {
					$syncProduct = $this->syncProductRepository->getByProductId($product->getId());
				} catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
					$syncProduct = $this->syncProductFactory->create();
					$syncProduct->setProductId($product->getId());
				}
				
				$syncProduct->setMessage('');
				$syncProduct->setStatus(\Walkthechat\Walkthechat\Api\Data\ProductInterface::COMPLETE_STATUS);
				
				$this->syncProductRepository->save($syncProduct);
			}
			
			foreach ($this->queueService->getNotProcessed() as $item) {
				if (!$item->getProductId()) continue;
				
				try {
					$syncProduct = $this->syncProductRepository->getByProductId($item->getProductId());
				} catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
					$syncProduct = $this->syncProductFactory->create();
					$syncProduct->setProductId($item->getProductId());
				}
				
				if ($item->getAction() == \Walkthechat\Walkthechat\Model\Action\Delete::ACTION) {
					$syncProduct->setMessage('Deleting data');
				} elseif ($item->getAction() == \Walkthechat\Walkthechat\Model\Action\Add::ACTION) {
					$syncProduct->setMessage('Adding data');
				} else {
					$syncProduct->setMessage('Updating data');
				}
				
				$syncProduct->setStatus(\Walkthechat\Walkthechat\Api\Data\ProductInterface::QUEUE_STATUS);
				
				$this->syncProductRepository->save($syncProduct);
			}
		}
	}
}

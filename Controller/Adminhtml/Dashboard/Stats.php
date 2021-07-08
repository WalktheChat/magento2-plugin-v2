<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */
namespace Walkthechat\Walkthechat\Controller\Adminhtml\Dashboard;

/**
 * Class Stats
 * @package Walkthechat\Walkthechat\Controller\Adminhtml\Dashboard
 */
class Stats extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Walkthechat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * @var \Walkthechat\Walkthechat\Model\QueueService
     */
    protected $queueService;

    /**
     * @var \Walkthechat\Walkthechat\Model\ProductService
     */
    protected $productService;

    /**
     * @var \Walkthechat\Walkthechat\Model\ImageService
     */
    protected $imageService;

    /**
     * Stats constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Walkthechat\Walkthechat\Helper\Data $helper
     * @param \Walkthechat\Walkthechat\Model\QueueService $queueService
     * @param \Walkthechat\Walkthechat\Model\ProductService $productService
     * @param \Walkthechat\Walkthechat\Model\ImageService $imageService
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Walkthechat\Walkthechat\Model\QueueService $queueService,
        \Walkthechat\Walkthechat\Model\ProductService $productService,
        \Walkthechat\Walkthechat\Model\ImageService $imageService
    ) {
        $this->jsonEncoder      = $jsonEncoder;
        $this->helper           = $helper;
        $this->queueService     = $queueService;
        $this->productService   = $productService;
        $this->imageService     = $imageService;

        parent::__construct($context);
    }

    public function execute()
    {
        $syncedProducts = $this->productService->getSyncedProducts()->getTotalCount();

        $response = [
            'resync_status' => $this->helper->getInventorySyncStatus() ? __('Processing') : __('Idle'),
            'synced_products' => $syncedProducts,
            'exported_products' => $syncedProducts + $this->queueService->countNewNotProcessed(),
            'synced_images' => $this->imageService->getExportedImages()->getTotalCount(),
            'exported_images' => $this->imageService->getSyncedImages()->getTotalCount()
        ];

        $this->getResponse()->representJson($this->jsonEncoder->encode($response));
    }
}

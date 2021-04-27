<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */
namespace Walkthechat\Walkthechat\Block\Adminhtml\Dashboard;

/**
 * Class Stats
 * @package Walkthechat\Walkthechat\Block\Adminhtml\Dashboard
 */
class Stats extends \Magento\Backend\Block\Template {
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'stats.phtml';

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
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Walkthechat\Walkthechat\Model\QueueService $queueService
     * @param \Walkthechat\Walkthechat\Model\ProductService $productService
     * @param \Walkthechat\Walkthechat\Model\ImageService $imageService
     * @param array $data
     * @param \Magento\Framework\Json\Helper\Data|null $jsonHelper
     * @param \Magento\Directory\Helper\Data|null $directoryHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Walkthechat\Walkthechat\Model\QueueService $queueService,
        \Walkthechat\Walkthechat\Model\ProductService $productService,
        \Walkthechat\Walkthechat\Model\ImageService $imageService,
        array $data = [],
        \Magento\Framework\Json\Helper\Data $jsonHelper = null,
        \Magento\Directory\Helper\Data $directoryHelper = null
    ) {
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);

        $this->queueService   = $queueService;
        $this->productService = $productService;
        $this->imageService   = $imageService;
    }

    /**
     * @return mixed
     */
    public function getExportedProducts()
    {
        return $this->getSyncedProducts() + $this->queueService->countNewNotProcessed();
    }

    /**
     * @return mixed
     */
    public function getSyncedProducts()
    {
        return $this->productService->getSyncedProducts()->getTotalCount();
    }

    /**
     * @return mixed
     */
    public function getExportedImages()
    {
        return $this->imageService->getExportedImages()->getTotalCount();
    }

    /**
     * @return mixed
     */
    public function getSyncedImages()
    {
        return $this->imageService->getSyncedImages()->getTotalCount();
    }
}

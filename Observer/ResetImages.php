<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Observer;

/**
 * Class ResetImages
 *
 * @package Walkthechat\Walkthechat\Observer
 */
class ResetImages implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Walkthechat\Walkthechat\Model\ImageService
     */
    protected $imageService;

    /**
     * ResetImages constructor.
     *
     * @param  \Walkthechat\Walkthechat\Model\ImageService $imageService
     */
    public function __construct(\Walkthechat\Walkthechat\Model\ImageService $imageService) {
        $this->imageService = $imageService;
    }

    /**
     * Reset product images
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();
        
        $this->imageService->resetImagesData($product);
    }
}

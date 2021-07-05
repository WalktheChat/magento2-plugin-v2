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
 * Class ResyncInventory
 *
 * @package Walkthechat\Walkthechat\Observer
 */
class ResyncInventory implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Walkthechat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * ResyncInventory constructor.
     *
     * @param  \Walkthechat\Walkthechat\Helper\Data $helper
     */
    public function __construct(\Walkthechat\Walkthechat\Helper\Data $helper) {
        $this->helper = $helper;
    }

    /**
     * Init inventory resync
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->helper->setInventorySyncStatus(\Walkthechat\Walkthechat\Api\Data\InventoryInterface::STATUS_GENERATE);
    }
}

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
 * Class QuoteSubmitBefore
 *
 * @package Walkthechat\Walkthechat\Observer
 */
class QuoteSubmitBefore implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getQuote();
        $order = $observer->getOrder();
        
        $order->setData(\Walkthechat\Walkthechat\Helper\Data::ATTRIBUTE_CODE, $quote->getData(\Walkthechat\Walkthechat\Helper\Data::ATTRIBUTE_CODE));
        $order->setData(\Walkthechat\Walkthechat\Helper\Data::ATTRIBUTE_NAME_CODE, $quote->getData(\Walkthechat\Walkthechat\Helper\Data::ATTRIBUTE_NAME_CODE));
        $order->setData('walkthechat_customer_id_number', $quote->getData('walkthechat_customer_id_number'));
        $order->setData('walkthechat_customer_id_name', $quote->getData('walkthechat_customer_id_name'));
    }
}

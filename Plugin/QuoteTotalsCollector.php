<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 * @license   See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Walkthechat\Walkthechat\Plugin;

/**
 * Class QuoteTotalsCollector
 * @package Walkthechat\Walkthechat\Model\Plugin
 */
class QuoteTotalsCollector
{
    /**
     * QuoteTotalsCollector constructor.
     *
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(\Magento\Framework\Registry $registry) {
        $this->registry = $registry;
    }
    
    /**
     * @param \Magento\Quote\Model\Quote\TotalsCollector $subject
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address $address
     */
    public function beforeCollectAddressTotals(
        \Magento\Quote\Model\Quote\TotalsCollector $subject,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Model\Quote\Address $address
        ): void {
        if (!is_null($this->registry->registry('walkthechat_order_import'))) {
            $address->setData('amastyFreeGiftProcessed', true);
        }
    }
}

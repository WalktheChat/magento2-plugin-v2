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
 * Class AccessChangeQuoteControl
 * @package Walkthechat\Walkthechat\Model\Plugin
 */
class AccessChangeQuoteControl
{
    /**
     * @var \Magento\Quote\Api\ChangeQuoteControlInterface $changeQuoteControl
     */
    private $changeQuoteControl;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Quote\Api\ChangeQuoteControlInterface $changeQuoteControl
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Quote\Api\ChangeQuoteControlInterface $changeQuoteControl,
        \Magento\Framework\Registry $registry
    )
    {
        $this->changeQuoteControl   = $changeQuoteControl;
        $this->registry             = $registry;
    }

    /**
     * Checks if change quote's customer id is allowed for current user.
     *
     * A StateException is thrown if Guest's or Customer's customer_id not match user_id or unknown user type
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $subject
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     */
    public function beforeSave(\Magento\Quote\Api\CartRepositoryInterface $subject, \Magento\Quote\Api\Data\CartInterface $quote): void
    {
        if (!$this->registry->registry('walkthechat_order_import') && !$this->changeQuoteControl->isAllowed($quote)) {
            throw new \Magento\Framework\Exception\StateException(__("Invalid state change requested"));
        }
    }
}

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
     * @param \Magento\Quote\Api\ChangeQuoteControlInterface $changeQuoteControl
     */
    public function __construct(\Magento\Quote\Api\ChangeQuoteControlInterface $changeQuoteControl)
    {
        $this->changeQuoteControl = $changeQuoteControl;
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
        if (!$quote->getWalkthechatId() && !$this->changeQuoteControl->isAllowed($quote)) {
            throw new \Magento\Framework\Exception\StateException(__("Invalid state change requested"));
        }
    }
}

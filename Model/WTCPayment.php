<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model;

/**
 * Class WTCPaymentMethod
 *
 * @package Walkthechat\Walkthechat\Model
 */
class WTCPayment extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'walkthechat';

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;

    /**
     * @var string
     */
    protected $_infoBlockType = \Walkthechat\Walkthechat\Block\Payment\Info::class;

    /**
     * {@inheritdoc}
     *
     * Allow only for import request
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        return (bool)$this->_registry->registry('walkthechat_payment_and_shipping_available');
    }
}

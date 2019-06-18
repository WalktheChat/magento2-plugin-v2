<?php
/**
 * @package   WalktheChat\Walkthechat
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Model;

/**
 * Class WTCPaymentMethod
 *
 * @package WalktheChat\Walkthechat\Model
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
     * {@inheritdoc}
     *
     * Allow only for import request
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        return (bool)$this->_registry->registry('walkthechat_payment_and_shipping_available');
    }
}

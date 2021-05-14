<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Block\Payment;

/**
 * Class Info
 * @package Walkthechat\Walkthechat\Block\Payment
 */
class Info extends \Magento\Payment\Block\Info
{
    /**
     * @var string
     */
    protected $_template = 'Walkthechat_Walkthechat::payment/info.phtml';

    /**
     * @return string
     */
    public function getProvider()
    {
        return $this->getInfo()->getAdditionalInformation('provider');
    }

    /**
     * @return string
     */
    public function getVendor()
    {
        return $this->getInfo()->getAdditionalInformation('vendor');
    }
}

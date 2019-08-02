<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Block\Adminhtml\System\Config;

/**
 * Class Action
 *
 * @package Walkthechat\Walkthechat\Block\Adminhtml\System\Config
 */
class Action extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var string
     */
    protected $_template = 'Walkthechat_Walkthechat::system/config/action.phtml';

    /**
     * @var \Walkthechat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * {@inheritdoc}
     *
     * @param \Walkthechat\Walkthechat\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Walkthechat\Walkthechat\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * @return boolean
     */
    public function isConnected()
    {
        return $this->helper->isConnected();
    }

    /**
     * @return boolean
     */
    public function canConnect()
    {
        return $this->helper->canConnect();
    }

    /**
     * @return string
     */
    public function getConnectUrl()
    {
        return $this->_urlBuilder->getUrl('walkthechat/auth/redirect');
    }

    /**
     * @return string
     */
    public function getDisconnectUrl()
    {
        return $this->_urlBuilder->getUrl('walkthechat/auth/disconnect');
    }
}

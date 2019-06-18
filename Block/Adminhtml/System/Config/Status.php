<?php
/**
 * @package   WalktheChat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Block\Adminhtml\System\Config;

/**
 * Class Status
 *
 * @package WalktheChat\Walkthechat\Block\Adminhtml\System\Config
 */
class Status extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var string
     */
    protected $_template = 'WalktheChat_Walkthechat::system/config/status.phtml';

    /**
     * @var \WalktheChat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * {@inheritdoc}
     *
     * @param \WalktheChat\Walkthechat\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \WalktheChat\Walkthechat\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($context, $data);
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
        return $this->helper->isConnected() && $this->helper->getProjectId();
    }
}

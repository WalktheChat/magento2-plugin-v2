<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Controller\Adminhtml\Auth;

/**
 * Class Redirect
 *
 * @package Walkthechat\Walkthechat\Controller\Adminhtml\Auth
 */
class Redirect extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Walkthechat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * {@inheritdoc}
     *
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Walkthechat\Walkthechat\Helper\Data            $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Walkthechat\Walkthechat\Helper\Data $helper
    ) {
        $this->resultFactory = $resultFactory;
        $this->helper        = $helper;

        parent::__construct($context);
    }

    /**
     * Redirect to Walkthechat in order to connect with app
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultInstance */
        $resultInstance = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);

        $resultInstance->setUrl($this->helper->getAuthUrl());

        return $resultInstance;
    }
}

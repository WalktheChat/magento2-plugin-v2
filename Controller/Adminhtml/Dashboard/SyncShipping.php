<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Controller\Adminhtml\Dashboard;

/**
 * Class SyncShipping
 *
 * @package Walkthechat\Walkthechat\Controller\Adminhtml\Dashboard
 */
class SyncShipping extends \Magento\Backend\App\Action
{
    /**
     * @var \Walkthechat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * @var \Walkthechat\Walkthechat\Model\ShippingService
     */
    protected $shippingService;

    /**
     * {@inheritdoc}
     *
     * @param \Walkthechat\Walkthechat\Helper\Data           $helper
     * @param \Walkthechat\Walkthechat\Model\ShippingService $shippingService
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Walkthechat\Walkthechat\Model\ShippingService $shippingService
    ) {
        $this->helper          = $helper;
        $this->shippingService = $shippingService;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if ($this->helper->isTableRateActive()) {
            $this->shippingService->sync();

            $this->messageManager->addSuccessMessage(__('Shipping Synced.'));
        } else {
            $this->messageManager->addErrorMessage(__('Table Rate is disabled.'));
        }

        $this->_redirect('*/*/index');
    }
}

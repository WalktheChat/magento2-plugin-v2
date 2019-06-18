<?php
/**
 * @package   WalktheChat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Controller\Adminhtml\Dashboard;

/**
 * Class SyncShipping
 *
 * @package WalktheChat\Walkthechat\Controller\Adminhtml\Dashboard
 */
class SyncShipping extends \Magento\Backend\App\Action
{
    /**
     * @var \WalktheChat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * @var \WalktheChat\Walkthechat\Model\ShippingService
     */
    protected $shippingService;

    /**
     * {@inheritdoc}
     *
     * @param \WalktheChat\Walkthechat\Helper\Data           $helper
     * @param \WalktheChat\Walkthechat\Model\ShippingService $shippingService
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \WalktheChat\Walkthechat\Helper\Data $helper,
        \WalktheChat\Walkthechat\Model\ShippingService $shippingService
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

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
 * Class Confirm
 *
 * @package Walkthechat\Walkthechat\Controller\Adminhtml\Auth
 */
class Confirm extends \Magento\Backend\App\Action
{
    /**
     * @var \Walkthechat\Walkthechat\Service\AuthorizeRepository
     */
    protected $authorizeRepository;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * {@inheritdoc}
     *
     * @param \Magento\Framework\App\RequestInterface          $request
     * @param \Walkthechat\Walkthechat\Service\AuthorizeRepository $authorizeRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Walkthechat\Walkthechat\Service\AuthorizeRepository $authorizeRepository
    ) {
        $this->request             = $request;
        $this->authorizeRepository = $authorizeRepository;

        parent::__construct($context);
    }

    /**
     * Get token from Walkthechat
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $code = $this->request->getParam('code');

        try {
            $this->authorizeRepository->authorize($code);

            $this->messageManager->addSuccessMessage(__('App connected.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $this->_redirect('adminhtml/system_config/edit/section/walkthechat_settings');
    }
}

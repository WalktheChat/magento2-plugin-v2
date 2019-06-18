<?php
/**
 * @package   WalktheChat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Controller\Adminhtml\Logs;

/**
 * Class Details
 *
 * @package WalktheChat\Walkthechat\Controller\Adminhtml\Logs
 */
class Details extends \Magento\Backend\App\Action
{
    /**
     * Display dashboard
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);

        return $resultPage;
    }
}

<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Controller\Adminhtml\Auth;

/**
 * Class Disconnect
 *
 * @package Walkthechat\Walkthechat\Controller\Adminhtml\Auth
 */
class Disconnect extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * {@inheritdoc}
     *
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\App\Cache\TypeListInterface        $cacheTypeList
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
    ) {
        $this->configWriter  = $configWriter;
        $this->cacheTypeList = $cacheTypeList;

        parent::__construct($context);
    }

    /**
     * Disconnect
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->configWriter->delete('walkthechat_settings/general/token');
        $this->cacheTypeList->cleanType('config');
        $this->messageManager->addSuccessMessage(__('App was disconnected.'));

        $this->_redirect('adminhtml/system_config/edit/section/walkthechat_settings');
    }
}

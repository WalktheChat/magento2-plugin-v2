<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Controller\Adminhtml\Product;

/**
 * Class Inventory
 *
 * @package Walkthechat\Walkthechat\Controller\Adminhtml\Product
 */
class Inventory extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     *
     * Delete all existing products from Walkthechat
     */
    public function execute()
    {
        $this->_eventManager->dispatch('walkthechat_resync_inventory');

        $this->messageManager->addSuccessMessage(__('Inventory resync initiated'));

        $this->_redirect('*/dashboard/products');
    }
}

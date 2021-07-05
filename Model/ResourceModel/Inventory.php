<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model\ResourceModel;

/**
 * Class ImageSync
 *
 * @package Walkthechat\Walkthechat\Model\ResourceModel
 */
class Inventory extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Table name
     */
    const TABLE_NAME = 'wtc_walkthechat_inventory';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, \Walkthechat\Walkthechat\Api\Data\InventoryInterface::ID);
    }
}

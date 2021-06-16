<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model\ResourceModel;

/**
 * Class Order
 *
 * @package Walkthechat\Walkthechat\Model\ResourceModel
 */
class Order extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Table name
     *
     * @var string
     */
    const TABLE_NAME = 'wtc_walkthechat_order';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, \Walkthechat\Walkthechat\Api\Data\OrderInterface::ID);
    }
}

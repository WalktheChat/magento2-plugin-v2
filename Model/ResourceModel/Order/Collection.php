<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model\ResourceModel\Order;

/**
 * Class Collection
 *
 * @package Walkthechat\Walkthechat\Model\ResourceModel\Queue
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Walkthechat\Walkthechat\Model\Order::class, \Walkthechat\Walkthechat\Model\ResourceModel\Order::class);
    }
}

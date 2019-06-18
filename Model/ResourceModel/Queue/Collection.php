<?php
/**
 * @package   WalktheChat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Model\ResourceModel\Queue;

/**
 * Class Collection
 *
 * @package WalktheChat\Walkthechat\Model\ResourceModel\Queue
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
        $this->_init(\WalktheChat\Walkthechat\Model\Queue::class, \WalktheChat\Walkthechat\Model\ResourceModel\Queue::class);
    }
}

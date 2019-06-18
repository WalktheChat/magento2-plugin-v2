<?php
/**
 * @package   WalktheChat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Model\ResourceModel\ImageSync;

/**
 * Class Collection
 *
 * @package WalktheChat\Walkthechat\Model\ResourceModel\ImageSync
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(
            \WalktheChat\Walkthechat\Model\ImageSync::class,
            \WalktheChat\Walkthechat\Model\ResourceModel\ImageSync::class
        );
    }
}

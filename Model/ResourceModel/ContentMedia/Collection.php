<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model\ResourceModel\ContentMedia;

/**
 * Class Collection
 *
 * @package Walkthechat\Walkthechat\Model\ResourceModel\ContentMedia
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
            \Walkthechat\Walkthechat\Model\ContentMedia::class,
            \Walkthechat\Walkthechat\Model\ResourceModel\ContentMedia::class
        );
    }
}

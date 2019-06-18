<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model\ResourceModel\ImageSync;

/**
 * Class Collection
 *
 * @package Walkthechat\Walkthechat\Model\ResourceModel\ImageSync
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
            \Walkthechat\Walkthechat\Model\ImageSync::class,
            \Walkthechat\Walkthechat\Model\ResourceModel\ImageSync::class
        );
    }
}

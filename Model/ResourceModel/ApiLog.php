<?php
/**
 * @package   WalktheChat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Model\ResourceModel;

/**
 * Class ApiLog
 *
 * @package WalktheChat\Walkthechat\Model\ResourceModel
 */
class ApiLog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Logs table
     *
     * @var string
     */
    const TABLE_NAME = 'wtc_walkthechat_api_log';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, \WalktheChat\Walkthechat\Api\Data\ApiLogInterface::ENTITY_ID_FIELD);
    }

    /**
     * {@inheritdoc}
     *
     * Add order DESC for all requests
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        /** @var $select \Magento\Framework\DB\Select */
        $select = parent::_getLoadSelect($field, $value, $object);

        $select->order('created_at '.\Magento\Framework\DB\Select::SQL_DESC);

        return $select;
    }
}

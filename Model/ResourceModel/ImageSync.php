<?php
/**
 * @package   WalktheChat\Walkthechat
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Model\ResourceModel;

/**
 * Class ImageSync
 *
 * @package WalktheChat\Walkthechat\Model\ResourceModel
 */
class ImageSync extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Table name
     */
    const TABLE_NAME = 'wtc_walkthechat_image_sync';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, \WalktheChat\Walkthechat\Api\Data\ImageSyncInterface::ID);
    }
}

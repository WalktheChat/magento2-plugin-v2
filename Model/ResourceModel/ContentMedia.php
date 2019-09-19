<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model\ResourceModel;

/**
 * Class ContentMedia
 *
 * @package Walkthechat\Walkthechat\Model\ResourceModel
 */
class ContentMedia extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Table name
     */
    const TABLE_NAME = 'wtc_walkthechat_content_media_sync';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, \Walkthechat\Walkthechat\Api\Data\ContentMediaInterface::ID);
    }
}

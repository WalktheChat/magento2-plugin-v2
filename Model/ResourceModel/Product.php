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
 * Class Product
 *
 * @package Walkthechat\Walkthechat\Model\ResourceModel
 */
class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Table name
     *
     * @var string
     */
    const TABLE_NAME = 'wtc_walkthechat_product';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, \Walkthechat\Walkthechat\Api\Data\ProductInterface::ID);
    }
}

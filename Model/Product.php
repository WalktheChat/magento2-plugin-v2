<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model;

/**
 * Class Product
 *
 * @package Walkthechat\Walkthechat\Model
 */
class Product extends \Magento\Framework\Model\AbstractModel implements \Walkthechat\Walkthechat\Api\Data\ProductInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(\Walkthechat\Walkthechat\Model\ResourceModel\Product::class);
    }

    /**
     * @inheritdoc}
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @inheritdoc}
     */
    public function setProductId($id)
    {
        return $this->setData(self::PRODUCT_ID, $id);
    }

    /**
     * @inheritdoc}
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * @inheritdoc}
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * @inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritdoc}
     */
    public function setCreatedAt($gsmDate)
    {
        return $this->setData(self::CREATED_AT, $gsmDate);
    }

    /**
     * @inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritdoc}
     */
    public function setUpdatedAt($gsmDate)
    {
        return $this->setData(self::UPDATED_AT, $gsmDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }
}

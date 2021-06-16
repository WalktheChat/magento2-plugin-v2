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
 * Class Order
 *
 * @package Walkthechat\Walkthechat\Model
 */
class Order extends \Magento\Framework\Model\AbstractModel implements \Walkthechat\Walkthechat\Api\Data\OrderInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(\Walkthechat\Walkthechat\Model\ResourceModel\Order::class);
    }

    /**
     * @inheritdoc}
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @inheritdoc}
     */
    public function setOrderId($id)
    {
        return $this->setData(self::ORDER_ID, $id);
    }

    /**
     * @inheritdoc}
     */
    public function getWalkthechatId()
    {
        return $this->getData(self::WALKTHECHAT_ID);
    }

    /**
     * @inheritdoc}
     */
    public function setWalkthechatId($id)
    {
        return $this->setData(self::WALKTHECHAT_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getWalkthechatName()
    {
        return $this->getData(self::WALKTHECHAT_NAME);
    }

    /**
     * @inheritdoc}
     */
    public function setWalkthechatName($name)
    {
        return $this->setData(self::WALKTHECHAT_NAME, $name);
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

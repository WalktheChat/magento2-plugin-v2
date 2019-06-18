<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model;

/**
 * Class Queue
 *
 * @package Walkthechat\Walkthechat\Model
 */
class Queue extends \Magento\Framework\Model\AbstractModel implements \Walkthechat\Walkthechat\Api\Data\QueueInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(\Walkthechat\Walkthechat\Model\ResourceModel\Queue::class);
    }

    /**
     * {@inheritdoc}
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
     * @inheritdoc}
     */
    public function getAction()
    {
        return $this->getData(self::ACTION);
    }

    /**
     * @inheritdoc}
     */
    public function setAction($action)
    {
        return $this->setData(self::ACTION, $action);
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
    public function getProcessedAt()
    {
        return $this->getData(self::PROCESSED_AT);
    }

    /**
     * @inheritdoc}
     */
    public function setProcessedAt($gsmDate)
    {
        return $this->setData(self::PROCESSED_AT, $gsmDate);
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

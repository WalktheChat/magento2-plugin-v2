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
class Inventory extends \Magento\Framework\Model\AbstractModel implements \Walkthechat\Walkthechat\Api\Data\InventoryInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(\Walkthechat\Walkthechat\Model\ResourceModel\Inventory::class);
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
    public function getQty()
    {
        return $this->getData(self::QTY);
    }

    /**
     * @inheritdoc}
     */
    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }
    
    /**
     * @inheritdoc}
     */
    public function getVisibility()
    {
        return $this->getData(self::VISIBILITY);
    }
    
    /**
     * @inheritdoc}
     */
    public function setVisibility($visibility)
    {
        return $this->setData(self::VISIBILITY, $visibility);
    }
    
    /**
     * @inheritdoc}
     */
    public function getVariantVisibility()
    {
        return $this->getData(self::VARIANT_VISIBILITY);
    }
    
    /**
     * @inheritdoc}
     */
    public function setVariantVisibility($visibility)
    {
        return $this->setData(self::VARIANT_VISIBILITY, $visibility);
    }
}

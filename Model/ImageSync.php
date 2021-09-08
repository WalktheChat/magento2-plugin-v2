<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model;

/**
 * Class ImageSync
 *
 * @package Walkthechat\Walkthechat\Model
 */
class ImageSync extends \Magento\Framework\Model\AbstractModel
    implements \Walkthechat\Walkthechat\Api\Data\ImageSyncInterface
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Walkthechat\Walkthechat\Model\ResourceModel\ImageSync::class);
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
    public function getImageId()
    {
        return $this->getData(self::IMAGE_ID);
    }

    /**
     * @inheritdoc}
     */
    public function setImageId($id)
    {
        return $this->setData(self::IMAGE_ID, $id);
    }

    /**
     * @inheritdoc}
     */
    public function getImageData()
    {
        return $this->getData(self::IMAGE_DATA);
    }

    /**
     * @inheritdoc}
     */
    public function setImageData($imageData)
    {
        return $this->setData(self::IMAGE_DATA, $imageData);
    }
    
    /**
     * @inheritdoc}
     */
    public function getImageUrl()
    {
        return $this->getData(self::IMAGE_URL);
    }
    
    /**
     * @inheritdoc}
     */
    public function setImageUrl($imageUrl)
    {
        return $this->setData(self::IMAGE_URL, $imageUrl);
    }
}

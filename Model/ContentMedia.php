<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model;

/**
 * Class ContentMedia
 *
 * @package Walkthechat\Walkthechat\Model
 */
class ContentMedia extends \Magento\Framework\Model\AbstractModel
    implements \Walkthechat\Walkthechat\Api\Data\ContentMediaInterface
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Walkthechat\Walkthechat\Model\ResourceModel\ContentMedia::class);
    }

    /**
     * @inheritdoc}
     */
    public function getImagePath()
    {
        return $this->getData(self::IMAGE_PATH);
    }

    /**
     * @inheritdoc}
     */
    public function setImagePath($path)
    {
        return $this->setData(self::IMAGE_PATH, $path);
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
}

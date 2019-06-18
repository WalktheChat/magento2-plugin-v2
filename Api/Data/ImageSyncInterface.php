<?php
/**
 * @package   WalktheChat\Walkthechat
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Api\Data;

/**
 * Interface ImageSyncInterface
 *
 * @package WalktheChat\Walkthechat\Api\Data
 */
interface ImageSyncInterface
{
    /**@#+
     * Fields
     */
    const ID         = 'entity_id';
    const PRODUCT_ID = 'product_id';
    const IMAGE_ID   = 'image_id';
    const IMAGE_DATA = 'image_data';
    /**@#- */

    /**
     * Return ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     *
     * @return \WalktheChat\Walkthechat\Api\Data\ImageSyncInterface
     */
    public function setId($id);

    /**
     * Return product ID
     *
     * @return int
     */
    public function getProductId();

    /**
     * Set product ID
     *
     * @param int $id
     *
     * @return \WalktheChat\Walkthechat\Api\Data\ImageSyncInterface
     */
    public function setProductId($id);

    /**
     * Return image ID
     *
     * @return int
     */
    public function getImageId();

    /**
     * Set image ID
     *
     * @param int $id
     *
     * @return \WalktheChat\Walkthechat\Api\Data\ImageSyncInterface
     */
    public function setImageId($id);

    /**
     * Return image data
     *
     * @return string
     */
    public function getImageData();

    /**
     * Set image data
     *
     * @param string $imageData
     *
     * @return \WalktheChat\Walkthechat\Api\Data\ImageSyncInterface
     */
    public function setImageData($imageData);
}

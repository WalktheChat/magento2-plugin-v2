<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Api\Data;

/**
 * Interface ContentMediaInterface
 *
 * @package Walkthechat\Walkthechat\Api\Data
 */
interface ContentMediaInterface
{
    /**@#+
     * Fields
     */
    const ID         = 'entity_id';
    const IMAGE_PATH = 'image_path';
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
     * @return \Walkthechat\Walkthechat\Api\Data\ContentMediaInterface
     */
    public function setId($id);

    /**
     * Return image path
     *
     * @return int
     */
    public function getImagePath();

    /**
     * Set image path
     *
     * @param string $imagePath
     *
     * @return \Walkthechat\Walkthechat\Api\Data\ContentMediaInterface
     */
    public function setImagePath($imagePath);

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
     * @return \Walkthechat\Walkthechat\Api\Data\ContentMediaInterface
     */
    public function setImageData($imageData);
}

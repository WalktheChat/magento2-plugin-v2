<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Api\Data;

/**
 * Interface ProductInterface
 *
 * @package Walkthechat\Walkthechat\Api\Data
 */
interface ProductInterface
{
    /**@#+
     * Fields
     */
    const ID               = 'entity_id';
    const PRODUCT_ID       = 'product_id';
    const CREATED_AT       = 'created_at';
    const UPDATED_AT       = 'updated_at';
    const STATUS           = 'status';
    const MESSAGE          = 'message';
    /**@#- */

    /**@#+
     * Statuses
     */
    const QUEUE_STATUS        = 0;
    const COMPLETE_STATUS     = 1;
    const ERROR_STATUS        = 2;
    /**@#- */

    /**
     * Return entity_id
     *
     * @return int
     */
    public function getId();

    /**
     * Set entity_id
     *
     * @param int $id
     *
     * @return \Walkthechat\Walkthechat\Api\Data\ProductInterface
     */
    public function setId($id);

    /**
     * Return product_id
     *
     * @return int
     */
    public function getProductId();

    /**
     * Set product_id
     *
     * @param int $id
     *
     * @return \Walkthechat\Walkthechat\Api\Data\ProductInterface
     */
    public function setProductId($id);

    /**
     * Return message
     *
     * @return string
     */
    public function getMessage();

    /**
     * Set message
     *
     * @param string $message
     *
     * @return \Walkthechat\Walkthechat\Api\Data\OrderInterface
     */
    public function setMessage($message);

    /**
     * Return created_at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created_at
     *
     * @param string $gsmDate
     *
     * @return \Walkthechat\Walkthechat\Api\Data\ProductInterface
     */
    public function setCreatedAt($gsmDate);

    /**
     * Return updated_at
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     *
     * @param string $gsmDate
     *
     * @return \Walkthechat\Walkthechat\Api\Data\ProductInterface
     */
    public function setUpdatedAt($gsmDate);

    /**
     * Return status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param int $status
     *
     * @return \Walkthechat\Walkthechat\Api\Data\ProductInterface
     */
    public function setStatus($status);
}

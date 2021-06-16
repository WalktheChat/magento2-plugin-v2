<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Api\Data;

/**
 * Interface OrderInterface
 *
 * @package Walkthechat\Walkthechat\Api\Data
 */
interface OrderInterface
{
    /**@#+
     * Fields
     */
    const ID               = 'entity_id';
    const WALKTHECHAT_ID   = 'walkthechat_id';
    const WALKTHECHAT_NAME = 'walkthechat_name';
    const ORDER_ID         = 'order_id';
    const CREATED_AT       = 'created_at';
    const UPDATED_AT       = 'updated_at';
    const STATUS           = 'status';
    const MESSAGE          = 'message';
    /**@#- */

    /**@#+
     * Statuses
     */
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
     * @return \Walkthechat\Walkthechat\Api\Data\OrderInterface
     */
    public function setId($id);

    /**
     * Return order_id
     *
     * @return int
     */
    public function getOrderId();

    /**
     * Set order_id
     *
     * @param int $id
     *
     * @return \Walkthechat\Walkthechat\Api\Data\OrderInterface
     */
    public function setOrderId($id);

    /**
     * Return walkthechat_id
     *
     * @return int
     */
    public function getWalkthechatId();

    /**
     * Set walkthechat_id
     *
     * @param int $id
     *
     * @return \Walkthechat\Walkthechat\Api\Data\OrderInterface
     */
    public function setWalkthechatId($id);

    /**
     * Return walkthechat_name
     *
     * @return string
     */
    public function getWalkthechatName();

    /**
     * Set walkthechat_name
     *
     * @param string $name
     *
     * @return \Walkthechat\Walkthechat\Api\Data\OrderInterface
     */
    public function setWalkthechatName($name);

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
     * @return \Walkthechat\Walkthechat\Api\Data\OrderInterface
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
     * @return \Walkthechat\Walkthechat\Api\Data\OrderInterface
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
     * @return \Walkthechat\Walkthechat\Api\Data\OrderInterface
     */
    public function setStatus($status);
}

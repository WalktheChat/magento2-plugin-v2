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
 * Interface QueueInterface
 *
 * @package Walkthechat\Walkthechat\Api\Data
 */
interface QueueInterface
{
    /**@#+
     * Fields
     */
    const ID             = 'entity_id';
    const PRODUCT_ID     = 'product_id';
    const ORDER_ID       = 'order_id';
    const WALKTHECHAT_ID = 'walkthechat_id';
    const ACTION         = 'action';
    const CREATED_AT     = 'created_at';
    const PROCESSED_AT   = 'processed_at';
    const STATUS         = 'status';
    /**@#- */

    /**@#+
     * Statuses
     */
    const WAITING_IN_QUEUE_STATUS = 0;
    const COMPLETE_STATUS         = 1;
    const API_ERROR_STATUS        = 2;
    const INTERNAL_ERROR_STATUS   = 3;
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
     * @return \Walkthechat\Walkthechat\Api\Data\QueueInterface
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
     * @return \Walkthechat\Walkthechat\Api\Data\QueueInterface
     */
    public function setProductId($id);

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
     * @return \Walkthechat\Walkthechat\Api\Data\QueueInterface
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
     * @return \Walkthechat\Walkthechat\Api\Data\QueueInterface
     */
    public function setWalkthechatId($id);

    /**
     * Return action
     *
     * @return string
     */
    public function getAction();

    /**
     * Set action
     *
     * @param string $action
     *
     * @return \Walkthechat\Walkthechat\Api\Data\QueueInterface
     */
    public function setAction($action);

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
     * @return \Walkthechat\Walkthechat\Api\Data\QueueInterface
     */
    public function setCreatedAt($gsmDate);

    /**
     * Return processed_at
     *
     * @return string
     */
    public function getProcessedAt();

    /**
     * Set processed_at
     *
     * @param string $gsmDate
     *
     * @return \Walkthechat\Walkthechat\Api\Data\QueueInterface
     */
    public function setProcessedAt($gsmDate);

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
     * @return \Walkthechat\Walkthechat\Api\Data\QueueInterface
     */
    public function setStatus($status);
}

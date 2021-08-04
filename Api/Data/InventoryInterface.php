<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Api\Data;

/**
 * Interface InventoryInterface
 *
 * @package Walkthechat\Walkthechat\Api\Data
 */
interface InventoryInterface
{
    /**@#+
     * Fields
     */
    const ID                = 'entity_id';
    const PRODUCT_ID        = 'product_id';
    const WALKTHECHAT_ID    = 'walkthechat_id';
    const QTY               = 'qty';
    const VISIBILITY        = 'visibility';
    /**@#- */

    /**@#+
     * Statuses
     */
    const STATUS_IDLE       = 0;
    const STATUS_GENERATE   = 1;
    const STATUS_PROCESS    = 2;
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
     * @return \Walkthechat\Walkthechat\Api\Data\InventoryInterface
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
     * @return \Walkthechat\Walkthechat\Api\Data\InventoryInterface
     */
    public function setProductId($id);

    /**
     * Return Walkthechat ID
     *
     * @return string
     */
    public function getWalkthechatId();

    /**
     * Set Walkthechat ID
     *
     * @param string $id
     *
     * @return \Walkthechat\Walkthechat\Api\Data\InventoryInterface
     */
    public function setWalkthechatId($id);

    /**
     * Return qty
     *
     * @return int
     */
    public function getQty();

    /**
     * Set qty
     *
     * @param int $qty
     *
     * @return \Walkthechat\Walkthechat\Api\Data\InventoryInterface
     */
    public function setQty($qty);
    
    /**
     * Return visibility
     *
     * @return int
     */
    public function getVisibility();
    
    /**
     * Set visibility
     *
     * @param int $visibility
     *
     * @return \Walkthechat\Walkthechat\Api\Data\InventoryInterface
     */
    public function setVisibility($visibility);
}

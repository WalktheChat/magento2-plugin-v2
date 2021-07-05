<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Api;

/**
 * Interface InventoryRepositoryInterface
 *
 * @package Walkthechat\Walkthechat\Api
 */
interface InventoryRepositoryInterface
{
    /**
     * Truncate table
     */
    public function truncate();

    /**
     * Bulk save many entities
     *
     * @param array $data
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function bulkSave(array $data);

    /**
     * Bulk delete many entities
     *
     * @param array $data
     */
    public function bulkDelete(array $data);
}

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
 * Interface InventorySearchResultsInterface
 *
 * @package Walkthechat\Walkthechat\Api\Data
 */
interface InventorySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \Walkthechat\Walkthechat\Api\Data\InventoryInterface[]
     */
    public function getItems();

    /**
     * @param \Walkthechat\Walkthechat\Api\Data\InventoryInterface[] $items
     *
     * @return void
     */
    public function setItems(array $items);
}

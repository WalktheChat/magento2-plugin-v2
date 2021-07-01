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
 * Interface ProductSearchResultsInterface
 *
 * @package Walkthechat\Walkthechat\Api\Data
 */
interface ProductSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \Walkthechat\Walkthechat\Api\Data\ProductInterface[]
     */
    public function getItems();

    /**
     * @param \Walkthechat\Walkthechat\Api\Data\ProductInterface[] $items
     *
     * @return void
     */
    public function setItems(array $items);
}

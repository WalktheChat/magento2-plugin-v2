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
 * Interface ImageSyncSearchResultsInterface
 *
 * @package Walkthechat\Walkthechat\Api\Data
 */
interface ImageSyncSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \Walkthechat\Walkthechat\Api\Data\ImageSyncInterface[]
     */
    public function getItems();

    /**
     * @param \Walkthechat\Walkthechat\Api\Data\ImageSyncInterface[] $items
     *
     * @return void
     */
    public function setItems(array $items);
}

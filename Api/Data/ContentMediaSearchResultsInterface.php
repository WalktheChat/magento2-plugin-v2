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
 * Interface ContentMediaSearchResultsInterface
 *
 * @package Walkthechat\Walkthechat\Api\Data
 */
interface ContentMediaSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \Walkthechat\Walkthechat\Api\Data\ContentMediaInterface[]
     */
    public function getItems();

    /**
     * @param \Walkthechat\Walkthechat\Api\Data\ContentMediaInterface[] $items
     *
     * @return void
     */
    public function setItems(array $items);
}

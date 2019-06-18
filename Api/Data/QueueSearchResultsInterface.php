<?php
/**
 * @package   WalktheChat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Api\Data;

/**
 * Interface QueueSearchResultsInterface
 *
 * @package WalktheChat\Walkthechat\Api\Data
 */
interface QueueSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \WalktheChat\Walkthechat\Api\Data\QueueInterface[]
     */
    public function getItems();

    /**
     * @param \WalktheChat\Walkthechat\Api\Data\QueueInterface[] $items
     *
     * @return void
     */
    public function setItems(array $items);
}

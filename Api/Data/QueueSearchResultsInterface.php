<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Api\Data;

/**
 * Interface QueueSearchResultsInterface
 *
 * @package Walkthechat\Walkthechat\Api\Data
 */
interface QueueSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \Walkthechat\Walkthechat\Api\Data\QueueInterface[]
     */
    public function getItems();

    /**
     * @param \Walkthechat\Walkthechat\Api\Data\QueueInterface[] $items
     *
     * @return void
     */
    public function setItems(array $items);
}

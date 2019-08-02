<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Api;

/**
 * Interface QueueRepositoryInterface
 *
 * @package Walkthechat\Walkthechat\Api
 */
interface QueueRepositoryInterface
{
    /**
     * Return entity by ID
     *
     * @param int $id
     *
     * @return \Walkthechat\Walkthechat\Api\Data\QueueInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Saves entity
     *
     * @param \Walkthechat\Walkthechat\Api\Data\QueueInterface $queue
     *
     * @return \Walkthechat\Walkthechat\Api\Data\QueueInterface
     */
    public function save(\Walkthechat\Walkthechat\Api\Data\QueueInterface $queue);

    /**
     * Remove entity
     *
     * @param \Walkthechat\Walkthechat\Api\Data\QueueInterface $queue
     *
     * @return void
     */
    public function delete(\Walkthechat\Walkthechat\Api\Data\QueueInterface $queue);

    /**
     * Return list of entities
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Walkthechat\Walkthechat\Api\Data\QueueSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Bulk save many entities
     *
     * @param array $data
     *
     * @return \Walkthechat\Walkthechat\Api\Data\QueueSearchResultsInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function bulkSave(array $data);
}

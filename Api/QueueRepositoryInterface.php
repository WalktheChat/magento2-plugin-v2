<?php
/**
 * @package   WalktheChat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Api;

/**
 * Interface QueueRepositoryInterface
 *
 * @package WalktheChat\Walkthechat\Api
 */
interface QueueRepositoryInterface
{
    /**
     * Return entity by ID
     *
     * @param int $id
     *
     * @return \WalktheChat\Walkthechat\Api\Data\QueueInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Saves entity
     *
     * @param \WalktheChat\Walkthechat\Api\Data\QueueInterface $queue
     *
     * @return \WalktheChat\Walkthechat\Api\Data\QueueInterface
     */
    public function save(\WalktheChat\Walkthechat\Api\Data\QueueInterface $queue);

    /**
     * Remove entity
     *
     * @param \WalktheChat\Walkthechat\Api\Data\QueueInterface $queue
     *
     * @return void
     */
    public function delete(\WalktheChat\Walkthechat\Api\Data\QueueInterface $queue);

    /**
     * Return list of entities
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \WalktheChat\Walkthechat\Api\Data\QueueSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Bulk save many entities
     *
     * @param array $data
     *
     * @return \WalktheChat\Walkthechat\Api\Data\QueueSearchResultsInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function bulkSave(array $data);
}

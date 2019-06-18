<?php
/**
 * @package   WalktheChat\Walkthechat
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Api;

/**
 * Interface ImageSyncRepositoryInterface
 *
 * @package WalktheChat\Walkthechat\Api
 */
interface ImageSyncRepositoryInterface
{
    /**
     * Save Image Sync entity
     *
     * @param \WalktheChat\Walkthechat\Api\Data\ImageSyncInterface $imageSync
     *
     * @return \WalktheChat\Walkthechat\Api\Data\ImageSyncInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\WalktheChat\Walkthechat\Api\Data\ImageSyncInterface $imageSync);

    /**
     * Return list of entities
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \WalktheChat\Walkthechat\Api\Data\ImageSyncSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Remove entities by product ids
     *
     * @param array $productIds
     *
     * @return bool
     * @throws \Exception
     */
    public function deleteByProductIds(array $productIds);
}

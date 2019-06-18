<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 Walkthechat
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Api;

/**
 * Interface ImageSyncRepositoryInterface
 *
 * @package Walkthechat\Walkthechat\Api
 */
interface ImageSyncRepositoryInterface
{
    /**
     * Save Image Sync entity
     *
     * @param \Walkthechat\Walkthechat\Api\Data\ImageSyncInterface $imageSync
     *
     * @return \Walkthechat\Walkthechat\Api\Data\ImageSyncInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Walkthechat\Walkthechat\Api\Data\ImageSyncInterface $imageSync);

    /**
     * Return list of entities
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Walkthechat\Walkthechat\Api\Data\ImageSyncSearchResultsInterface
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

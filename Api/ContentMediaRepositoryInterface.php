<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Api;

/**
 * Interface ContentMediaRepositoryInterface
 *
 * @package Walkthechat\Walkthechat\Api
 */
interface ContentMediaRepositoryInterface
{
    /**
     * Save Content Media entity
     *
     * @param \Walkthechat\Walkthechat\Api\Data\ContentMediaInterface $contentMedia
     *
     * @return \Walkthechat\Walkthechat\Api\Data\ContentMediaInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Walkthechat\Walkthechat\Api\Data\ContentMediaInterface $contentMedia);

    /**
     * Return list of entities
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Walkthechat\Walkthechat\Api\Data\ContentMediaSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}

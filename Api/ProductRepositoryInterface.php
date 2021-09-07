<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Api;

/**
 * Interface ProductRepositoryInterface
 *
 * @package Walkthechat\Walkthechat\Api
 */
interface ProductRepositoryInterface
{
    /**
     * Return entity by ID
     *
     * @param int $id
     *
     * @return \Walkthechat\Walkthechat\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);
	
	/**
     * Return entity by Product ID
     *
     * @param int $id
     *
     * @return \Walkthechat\Walkthechat\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByProductId($id);

    /**
     * Saves entity
     *
     * @param \Walkthechat\Walkthechat\Api\Data\ProductInterface $product
     *
     * @return \Walkthechat\Walkthechat\Api\Data\ProductInterface
     */
    public function save(\Walkthechat\Walkthechat\Api\Data\ProductInterface $product);

    /**
     * Remove entity
     *
     * @param \Walkthechat\Walkthechat\Api\Data\ProductInterface $product
     *
     * @return void
     */
    public function delete(\Walkthechat\Walkthechat\Api\Data\ProductInterface $product);

    /**
     * Return list of entities
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Walkthechat\Walkthechat\Api\Data\ProductSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Bulk save many entities
     *
     * @param array $data
     *
     * @return \Walkthechat\Walkthechat\Api\Data\ProductSearchResultsInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function bulkSave(array $data);
}

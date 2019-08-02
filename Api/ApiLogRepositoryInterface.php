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
 * Interface ApiLogRepositoryInterface
 *
 * @package Walkthechat\Walkthechat\Api
 */
interface ApiLogRepositoryInterface
{
    /**
     * Save ApiLog entity
     *
     * @param \Walkthechat\Walkthechat\Api\Data\ApiLogInterface $log
     *
     * @return \Walkthechat\Walkthechat\Api\Data\ApiLogInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException(
     */
    public function save(\Walkthechat\Walkthechat\Api\Data\ApiLogInterface $log);

    /**
     * Return entity instance by ID
     *
     * @param int $id
     *
     * @return \Walkthechat\Walkthechat\Api\Data\ApiLogInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Return last item by for queue item id
     *
     * @param int $id
     *
     * @return \Walkthechat\Walkthechat\Api\Data\ApiLogInterface
     */
    public function getLastByQuoteItemId($id);
}

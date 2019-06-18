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
 * Interface ApiLogRepositoryInterface
 *
 * @package WalktheChat\Walkthechat\Api
 */
interface ApiLogRepositoryInterface
{
    /**
     * Save ApiLog entity
     *
     * @param \WalktheChat\Walkthechat\Api\Data\ApiLogInterface $log
     *
     * @return \WalktheChat\Walkthechat\Api\Data\ApiLogInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException(
     */
    public function save(\WalktheChat\Walkthechat\Api\Data\ApiLogInterface $log);

    /**
     * Return entity instance by ID
     *
     * @param int $id
     *
     * @return \WalktheChat\Walkthechat\Api\Data\ApiLogInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Return last item by for queue item id
     *
     * @param int $id
     *
     * @return \WalktheChat\Walkthechat\Api\Data\ApiLogInterface
     */
    public function getLastByQuoteItemId($id);
}

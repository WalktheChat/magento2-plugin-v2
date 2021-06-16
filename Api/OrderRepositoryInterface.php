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
 * Interface OrderRepositoryInterface
 *
 * @package Walkthechat\Walkthechat\Api
 */
interface OrderRepositoryInterface
{
    /**
     * Return entity by ID
     *
     * @param int $id
     *
     * @return \Walkthechat\Walkthechat\Api\Data\OrderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Return entity by Walkthechat ID
     *
     * @param string $id
     *
     * @return \Walkthechat\Walkthechat\Api\Data\OrderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByWalkthechatId($id);

    /**
     * Saves entity
     *
     * @param \Walkthechat\Walkthechat\Api\Data\OrderInterface $order
     *
     * @return \Walkthechat\Walkthechat\Api\Data\OrderInterface
     */
    public function save(\Walkthechat\Walkthechat\Api\Data\OrderInterface $order);
}

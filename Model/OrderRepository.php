<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model;

/**
 * Class QueueRepository
 *
 * @package Walkthechat\Walkthechat\Model
 */
class OrderRepository implements \Walkthechat\Walkthechat\Api\OrderRepositoryInterface
{
    /**
     * @var \Walkthechat\Walkthechat\Model\ResourceModel\Order
     */
    protected $resource;

    /**
     * @var \Walkthechat\Walkthechat\Api\Data\OrderInterfaceFactory
     */
    protected $orderFactory;

    /**
     * OrderRepository constructor.
     *
     * @param \Walkthechat\Walkthechat\Model\ResourceModel\Order      $resource
     * @param \Walkthechat\Walkthechat\Api\Data\OrderInterfaceFactory $orderFactory
     */
    public function __construct(
        \Walkthechat\Walkthechat\Model\ResourceModel\Order $resource,
        \Walkthechat\Walkthechat\Api\Data\OrderInterfaceFactory $orderFactory
    ) {
        $this->resource               = $resource;
        $this->orderFactory           = $orderFactory;
    }

    /**
     * @param int $id
     *
     * @return \Walkthechat\Walkthechat\Api\Data\OrderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id)
    {
        $order = $this->orderFactory->create();
        $order->load($id);

        if (!$order->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Order with id "%1" does not exist.', $id));
        }

        return $order;
    }

    /**
     * @param string $id
     *
     * @return \Walkthechat\Walkthechat\Api\Data\OrderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByWalkthechatId($id)
    {
        $order = $this->orderFactory->create();
        $order->load($id, 'walkthechat_id');

        if (!$order->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Order with id "%1" does not exist.', $id));
        }

        return $order;
    }

    /**
     * @param \Walkthechat\Walkthechat\Api\Data\OrderInterface $order
     *
     * @return \Walkthechat\Walkthechat\Api\Data\OrderInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Walkthechat\Walkthechat\Api\Data\OrderInterface $order)
    {
        try {
            $this->resource->save($order);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __('Could not save the order: %1', $exception->getMessage()),
                $exception
            );
        }

        return $order;
    }
}

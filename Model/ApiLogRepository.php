<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model;

/**
 * Class ApiLogRepository
 *
 * @package Walkthechat\Walkthechat\Model
 */
class ApiLogRepository implements \Walkthechat\Walkthechat\Api\ApiLogRepositoryInterface
{
    /**
     * @var \Walkthechat\Walkthechat\Model\ResourceModel\ApiLog
     */
    protected $logResource;

    /**
     * @var \Walkthechat\Walkthechat\Api\Data\ApiLogInterfaceFactory
     */
    protected $logFactory;

    /**
     * ApiLogRepository constructor.
     *
     * @param \Walkthechat\Walkthechat\Model\ResourceModel\ApiLog      $logResource
     * @param \Walkthechat\Walkthechat\Api\Data\ApiLogInterfaceFactory $logFactory
     */
    public function __construct(
        \Walkthechat\Walkthechat\Model\ResourceModel\ApiLog $logResource,
        \Walkthechat\Walkthechat\Api\Data\ApiLogInterfaceFactory $logFactory
    ) {
        $this->logResource = $logResource;
        $this->logFactory  = $logFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Walkthechat\Walkthechat\Api\Data\ApiLogInterface $log)
    {
        try {
            $this->logResource->save($log);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__($exception->getMessage()));
        }

        return $log;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        /** @var \Walkthechat\Walkthechat\Api\Data\ApiLogInterface $log */
        $log = $this->logFactory->create();

        $this->logResource->load($log, $id);

        if (!$log->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('API log with id "%1" does not exist.', $log->getId())
            );
        }

        return $log;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastByQuoteItemId($id)
    {
        /** @var \Walkthechat\Walkthechat\Api\Data\ApiLogInterface $log */
        $log = $this->logFactory->create();

        // request was rewrite in resource module to set DESC order by processed_at field
        $this->logResource->load($log, $id, \Walkthechat\Walkthechat\Api\Data\ApiLogInterface::QUEUE_ITEM_ID_FIELD);

        return $log;
    }
}

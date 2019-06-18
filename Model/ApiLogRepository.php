<?php
/**
 * @package   WalktheChat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Model;

/**
 * Class ApiLogRepository
 *
 * @package WalktheChat\Walkthechat\Model
 */
class ApiLogRepository implements \WalktheChat\Walkthechat\Api\ApiLogRepositoryInterface
{
    /**
     * @var \WalktheChat\Walkthechat\Model\ResourceModel\ApiLog
     */
    protected $logResource;

    /**
     * @var \WalktheChat\Walkthechat\Api\Data\ApiLogInterfaceFactory
     */
    protected $logFactory;

    /**
     * ApiLogRepository constructor.
     *
     * @param \WalktheChat\Walkthechat\Model\ResourceModel\ApiLog      $logResource
     * @param \WalktheChat\Walkthechat\Api\Data\ApiLogInterfaceFactory $logFactory
     */
    public function __construct(
        \WalktheChat\Walkthechat\Model\ResourceModel\ApiLog $logResource,
        \WalktheChat\Walkthechat\Api\Data\ApiLogInterfaceFactory $logFactory
    ) {
        $this->logResource = $logResource;
        $this->logFactory  = $logFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\WalktheChat\Walkthechat\Api\Data\ApiLogInterface $log)
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
        /** @var \WalktheChat\Walkthechat\Api\Data\ApiLogInterface $log */
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
        /** @var \WalktheChat\Walkthechat\Api\Data\ApiLogInterface $log */
        $log = $this->logFactory->create();

        // request was rewrite in resource module to set DESC order by processed_at field
        $this->logResource->load($log, $id, \WalktheChat\Walkthechat\Api\Data\ApiLogInterface::QUEUE_ITEM_ID_FIELD);

        return $log;
    }
}

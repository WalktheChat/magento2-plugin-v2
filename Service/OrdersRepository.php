<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Service;

/**
 * Class OrdersRepository
 *
 * @package Walkthechat\Walkthechat\Service
 */
class OrdersRepository extends AbstractService
{
    /**
     * @var Resource\Orders\Find
     */
    protected $orderFindResource;

    /**
     * @var Resource\Orders\Get
     */
    protected $orderGetResource;

    /**
     * @var Resource\Orders\Parcels\Create
     */
    protected $orderParcelCreateResource;

    /**
     * @var \Walkthechat\Walkthechat\Service\Resource\Orders\Refund
     */
    protected $orderRefundResource;

    /**
     * @var \Magento\Sales\Api\ShipmentRepositoryInterface
     */
    protected $shipmentRepository;

    /**
     * @var \Magento\Sales\Api\CreditmemoRepositoryInterface
     */
    protected $creditmemoRepository;

    /**
     * {@inheritdoc}
     *
     * @param \Walkthechat\Walkthechat\Service\Resource\Orders\Find           $orderFindResource
     * @param \Walkthechat\Walkthechat\Service\Resource\Orders\Get            $orderGetResource
     * @param \Walkthechat\Walkthechat\Service\Resource\Orders\Parcels\Create $orderParcelCreateResource
     * @param \Walkthechat\Walkthechat\Service\Resource\Orders\Refund         $orderRefundResource
     * @param \Walkthechat\Walkthechat\Service\Resource\Orders\Refund         $orderRefundResource
     * @param \Magento\Sales\Api\ShipmentRepositoryInterface              $shipmentRepository
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface            $creditmemoRepository
     */
    public function __construct(
        \Walkthechat\Walkthechat\Service\Client $serviceClient,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Walkthechat\Walkthechat\Log\ApiLogger $logger,
        \Walkthechat\Walkthechat\Service\Resource\Orders\Find $orderFindResource,
        \Walkthechat\Walkthechat\Service\Resource\Orders\Get $orderGetResource,
        \Walkthechat\Walkthechat\Service\Resource\Orders\Parcels\Create $orderParcelCreateResource,
        \Walkthechat\Walkthechat\Service\Resource\Orders\Refund $orderRefundResource,
        \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository,
        \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository
    ) {
        $this->orderFindResource       = $orderFindResource;
        $this->orderGetResource          = $orderGetResource;
        $this->orderParcelCreateResource = $orderParcelCreateResource;
        $this->orderRefundResource       = $orderRefundResource;
        $this->shipmentRepository        = $shipmentRepository;
        $this->creditmemoRepository      = $creditmemoRepository;

        parent::__construct(
            $serviceClient,
            $jsonHelper,
            $helper,
            $logger
        );
    }

    /**
     * Find order
     *
     * @return mixed
     * @throws \Zend_Http_Client_Exception
     */
    public function find()
    {
        return $this->request($this->orderFindResource);
    }

    /**
     * Get order by ID
     *
     * @param $id
     *
     * @return mixed
     * @throws \Zend_Http_Client_Exception
     */
    public function get($id)
    {
        return $this->request($this->orderGetResource, ['id' => $id]);
    }

    /**
     * Update order in Walkthechat
     *
     * @param $data
     *
     * @throws \Zend_Http_Client_Exception
     */
    public function update($data)
    {
        // proceed order cancellation
        if ($data['is_canceled']) {
            // TODO: cancel request
        }

        // proceed parcels
        foreach ($data['parcels'] as $parcel) {
            $this->request($this->orderParcelCreateResource, $parcel['data']);

            $this->setParcelAsSentToWalkTheChat($parcel['entity']);
        }

        // proceed refunds
        foreach ($data['refunds'] as $refund) {
            $this->request($this->orderRefundResource, $refund['data']);

            $this->setRefundAsSentToWalkTheChat($refund['entity']);
        }
    }

    /**
     * Set flag to omit double proceed the same parcel
     *
     * @param \Magento\Sales\Api\Data\ShipmentInterface $parcel
     */
    protected function setParcelAsSentToWalkTheChat(\Magento\Sales\Api\Data\ShipmentInterface $parcel)
    {
        $parcel->setIsSentToWalkTheChat(true);

        $this->shipmentRepository->save($parcel);
    }

    /**
     * Set flag to omit double proceed the same credit memo
     *
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $refund
     */
    protected function setRefundAsSentToWalkTheChat(\Magento\Sales\Api\Data\CreditmemoInterface $refund)
    {
        $refund->setIsSentToWalkTheChat(true);

        $this->creditmemoRepository->save($refund);
    }
}

<?php
/**
 * @package   WalktheChat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Service;

/**
 * Class ShippingZonesRepository
 *
 * @package WalktheChat\Walkthechat\Service
 */
class ShippingZonesRepository extends AbstractService
{
    /**
     * @var \WalktheChat\Walkthechat\Service\Resource\ShippingZones\Create
     */
    protected $shippingZonesCreateResource;

    /**
     * @var \WalktheChat\Walkthechat\Service\Resource\ShippingZones\Find
     */
    protected $shippingZonesFindResource;

    /**
     * @var \WalktheChat\Walkthechat\Service\Resource\ShippingZones\Delete
     */
    protected $shippingZonesDeleteResource;

    /**
     * {@inheritdoc}
     *
     * @param \WalktheChat\Walkthechat\Service\Resource\ShippingZones\Create $shippingZonesCreateResource
     * @param \WalktheChat\Walkthechat\Service\Resource\ShippingZones\Find   $shippingZonesFindResource
     * @param \WalktheChat\Walkthechat\Service\Resource\ShippingZones\Delete $shippingZonesDeleteResource
     */
    public function __construct(
        \WalktheChat\Walkthechat\Service\Client $serviceClient,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \WalktheChat\Walkthechat\Helper\Data $helper,
        \WalktheChat\Walkthechat\Log\ApiLogger $logger,
        \WalktheChat\Walkthechat\Service\Resource\ShippingZones\Create $shippingZonesCreateResource,
        \WalktheChat\Walkthechat\Service\Resource\ShippingZones\Find $shippingZonesFindResource,
        \WalktheChat\Walkthechat\Service\Resource\ShippingZones\Delete $shippingZonesDeleteResource
    ) {
        $this->shippingZonesCreateResource = $shippingZonesCreateResource;
        $this->shippingZonesFindResource   = $shippingZonesFindResource;
        $this->shippingZonesDeleteResource = $shippingZonesDeleteResource;

        parent::__construct(
            $serviceClient,
            $jsonHelper,
            $helper,
            $logger
        );
    }

    /**
     * create shipping zone
     *
     * @param $data
     *
     * @return mixed
     * @throws \Zend_Http_Client_Exception
     */
    public function create($data)
    {
        return $this->request($this->shippingZonesCreateResource, $data);
    }

    /**
     * delete shipping zone
     *
     * @param $id
     *
     * @return mixed
     * @throws \Zend_Http_Client_Exception
     */
    public function delete($id)
    {
        return $this->request($this->shippingZonesDeleteResource, ['id' => $id]);
    }

    /**
     * Find shipping zone
     *
     * @return mixed
     * @throws \Zend_Http_Client_Exception
     */
    public function find()
    {
        return $this->request($this->shippingZonesFindResource);
    }
}

<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Service;

/**
 * Class ShippingZonesRepository
 *
 * @package Walkthechat\Walkthechat\Service
 */
class ShippingZonesRepository extends AbstractService
{
    /**
     * @var \Walkthechat\Walkthechat\Service\Resource\ShippingZones\Create
     */
    protected $shippingZonesCreateResource;

    /**
     * @var \Walkthechat\Walkthechat\Service\Resource\ShippingZones\Find
     */
    protected $shippingZonesFindResource;

    /**
     * @var \Walkthechat\Walkthechat\Service\Resource\ShippingZones\Delete
     */
    protected $shippingZonesDeleteResource;

    /**
     * {@inheritdoc}
     *
     * @param \Walkthechat\Walkthechat\Service\Resource\ShippingZones\Create $shippingZonesCreateResource
     * @param \Walkthechat\Walkthechat\Service\Resource\ShippingZones\Find   $shippingZonesFindResource
     * @param \Walkthechat\Walkthechat\Service\Resource\ShippingZones\Delete $shippingZonesDeleteResource
     */
    public function __construct(
        \Walkthechat\Walkthechat\Service\Client $serviceClient,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Walkthechat\Walkthechat\Log\ApiLogger $logger,
        \Walkthechat\Walkthechat\Service\Resource\ShippingZones\Create $shippingZonesCreateResource,
        \Walkthechat\Walkthechat\Service\Resource\ShippingZones\Find $shippingZonesFindResource,
        \Walkthechat\Walkthechat\Service\Resource\ShippingZones\Delete $shippingZonesDeleteResource
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

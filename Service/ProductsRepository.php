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
 * Class ProductsRepository
 *
 * @package Walkthechat\Walkthechat\Service
 */
class ProductsRepository extends AbstractService
{
    /**
     * @var \Walkthechat\Walkthechat\Service\Resource\Products\Create
     */
    protected $productCreateResource;

    /**
     * @var \Walkthechat\Walkthechat\Service\Resource\Products\Delete
     */
    protected $productDeleteResource;

    /**
     * @var \Walkthechat\Walkthechat\Service\Resource\Products\Find
     */
    protected $productFindResource;

    /**
     * @var \Walkthechat\Walkthechat\Service\Resource\Products\Update
     */
    protected $productUpdateResource;

    /**
     * {@inheritdoc}
     *
     * @param \Walkthechat\Walkthechat\Service\Resource\Products\Create $productCreateResource
     * @param \Walkthechat\Walkthechat\Service\Resource\Products\Delete $productDeleteResource
     * @param \Walkthechat\Walkthechat\Service\Resource\Products\Find   $productFindResource
     * @param \Walkthechat\Walkthechat\Service\Resource\Products\Update $productUpdateResource
     */
    public function __construct(
        \Walkthechat\Walkthechat\Service\Client $serviceClient,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Walkthechat\Walkthechat\Log\ApiLogger $logger,
        \Walkthechat\Walkthechat\Service\Resource\Products\Create $productCreateResource,
        \Walkthechat\Walkthechat\Service\Resource\Products\Delete $productDeleteResource,
        \Walkthechat\Walkthechat\Service\Resource\Products\Find $productFindResource,
        \Walkthechat\Walkthechat\Service\Resource\Products\Update $productUpdateResource
    ) {
        $this->productCreateResource = $productCreateResource;
        $this->productDeleteResource = $productDeleteResource;
        $this->productFindResource   = $productFindResource;
        $this->productUpdateResource = $productUpdateResource;

        parent::__construct(
            $serviceClient,
            $jsonHelper,
            $helper,
            $logger
        );
    }

    /**
     * Create product
     *
     * @param array $data
     *
     * @return string|null
     * @throws \Zend_Http_Client_Exception
     */
    public function create($data)
    {
        $response = $this->request($this->productCreateResource, $data);

        return isset($response['id']) ? $response['id'] : null;
    }

    /**
     * Delete product
     *
     * @param $id
     *
     * @return mixed
     * @throws \Zend_Http_Client_Exception
     */
    public function delete($id)
    {
        return $this->request($this->productDeleteResource, $id);
    }

    /**
     * Find product
     *
     * @return mixed
     * @throws \Zend_Http_Client_Exception
     */
    public function find()
    {
        return $this->request($this->productFindResource);
    }

    /**
     * Update product
     *
     * @param $data
     *
     * @return mixed
     * @throws \Zend_Http_Client_Exception
     */
    public function update($data)
    {
        return $this->request($this->productUpdateResource, $data);
    }
}

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
 * Class ProductsRepository
 *
 * @package WalktheChat\Walkthechat\Service
 */
class ProductsRepository extends AbstractService
{
    /**
     * @var \WalktheChat\Walkthechat\Service\Resource\Products\Create
     */
    protected $productCreateResource;

    /**
     * @var \WalktheChat\Walkthechat\Service\Resource\Products\Delete
     */
    protected $productDeleteResource;

    /**
     * @var \WalktheChat\Walkthechat\Service\Resource\Products\Find
     */
    protected $productFindResource;

    /**
     * @var \WalktheChat\Walkthechat\Service\Resource\Products\Update
     */
    protected $productUpdateResource;

    /**
     * {@inheritdoc}
     *
     * @param \WalktheChat\Walkthechat\Service\Resource\Products\Create $productCreateResource
     * @param \WalktheChat\Walkthechat\Service\Resource\Products\Delete $productDeleteResource
     * @param \WalktheChat\Walkthechat\Service\Resource\Products\Find   $productFindResource
     * @param \WalktheChat\Walkthechat\Service\Resource\Products\Update $productUpdateResource
     */
    public function __construct(
        \WalktheChat\Walkthechat\Service\Client $serviceClient,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \WalktheChat\Walkthechat\Helper\Data $helper,
        \WalktheChat\Walkthechat\Log\ApiLogger $logger,
        \WalktheChat\Walkthechat\Service\Resource\Products\Create $productCreateResource,
        \WalktheChat\Walkthechat\Service\Resource\Products\Delete $productDeleteResource,
        \WalktheChat\Walkthechat\Service\Resource\Products\Find $productFindResource,
        \WalktheChat\Walkthechat\Service\Resource\Products\Update $productUpdateResource
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

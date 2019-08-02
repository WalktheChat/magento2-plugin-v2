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
 * Class AbstractService
 *
 * @package Walkthechat\Walkthechat\Service
 */
abstract class AbstractService
{
    /**
     * @var \Walkthechat\Walkthechat\Service\Client
     */
    protected $serviceClient;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Walkthechat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * @var \Walkthechat\Walkthechat\Log\ApiLogger
     */
    protected $logger;

    /**
     * AbstractService constructor.
     *
     * @param \Walkthechat\Walkthechat\Service\Client $serviceClient
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Walkthechat\Walkthechat\Helper\Data    $helper
     * @param \Walkthechat\Walkthechat\Log\ApiLogger  $logger
     */
    public function __construct(
        \Walkthechat\Walkthechat\Service\Client $serviceClient,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Walkthechat\Walkthechat\Log\ApiLogger $logger
    ) {
        $this->serviceClient = $serviceClient;
        $this->jsonHelper    = $jsonHelper;
        $this->helper        = $helper;
        $this->logger        = $logger;
    }

    /**
     * Send request to API
     *
     * @param \Walkthechat\Walkthechat\Service\Resource\AbstractResource $resource
     * @param array                                                  $params
     * @param bool                                                   $isImageUpload
     *
     * @return mixed
     * @throws \Zend_Http_Client_Exception
     */
    public function request(
        \Walkthechat\Walkthechat\Service\Resource\AbstractResource $resource, 
        $params = [], 
        $isImageUpload = false
    ) {
        $headers = $resource->getHeaders();

        $headers['x-access-token'] = $this->helper->getToken();

        $path = $resource->getPath();

        // id to represent for logging
        $placeholderId = null;

        if (isset($params['id'])) {
            $path = str_replace(':id', $params['id'], $path);

            // set valid it to log proper path
            $placeholderId = $params['id'];

            unset($params['id']);
        }

        if (!$isImageUpload) {
            $params['projectId'] = $this->helper->getProjectId();
        }

        $response = $this->serviceClient->request($resource->getType(), $path, $params, $headers, $isImageUpload);

        // log into WalkTheChat log in Admin Panel
        $this->logger->log($resource, $params, $response, $placeholderId);

        if ($response->isError()) {
            throw new \Zend\Http\Client\Exception\RuntimeException('Invalid response.');
        }

        return $this->jsonHelper->jsonDecode($response->getBody());
    }
}

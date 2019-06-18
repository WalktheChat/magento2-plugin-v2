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
 * Class Client
 *
 * @package WalktheChat\Walkthechat\Service
 */
class Client
{
    /**
     * @var \WalktheChat\Walkthechat\HTTP\ZendClientFactory
     */
    protected $httpClientFactory;

    /**
     * @var \WalktheChat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * Client constructor.
     *
     * @param \WalktheChat\Walkthechat\HTTP\ZendClientFactory $httpClientFactory
     * @param \WalktheChat\Walkthechat\Helper\Data            $helper
     */
    public function __construct(
        \WalktheChat\Walkthechat\HTTP\ZendClientFactory $httpClientFactory,
        \WalktheChat\Walkthechat\Helper\Data $helper
    ) {
        $this->httpClientFactory = $httpClientFactory;
        $this->helper            = $helper;
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return $this->helper->getApiUrl();
    }

    /**
     * Send request to API
     *
     * @param string       $type
     * @param string       $path
     * @param array|string $data
     * @param array        $headers
     * @param bool         $isImageUpload
     *
     * @return \Zend_Http_Response
     * @throws \Zend_Http_Client_Exception
     */
    public function request($type, $path, $data, $headers, $isImageUpload = false)
    {
        /** @var \WalktheChat\Walkthechat\HTTP\ZendClient $httpClient */
        $httpClient = $this->httpClientFactory->create();

        $httpClient
            ->setUri($this->getEndpoint().$path)
            ->setConfig(['timeout' => 600]); // China is a problematic connection country :)

        $headers['accept-encoding'] = 'identity';

        if ($type == 'POST' || $type == 'PUT') {
            if ($isImageUpload && isset($data['file'])) {
                $httpClient->setFileUpload($data['file'], 'file');

                $httpClient->setConfig(['timeout' => 3600]); // China is a problematic connection country :)
            } else {
                $httpClient->setParameterPost($data);
            }
        } elseif ($type == 'GET') {
            $httpClient->setParameterGet($data);
        } elseif ($type == 'PATCH') {
            $httpClient->setRawData(json_encode($data));
        }

        $httpClient->setHeaders($headers);

        return $httpClient->request($type);
    }
}

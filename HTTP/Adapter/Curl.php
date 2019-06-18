<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\HTTP\Adapter;

/**
 * Class Curl
 *
 * @package Walkthechat\Walkthechat\HTTP\Adapter
 */
class Curl extends \Magento\Framework\HTTP\Adapter\Curl
{
    /**
     * Send request to the remote server
     *
     * @param string                $method
     * @param \Zend_Uri_Http|string $url
     * @param string                $http_ver
     * @param array                 $headers
     * @param string                $body
     *
     * @return string Request as text
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Zend_Uri_Exception
     */
    public function write($method, $url, $http_ver = '1.1', $headers = [], $body = '')
    {
        if ($url instanceof \Zend_Uri_Http) {
            $url = $url->getUri();
        }

        $this->_applyConfig();

        // set url to post to
        curl_setopt($this->_getResource(), CURLOPT_URL, $url);
        curl_setopt($this->_getResource(), CURLOPT_RETURNTRANSFER, true);

        // debug option (logs of connections shows in terminal)
        //curl_setopt($this->_getResource(), CURLOPT_VERBOSE, 1);

        if ($method == \Zend_Http_Client::POST) {
            curl_setopt($this->_getResource(), CURLOPT_POST, true);
            curl_setopt($this->_getResource(), CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($this->_getResource(), CURLOPT_POSTFIELDS, $body);
        } elseif ($method == \Zend_Http_Client::PUT) {
            curl_setopt($this->_getResource(), CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($this->_getResource(), CURLOPT_POSTFIELDS, $body);
        } elseif ($method == \Zend_Http_Client::GET) {
            curl_setopt($this->_getResource(), CURLOPT_HTTPGET, true);
            curl_setopt($this->_getResource(), CURLOPT_CUSTOMREQUEST, 'GET');
        } elseif ($method == \Zend_Http_Client::DELETE) {
            curl_setopt($this->_getResource(), CURLOPT_CUSTOMREQUEST, 'DELETE');
        } elseif ($method == \Zend_Http_Client::PATCH) {
            curl_setopt($this->_getResource(), CURLOPT_POSTFIELDS, $body);
            curl_setopt($this->_getResource(), CURLOPT_CUSTOMREQUEST, 'PATCH');
        }

        if (is_array($headers)) {
            curl_setopt($this->_getResource(), CURLOPT_HTTPHEADER, $headers);
        }

        /**
         * @internal Curl options setter have to be re-factored
         */
        $header = isset($this->_config['header']) ? $this->_config['header'] : true;

        curl_setopt($this->_getResource(), CURLOPT_HEADER, $header);

        return $body;
    }
}

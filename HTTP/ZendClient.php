<?php
/**
 * @package   WalktheChat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\HTTP;

/**
 * Class ZendClient
 *
 * @package WalktheChat\Walkthechat\HTTP
 */
class ZendClient extends \Magento\Framework\HTTP\ZendClient
{
    /**
     * Try to load curl extension
     *
     * @return $this|\Magento\Framework\HTTP\ZendClient
     * @throws \Zend_Http_Client_Exception
     */
    protected function _trySetCurlAdapter()
    {
        if (extension_loaded('curl')) {
            $this->setAdapter(new \WalktheChat\Walkthechat\HTTP\Adapter\Curl());
        }

        return $this;
    }
}

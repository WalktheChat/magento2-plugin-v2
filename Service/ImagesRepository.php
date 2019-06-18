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
 * Class ImagesRepository
 *
 * @package Walkthechat\Walkthechat\Service
 */
class ImagesRepository extends AbstractService
{
    /**
     * @var \Walkthechat\Walkthechat\Service\Resource\Images\Create
     */
    protected $imagesCreateResource;

    /**
     * {@inheritdoc}
     *
     * @param \Walkthechat\Walkthechat\Service\Resource\Images\Create $imagesCreateResource
     */
    public function __construct(
        \Walkthechat\Walkthechat\Service\Client $serviceClient,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Walkthechat\Walkthechat\Log\ApiLogger $logger,
        \Walkthechat\Walkthechat\Service\Resource\Images\Create $imagesCreateResource
    ) {
        $this->imagesCreateResource = $imagesCreateResource;

        parent::__construct(
            $serviceClient,
            $jsonHelper,
            $helper,
            $logger
        );
    }

    /**
     * Create image in CDN
     *
     * @param string $filePath
     *
     * @return string|null
     * @throws \Zend_Http_Client_Exception
     */
    public function create($filePath)
    {
        return $this->request($this->imagesCreateResource, ['file' => $filePath], true);
    }
}

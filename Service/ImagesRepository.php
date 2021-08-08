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
     * @var Resource\Images\Background\Upload
     */
    protected $imagesBgUploadResource;

    /**
     * {@inheritdoc}
     *
     * @param \Walkthechat\Walkthechat\Service\Resource\Images\Create $imagesCreateResource
     * @param \Walkthechat\Walkthechat\Service\Resource\Images\Background\Upload $imagesBgUploadResource
     */
    public function __construct(
        \Walkthechat\Walkthechat\Service\Client $serviceClient,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Walkthechat\Walkthechat\Log\ApiLogger $logger,
        \Walkthechat\Walkthechat\Service\Resource\Images\Create $imagesCreateResource,
        \Walkthechat\Walkthechat\Service\Resource\Images\Background\Upload $imagesBgUploadResource
    ) {
        $this->imagesCreateResource     = $imagesCreateResource;
        $this->imagesBgUploadResource   = $imagesBgUploadResource;

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

    /**
     * Send images to CDN
     *
     * @param array $images
     *
     * @return string|null
     * @throws \Zend_Http_Client_Exception
     */
    public function backgroundUpload($images)
    {
        return $this->request($this->imagesBgUploadResource, ['urls' => $images]);
    }
}

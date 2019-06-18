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
 * Class ImagesRepository
 *
 * @package WalktheChat\Walkthechat\Service
 */
class ImagesRepository extends AbstractService
{
    /**
     * @var \WalktheChat\Walkthechat\Service\Resource\Images\Create
     */
    protected $imagesCreateResource;

    /**
     * {@inheritdoc}
     *
     * @param \WalktheChat\Walkthechat\Service\Resource\Images\Create $imagesCreateResource
     */
    public function __construct(
        \WalktheChat\Walkthechat\Service\Client $serviceClient,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \WalktheChat\Walkthechat\Helper\Data $helper,
        \WalktheChat\Walkthechat\Log\ApiLogger $logger,
        \WalktheChat\Walkthechat\Service\Resource\Images\Create $imagesCreateResource
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

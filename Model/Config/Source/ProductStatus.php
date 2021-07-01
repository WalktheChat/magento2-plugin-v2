<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 * @license   See LICENSE_WALKTHECHAT.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model\Config\Source;

/**
 * Class ProductStatus
 *
 * @package Walkthechat\Walkthechat\Model\Config\Source
 */
class ProductStatus implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => \Walkthechat\Walkthechat\Api\Data\ProductInterface::QUEUE_STATUS,
                'label' => __('In Queue'),
            ],
            [
                'value' => \Walkthechat\Walkthechat\Api\Data\OrderInterface::COMPLETE_STATUS,
                'label' => __('Synced'),
            ],
            [
                'value' => \Walkthechat\Walkthechat\Api\Data\OrderInterface::ERROR_STATUS,
                'label' => __('Error'),
            ],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            \Walkthechat\Walkthechat\Api\Data\OrderInterface::QUEUE_STATUS      => __('In Queue'),
            \Walkthechat\Walkthechat\Api\Data\OrderInterface::COMPLETE_STATUS   => __('Synced'),
            \Walkthechat\Walkthechat\Api\Data\OrderInterface::ERROR_STATUS      => __('Error')
        ];
    }
}

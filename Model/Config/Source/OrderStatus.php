<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 * @license   See LICENSE_WALKTHECHAT.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model\Config\Source;

/**
 * Class OrderStatus
 *
 * @package Walkthechat\Walkthechat\Model\Config\Source
 */
class OrderStatus implements \Magento\Framework\Option\ArrayInterface
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
                'value' => \Walkthechat\Walkthechat\Api\Data\OrderInterface::COMPLETE_STATUS,
                'label' => __('Complete'),
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
            \Walkthechat\Walkthechat\Api\Data\OrderInterface::COMPLETE_STATUS   => __('Complete'),
            \Walkthechat\Walkthechat\Api\Data\OrderInterface::ERROR_STATUS      => __('Error')
        ];
    }
}

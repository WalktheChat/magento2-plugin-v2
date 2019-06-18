<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 Walkthechat
 * @license   See LICENSE_DIVANTE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model\Config\Source;

/**
 * Class QueueItemStatus
 *
 * @package Walkthechat\Walkthechat\Model\Config\Source
 */
class QueueItemStatus implements \Magento\Framework\Option\ArrayInterface
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
                'value' => \Walkthechat\Walkthechat\Api\Data\QueueInterface::INTERNAL_ERROR_STATUS,
                'label' => __('Internal Error'),
            ],
            ['value' => \Walkthechat\Walkthechat\Api\Data\QueueInterface::API_ERROR_STATUS, 'label' => __('API Error')],
            ['value' => \Walkthechat\Walkthechat\Api\Data\QueueInterface::COMPLETE_STATUS, 'label' => __('Complete')],
            [
                'value' => \Walkthechat\Walkthechat\Api\Data\QueueInterface::WAITING_IN_QUEUE_STATUS,
                'label' => __('Waiting in Queue'),
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
            \Walkthechat\Walkthechat\Api\Data\QueueInterface::WAITING_IN_QUEUE_STATUS => __('Waiting in Queue'),
            \Walkthechat\Walkthechat\Api\Data\QueueInterface::COMPLETE_STATUS         => __('Complete'),
            \Walkthechat\Walkthechat\Api\Data\QueueInterface::API_ERROR_STATUS        => __('API Error'),
            \Walkthechat\Walkthechat\Api\Data\QueueInterface::INTERNAL_ERROR_STATUS   => __('Internal Error'),
        ];
    }
}

<?php
/**
 * @package   WalktheChat\Walkthechat
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 * @license   See LICENSE_DIVANTE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Model\Config\Source;

/**
 * Class QueueItemStatus
 *
 * @package WalktheChat\Walkthechat\Model\Config\Source
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
                'value' => \WalktheChat\Walkthechat\Api\Data\QueueInterface::INTERNAL_ERROR_STATUS,
                'label' => __('Internal Error'),
            ],
            ['value' => \WalktheChat\Walkthechat\Api\Data\QueueInterface::API_ERROR_STATUS, 'label' => __('API Error')],
            ['value' => \WalktheChat\Walkthechat\Api\Data\QueueInterface::COMPLETE_STATUS, 'label' => __('Complete')],
            [
                'value' => \WalktheChat\Walkthechat\Api\Data\QueueInterface::WAITING_IN_QUEUE_STATUS,
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
            \WalktheChat\Walkthechat\Api\Data\QueueInterface::WAITING_IN_QUEUE_STATUS => __('Waiting in Queue'),
            \WalktheChat\Walkthechat\Api\Data\QueueInterface::COMPLETE_STATUS         => __('Complete'),
            \WalktheChat\Walkthechat\Api\Data\QueueInterface::API_ERROR_STATUS        => __('API Error'),
            \WalktheChat\Walkthechat\Api\Data\QueueInterface::INTERNAL_ERROR_STATUS   => __('Internal Error'),
        ];
    }
}

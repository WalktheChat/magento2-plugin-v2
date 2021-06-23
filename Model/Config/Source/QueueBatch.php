<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model\Config\Source;

/**
 * Class QueueBatch
 *
 * @package Walkthechat\Walkthechat\Model\Config\Source
 */
class QueueBatch implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 10, 'label' => 10],
            ['value' => 20, 'label' => 20],
            ['value' => 50, 'label' => 50],
            ['value' => 100, 'label' => 100],
            ['value' => 200, 'label' => 200],
            ['value' => 500, 'label' => 500],
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
            10 => 10,
            20 => 20,
            50 => 50,
            100 => 100,
            200 => 200,
            500 => 500
        ];
    }
}

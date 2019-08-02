<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model\Config\Source;

/**
 * Class RoundMethod
 *
 * @package Walkthechat\Walkthechat\Model\Config\Source
 */
class RoundMethod implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Nearest Integer')],
            ['value' => 2, 'label' => __('China friendly price')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [1 => __('Nearest Integer'), 2 => __('China friendly price')];
    }
}

<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Ui\Component\Listing\Column;

/**
 * Class Item
 *
 * @package Walkthechat\Walkthechat\Ui\Component\Listing\Column
 */
class Item extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');

                if ($item[$name] || $item['walkthechat_id']) {
                    $item[$name] = __('Product with ID: %1', $item[$name] ?? $item['walkthechat_id']);
                } elseif ($item['order_id']) {
                    $item[$name] = __('Order with ID: %1', $item['order_id']);
                }
            }
        }

        return $dataSource;
    }
}

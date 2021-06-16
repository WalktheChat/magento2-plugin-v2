<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Ui\Component\Listing\Column;

/**
 * Class OrderDetails
 *
 * @package Walkthechat\Walkthechat\Ui\Component\Listing\Column
 */
class OrderAction extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Url import path
     *
     * @var string
     */
    const URL_PATH_IMPORT = 'walkthechat/order/import';

    /**
     * Url import path
     *
     * @var string
     */
    const URL_PATH_ORDER = 'sales/order/view';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * {@inheritdoc}
     *
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;

        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');

                if ($item['order_id']) {
                    $item[$name]['edit'] = [
                        'href'  => $this->urlBuilder->getUrl(
                            self::URL_PATH_ORDER,
                            [
                                'order_id' => $item['order_id'],
                            ]
                        ),
                        'label' => __('View Order'),
                    ];
                } else {
                    $item[$name]['edit'] = [
                        'href'  => $this->urlBuilder->getUrl(
                            self::URL_PATH_IMPORT,
                            [
                                'entity_id' => $item['entity_id'],
                            ]
                        ),
                        'label' => __('Try Again'),
                    ];
                }
            }
        }

        return $dataSource;
    }
}

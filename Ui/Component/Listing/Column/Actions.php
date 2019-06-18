<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Ui\Component\Listing\Column;

/**
 * Class Actions
 *
 * @package Walkthechat\Walkthechat\Ui\Component\Listing\Column
 */
class Actions extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Url path
     *
     * @var string
     */
    const URL_PATH_DETAILS = 'walkthechat/logs/details';

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

                if (isset($item['entity_id'])) {
                    $item[$name]['edit'] = [
                        'href'  => $this->urlBuilder->getUrl(
                            self::URL_PATH_DETAILS,
                            [
                                'entity_id' => $item['entity_id'],
                            ]
                        ),
                        'label' => __('Details'),
                    ];
                }
            }
        }

        return $dataSource;
    }
}

<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model;

/**
 * Class ShippingService
 *
 * @package Walkthechat\Walkthechat\Model
 */
class ShippingService
{
    /**
     * @var \Walkthechat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Walkthechat\Walkthechat\Service\ShippingZonesRepository
     */
    protected $shippingZonesRepository;

    /**
     * ShippingService constructor.
     *
     * @param \Walkthechat\Walkthechat\Helper\Data                                                 $helper
     * @param \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CollectionFactory $collectionFactory
     * @param \Walkthechat\Walkthechat\Service\ShippingZonesRepository                             $shippingZonesRepository
     */
    public function __construct(
        \Walkthechat\Walkthechat\Helper\Data $helper,
        \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CollectionFactory $collectionFactory,
        \Walkthechat\Walkthechat\Service\ShippingZonesRepository $shippingZonesRepository
    ) {
        $this->helper                  = $helper;
        $this->collectionFactory       = $collectionFactory;
        $this->shippingZonesRepository = $shippingZonesRepository;
    }

    /**
     * Sync Table Rates with Walkthechat
     *
     * @return bool
     */
    public function sync()
    {
        if ($this->helper->getTableRateConditionName() == 'package_weight') {
            $type = 'weight';
        } elseif ($this->helper->getTableRateConditionName() == 'package_value') {
            $type = 'price';
        } else {
            return false;
        }

        try {
            $rows = $this->shippingZonesRepository->find();

            foreach ($rows as $row) {
                if (isset($row['id'])) {
                    $this->shippingZonesRepository->delete($row['id']);
                }
            }

            /** @var \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\Collection $collection */
            $collection = $this->collectionFactory->create();

            $collection->addFieldToFilter('condition_name', $this->helper->getTableRateConditionName());
            $collection->setOrder('condition_value', 'DESC');
            $collection->load();

            $rates = [];

            foreach ($collection as $row) {
                $rates[$row->getDestCountryId()][$row->getConditionValue()] = $row->getPrice();
            }

            foreach ($rates as $code => $rate) {
                $data = [
                    'name'      => [
                        'en' => $code,
                        'cn' => $code,
                    ],
                    'enabled'   => true,
                    'countries' => [$code],
                    'rates'     => [],
                ];

                $max = 999999999;
                foreach ($rate as $min => $value) {
                    $name = $code.' '.$min.'-'.$max;

                    $data['rates'][] = [
                        'name'   => [
                            'en' => $name,
                            'cn' => $name,
                        ],
                        'min'    => $min,
                        'max'    => $max,
                        'rate'   => $value,
                        'type'   => $type,
                        'isFree' => $value ? false : true,
                    ];
                }

                $this->shippingZonesRepository->create($data);
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}

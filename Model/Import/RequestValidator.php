<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model\Import;

/**
 * Class RequestValidator
 *
 * @package Walkthechat\Walkthechat\Model\Import
 */
class RequestValidator
{
    /**
     * Validates params and throw exception if validation failed
     *
     * @param string $id
     * @param string $name
     * @param string $financialStatus
     * @param mixed  $itemsToFulfill
     * @param array  $items
     * @param array  $deliveryAddress
     * @param array  $shippingRate
     * @param array  $tax
     * @param array  $total
     *
     * @param array  $coupon
     *
     * @return array
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function validate(
        $id,
        $name,
        $financialStatus,
        $itemsToFulfill,
        $items,
        $deliveryAddress,
        $shippingRate,
        $tax,
        $total,
        $coupon
    ) {
        $this->validateId($id);
        $this->validateStatus($financialStatus);
        $this->validateItems($items);
        $this->validateDeliveryAddress($deliveryAddress);
        $this->validateShippingRate($shippingRate);
        $this->validateTax($tax);
        $this->validateTotal($total);

        return compact(
            'id',
            'name',
            'items',
            'itemsToFulfill',
            'deliveryAddress',
            'shippingRate',
            'tax',
            'total',
            'coupon'
        );
    }

    /**
     * Throws exception if items structure is invalid
     *
     * @param array $items
     *
     * @return bool
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    protected function validateItems($items)
    {
        if (
            is_array($items['products'])
            && isset($items['products'][0])
            && isset($items['products'][0]['product']['id'])
            && isset($items['products'][0]['variant']['sku'])
            && isset($items['products'][0]['quantity'])
            && isset($items['products'][0]['variant']['discount'])
            && isset($items['products'][0]['variant']['priceWithDiscount'])
        ) {
            return true;
        }

        throw new \Magento\Framework\Exception\ValidatorException(
            __('Unable to proceed order import. Items has invalid structure.')
        );
    }

    /**
     * Throws exception if delivery address structure is invalid
     *
     * @param array $deliveryAddress
     *
     * @return bool
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    protected function validateDeliveryAddress($deliveryAddress)
    {
        if (
            isset($deliveryAddress['name'])
            && isset($deliveryAddress['address'])
            && isset($deliveryAddress['district'])
            && isset($deliveryAddress['city'])
            && isset($deliveryAddress['countryCode'])
            && isset($deliveryAddress['province'])
            && isset($deliveryAddress['zipcode'])
            && isset($deliveryAddress['phone'])
        ) {
            return true;
        }

        throw new \Magento\Framework\Exception\ValidatorException(
            __('Unable to proceed order import. Delivery address has invalid structure.')
        );
    }

    /**
     * Throws exception if shipping rate structure is invalid
     *
     * @param array $shippingRate
     *
     * @return bool
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    protected function validateShippingRate($shippingRate)
    {
        if (
            isset($shippingRate['rate'])
            && isset($shippingRate['name']['en'])
        ) {
            return true;
        }

        throw new \Magento\Framework\Exception\ValidatorException(
            __('Unable to proceed order import. Shipping rate has invalid structure.')
        );
    }

    /**
     * Throws exception if tax structure is invalid
     *
     * @param array $tax
     *
     * @return bool
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    protected function validateTax($tax)
    {
        if (isset($tax['rate'])) {
            return true;
        }

        throw new \Magento\Framework\Exception\ValidatorException(
            __('Unable to proceed order import. Tax has invalid structure.')
        );
    }

    /**
     * Throws exception if total structure is invalid
     *
     * @param array $total
     *
     * @return bool
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    protected function validateTotal($total)
    {
        if (
            isset($total['grandTotal']['tax'])
            && isset($total['grandTotal']['shipping'])
            && isset($total['grandTotal']['totalWithoutDiscountAndTax'])
            && isset($total['grandTotal']['totalWithoutTax'])
            && isset($total['grandTotal']['tax'])
            && isset($total['grandTotal']['discount'])
            && isset($total['grandTotal']['total'])
        ) {
            return true;
        }

        throw new \Magento\Framework\Exception\ValidatorException(
            __('Unable to proceed order import. Total has invalid structure.')
        );
    }

    /**
     * Throws exception if ID is invalid
     *
     * @param string $id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    protected function validateId($id)
    {
        if (isset($id)) {
            return true;
        }

        throw new \Magento\Framework\Exception\ValidatorException(
            __('Unable to proceed order import. Id was not passed.')
        );
    }

    /**
     * Throws exception if name is invalid
     *
     * @param string $name
     *
     * @return bool
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    protected function validateName($name)
    {
        if (isset($name)) {
            return true;
        }

        throw new \Magento\Framework\Exception\ValidatorException(
            __('Unable to proceed order import. Name was not passed.')
        );
    }

    /**
     * Throws exception if status is not paid
     *
     * @param string $financialStatus
     *
     * @return bool
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    protected function validateStatus($financialStatus)
    {
        if (strtolower($financialStatus) === 'paid') {
            return true;
        }

        throw new \Magento\Framework\Exception\ValidatorException(
            __('Unable to proceed order import. Order is not paid.')
        );
    }
}

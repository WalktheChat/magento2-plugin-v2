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
     * @var \Walkthechat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * RequestValidator constructor.
     *
     * @param \Walkthechat\Walkthechat\Helper\Data $helper
     */
    public function __construct(
        \Walkthechat\Walkthechat\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Validates params and throw exception if validation failed
     *
     * @param string $id
     * @param string $name
     * @param string $email
     * @param string $projectId
     * @param string $customerId
     * @param string $reference
     * @param string $status
     * @param string $fulfillmentStatus
     * @param string $financialStatus
     * @param boolean $draft
     * @param boolean $refundable
     * @param string $created
     * @param string $modified
     * @param string $sign
     * @param mixed  $payment
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
        $email,
        $projectId,
        $customerId,
        $reference,
        $status,
        $fulfillmentStatus,
        $financialStatus,
        $draft,
        $refundable,
        $created,
        $modified,
        $sign,
        $payment,
        $itemsToFulfill,
        $items,
        $deliveryAddress,
        $shippingRate,
        $tax,
        $total,
        $coupon,
        $checkSignature
    ) {
        $this->validateId($id);
        $this->validateStatus($financialStatus);
        $this->validatePayment($payment);
        $this->validateItems($items);
        $this->validateDeliveryAddress($deliveryAddress);
        $this->validateShippingRate($shippingRate);
        $this->validateTax($tax);
        $this->validateTotal($total);
        if ($checkSignature) {
            $this->validateSignature($sign, $projectId, $customerId, $reference, $status, $fulfillmentStatus, $financialStatus, $draft, $refundable, $id, $created, $modified, $total);
        }

        return compact(
            'id',
            'name',
            'email',
            'payment',
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
     * Throws exception if request signature is invalid
     *
     * @param string $sign
     * @param string $projectId
     * @param string $customerId
     * @param string $reference
     * @param string $status
     * @param string $fulfillmentStatus
     * @param string $financialStatus
     * @param boolean $draft
     * @param boolean $refundable
     * @param string $id
     * @param string $created
     * @param string $modified
     * @param array $total
     *
     * @return bool
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    protected function validateSignature($sign, $projectId, $customerId, $reference, $status, $fulfillmentStatus, $financialStatus, $draft, $refundable, $id, $created, $modified, $total)
    {
        $params = [
            'projectId'                 => $projectId,
            'customerId'                => $customerId,
            'reference'                 => $reference,
            'status'                    => $status,
            'fulfillmentStatus'         => $fulfillmentStatus,
            'financialStatus'           => $financialStatus,
            'draft'                     => $draft ? 'true' : 'false',
            'refundable'                => $refundable ? 'true' : 'false',
            'id'                        => $id,
            'created'                   => $created,
            'modified'                  => $modified,
            'total.currency'            => $total['currency'],
            'total.grandTotal.total'    => $total['grandTotal']['total']
        ];

        ksort($params);

        $params['key'] = $this->helper->getAppKey();

        $string = '';

        foreach ($params as $key => $value) {
            if ($string) {
                $string .= '&';
            }
            $string .= $key . '=' . $value;
        }

        if ($sign != md5($string)) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('Unable to proceed order import. Invalid signature.')
            );
        }

        return true;
    }

    /**
     * Throws exception if payment structure is invalid
     *
     * @param array $payment
     *
     * @return bool
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    protected function validatePayment($payment)
    {
        if (isset($payment['provider']) && isset($payment['vendor'])) {
            return true;
        }

        throw new \Magento\Framework\Exception\ValidatorException(
            __('Unable to proceed order import. Payment has invalid structure.')
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
            isset($deliveryAddress['transliteration']['name'])
            && isset($deliveryAddress['transliteration']['address'])
            && isset($deliveryAddress['transliteration']['district'])
            && isset($deliveryAddress['transliteration']['city'])
            && isset($deliveryAddress['countryCode'])
            && isset($deliveryAddress['transliteration']['province'])
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
            isset($total['currency'])
            && isset($total['grandTotal']['tax'])
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

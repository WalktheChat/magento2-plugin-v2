<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Api;

/**
 * Interface OrderImportInterface
 *
 * @package Walkthechat\Walkthechat\Api
 * @api
 */
interface OrderImportInterface
{
    /**
     * Import order from WalkTheChat CMS
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
     * @param mixed  $customerIdCard
     * @param mixed  $itemsToFulfill
     * @param mixed  $items
     * @param mixed  $deliveryAddress
     * @param mixed  $shippingRate
     * @param mixed  $tax
     * @param mixed  $total
     * @param mixed  $coupon
     * @param mixed  $checkSignature
     *
     * @return string
     */
    public function import(
        $id,
        $name,
        $email = '',
        $projectId,
        $customerId,
        $reference,
        $status,
        $fulfillmentStatus = '',
        $financialStatus,
        $draft,
        $refundable,
        $created,
        $modified,
        $sign,
        $payment = [],
        $customerIdCard = [],
        $itemsToFulfill,
        $items,
        $deliveryAddress,
        $shippingRate,
        $tax,
        $total,
        $coupon = [],
        $checkSignature = true
    );
}

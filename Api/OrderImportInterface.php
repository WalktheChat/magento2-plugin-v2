<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    Alex Yeremenko <madonzy13@gmail.com>
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
     * @param string $projectId
     * @param string $financialStatus
     * @param mixed  $itemsToFulfill
     * @param mixed  $items
     * @param mixed  $deliveryAddress
     * @param mixed  $shippingRate
     * @param mixed  $tax
     * @param mixed  $total
     * @param mixed  $coupon
     *
     * @return string
     */
    public function import(
        $id,
        $projectId,
        $financialStatus,
        $itemsToFulfill,
        $items,
        $deliveryAddress,
        $shippingRate,
        $tax,
        $total,
        $coupon = []
    );
}

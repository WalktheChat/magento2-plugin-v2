<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Service\Resource\ShippingZones;

/**
 * Class Create
 *
 * @package Walkthechat\Walkthechat\Service\Resource\ShippingZones
 */
class Create extends \Walkthechat\Walkthechat\Service\Resource\AbstractResource
{
    /**
     * @var string
     */
    protected $type = 'POST';

    /**
     * @var string
     */
    protected $path = 'shipping-zones';

    /**
     * @var array
     */
    protected $headers = [
        'Accept'       => "application/json, appl-header 'Content-Type: application/json",
        'Content-Type' => "application/json",
    ];
}

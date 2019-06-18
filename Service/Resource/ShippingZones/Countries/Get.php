<?php
/**
 * @package   WalktheChat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Service\Resource\ShippingZones\Countries;

/**
 * Class Get
 *
 * @package WalktheChat\Walkthechat\Service\Resource\ShippingZones\Countries
 */
class Get extends \WalktheChat\Walkthechat\Service\Resource\AbstractResource
{
    /**
     * @var string
     */
    protected $type = 'GET';

    /**
     * @var string
     */
    protected $path = 'shipping-zones/countries';
}

<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Service\Resource\ShippingZones\Rates;

/**
 * Class Find
 *
 * @package Walkthechat\Walkthechat\Service\Resource\ShippingZones\Rates
 */
class Find extends \Walkthechat\Walkthechat\Service\Resource\AbstractResource
{
    /**
     * @var string
     */
    protected $type = 'GET';

    /**
     * @var string
     */
    protected $path = 'shipping-zones/:id/rates/:fk';
}

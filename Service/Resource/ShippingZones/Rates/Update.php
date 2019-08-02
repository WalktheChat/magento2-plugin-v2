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
 * Class Update
 *
 * @package Walkthechat\Walkthechat\Service\Resource\ShippingZones\Rates
 */
class Update extends \Walkthechat\Walkthechat\Service\Resource\AbstractResource
{
    /**
     * @var string
     */
    protected $type = 'PUT';

    /**
     * @var string
     */
    protected $path = 'shipping-zones/:id/rates/:fk';
}

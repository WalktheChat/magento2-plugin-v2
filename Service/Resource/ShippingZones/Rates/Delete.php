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
 * Class Delete
 *
 * @package Walkthechat\Walkthechat\Service\Resource\ShippingZones\Rates
 */
class Delete extends \Walkthechat\Walkthechat\Service\Resource\AbstractResource
{
    /**
     * @var string
     */
    protected $type = 'DELETE';

    /**
     * @var string
     */
    protected $path = 'shipping-zones/:id/rates/:fk';
}

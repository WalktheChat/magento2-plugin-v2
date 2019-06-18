<?php
/**
 * @package   WalktheChat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Service\Resource\ShippingZones\Rates;

/**
 * Class Update
 *
 * @package WalktheChat\Walkthechat\Service\Resource\ShippingZones\Rates
 */
class Update extends \WalktheChat\Walkthechat\Service\Resource\AbstractResource
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

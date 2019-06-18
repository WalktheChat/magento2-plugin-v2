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
 * Class Create
 *
 * @package WalktheChat\Walkthechat\Service\Resource\ShippingZones\Rates
 */
class Create extends \WalktheChat\Walkthechat\Service\Resource\AbstractResource
{
    /**
     * @var string
     */
    protected $type = 'POST';

    /**
     * @var string
     */
    protected $path = 'shipping-zones/:id/rates';
}

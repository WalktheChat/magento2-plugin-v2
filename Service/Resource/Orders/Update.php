<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Service\Resource\Orders;

/**
 * Class Update
 *
 * @package Walkthechat\Walkthechat\Service\Resource\Orders
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
    protected $path = 'orders/:id';

    /**
     * @var array
     */
    protected $headers = [
        'Accept'       => "application/json, appl-header 'Content-Type: application/json",
        'Content-Type' => "application/json",
    ];
}

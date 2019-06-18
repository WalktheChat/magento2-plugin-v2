<?php
/**
 * @package   WalktheChat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Service\Resource\Orders;

/**
 * Class Update
 *
 * @package WalktheChat\Walkthechat\Service\Resource\Orders
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
    protected $path = 'orders/:id';

    /**
     * @var array
     */
    protected $headers = [
        'Accept'       => "application/json, appl-header 'Content-Type: application/json",
        'Content-Type' => "application/json",
    ];
}

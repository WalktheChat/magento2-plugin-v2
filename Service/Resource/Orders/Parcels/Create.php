<?php
/**
 * @package   WalktheChat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Service\Resource\Orders\Parcels;

/**
 * Class Create
 *
 * @package WalktheChat\Walkthechat\Service\Resource\Orders\Parcels
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
    protected $path = 'orders/:id/parcels';

    /**
     * @var array
     */
    protected $headers = [
        'Accept'       => "application/json, appl-header 'Content-Type: application/json",
        'Content-Type' => "application/json",
    ];
}

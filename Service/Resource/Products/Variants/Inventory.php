<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Service\Resource\Products\Variants;

/**
 * Class Inventory
 *
 * @package Walkthechat\Walkthechat\Service\Resource\Products\Variants
 */
class Inventory extends \Walkthechat\Walkthechat\Service\Resource\AbstractResource
{
    /**
     * @var string
     */
    protected $type = 'PATCH';

    /**
     * @var string
     */
    protected $path = 'products/variants/inventory';

    /**
     * @var array
     */
    protected $headers = [
        'Accept'       => 'application/json, application/xml, text/xml, application/javascript, text/javascript',
        'Content-Type' => 'application/json',
        'x-project-id' => ''
    ];
}

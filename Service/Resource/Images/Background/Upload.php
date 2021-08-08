<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Service\Resource\Images\Background;

/**
 * Class Upload
 * @package Walkthechat\Walkthechat\Service\Resource\Images\Background
 */
class Upload extends \Walkthechat\Walkthechat\Service\Resource\AbstractResource
{
    /**
     * @var string
     */
    protected $type = 'POST';

    /**
     * @var string
     */
    protected $path = 'images/background/upload';

    /**
     * @var array
     */
    protected $headers = [
        'Content-Type' => 'application/x-www-form-urlencoded',
        'x-project-id' => ''
    ];
}

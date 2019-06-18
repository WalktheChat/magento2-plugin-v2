<?php
/**
 * @package   WalktheChat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Service\Resource\Products;

/**
 * Class Delete
 *
 * @package WalktheChat\Walkthechat\Service\Resource\Products
 */
class Delete extends \WalktheChat\Walkthechat\Service\Resource\AbstractResource
{
    /**
     * @var string
     */
    protected $type = 'DELETE';

    /**
     * @var string
     */
    protected $path = 'products/:id';
}

<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model\Template;

/**
 * Class Filter
 *
 * @package Walkthechat\Walkthechat\Model\Template
 */
class Filter extends \Magento\Framework\Filter\Template
{
    /**
     * Retrieve media file path directive
     *
     * @param array $construction
     * @return string
     */
    public function mediaDirective($construction)
    {
        $params = $this->getParameters(html_entity_decode($construction[2], ENT_QUOTES));
        return $params['url'];
    }
}
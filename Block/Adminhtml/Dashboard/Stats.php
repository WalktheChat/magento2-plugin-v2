<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */
namespace Walkthechat\Walkthechat\Block\Adminhtml\Dashboard;

/**
 * Class Stats
 * @package Walkthechat\Walkthechat\Block\Adminhtml\Dashboard
 */
class Stats extends \Magento\Backend\Block\Template {
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'stats.phtml';

    /**
     * Get Stats URL
     *
     * @return mixed
     */
    public function getStatsUrl()
    {
        return $this->getUrl('walkthechat/dashboard/stats');
    }
}

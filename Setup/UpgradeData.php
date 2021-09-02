<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Setup;

/**
 * Class UpgradeData
 *
 * @package Walkthechat\Walkthechat\Setup
 */
class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    /*
     * @var \Walkthechat\Walkthechat\Model\QueueService
     */
    protected $queueService;
 
    /**
     * {@inheritdoc}
     *
     * @param \Walkthechat\Walkthechat\Model\QueueService $queueService
     */
    public function __construct(
        \Walkthechat\Walkthechat\Model\QueueService $queueService
    )
    {
        $this->queueService = $queueService;
    }
 
    /**
     * {@inheritdoc}
     */
    public function upgrade(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup, 
        \Magento\Framework\Setup\ModuleContextInterface $context 
    ) 
    {
        if (version_compare($context->getVersion(), '1.6.1', '<')) {
            $this->queueService->deleteNotExisting();
        }
    }
}
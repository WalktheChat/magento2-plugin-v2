<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Cron;

/**
 * Class CheckLockFiles
 *
 * @package Walkthechat\Walkthechat\Cron
 */
class CheckLockFiles
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;
    
    /**
     * @var integer
     */
    const LOCK_FILE_MINUTES = 30;

    /**
     * CheckLockFiles constructor.
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem
    ) 
    {
        $this->filesystem = $filesystem;
    }

    /**
     *
     * @throws \Magento\Framework\Exception\CronException
     * @throws \Exception
     */
    public function execute()
    {
        $directory = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
        
        if ($directory->isExist(\Walkthechat\Walkthechat\Cron\ProcessQueue::QUEUE_LOCK_FILE_NAME_LOCKED)) {
            $stat = $directory->stat(\Walkthechat\Walkthechat\Cron\ProcessQueue::QUEUE_LOCK_FILE_NAME_LOCKED);
            
            if (isset($stat['ctime']) && $stat['ctime'] < (time() - self::LOCK_FILE_MINUTES * 60)) {
                try {
                    $directory->renameFile(\Walkthechat\Walkthechat\Cron\ProcessQueue::QUEUE_LOCK_FILE_NAME_LOCKED, \Walkthechat\Walkthechat\Cron\ProcessQueue::QUEUE_LOCK_FILE_NAME_UNLOCKED);
                } catch (\Magento\Framework\Exception\FileSystemException $e) {
                    throw new \Magento\Framework\Exception\CronException(__('Unable to unlock the walkthechat queue.'));
                }
            }
        }
        
        if ($directory->isExist(\Walkthechat\Walkthechat\Cron\SyncImages::SYNC_IMAGES_LOCK_FILE_NAME_LOCKED)) {
            $stat = $directory->stat(\Walkthechat\Walkthechat\Cron\SyncImages::SYNC_IMAGES_LOCK_FILE_NAME_LOCKED);
            
            if (isset($stat['ctime']) && $stat['ctime'] < (time() - self::LOCK_FILE_MINUTES * 60)) {
                try {
                    $directory->renameFile(\Walkthechat\Walkthechat\Cron\SyncImages::SYNC_IMAGES_LOCK_FILE_NAME_LOCKED, \Walkthechat\Walkthechat\Cron\SyncImages::SYNC_IMAGES_LOCK_FILE_NAME_UNLOCKED);
                } catch (\Magento\Framework\Exception\FileSystemException $e) {
                    throw new \Magento\Framework\Exception\CronException(__('Unable to unlock the walkthechat sync images.'));
                }
            }
        }
        
        if ($directory->isExist(\Walkthechat\Walkthechat\Cron\SyncInventory::LOCK_FILE_NAME_LOCKED)) {
            $stat = $directory->stat(\Walkthechat\Walkthechat\Cron\SyncInventory::LOCK_FILE_NAME_LOCKED);
            
            if (isset($stat['ctime']) && $stat['ctime'] < (time() - self::LOCK_FILE_MINUTES * 60)) {
                try {
                    $directory->renameFile(\Walkthechat\Walkthechat\Cron\SyncInventory::LOCK_FILE_NAME_LOCKED, \Walkthechat\Walkthechat\Cron\SyncInventory::LOCK_FILE_NAME_UNLOCKED);
                } catch (\Magento\Framework\Exception\FileSystemException $e) {
                    throw new \Magento\Framework\Exception\CronException(__('Unable to unlock the walkthechat inventory.'));
                }
            }
        }
    }
}

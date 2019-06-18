<?php
/**
 * @package   WalktheChat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Cron;

/**
 * Class ProcessQueue
 *
 * @package WalktheChat\Walkthechat\Cron
 */
class ProcessQueue
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \WalktheChat\Walkthechat\Model\QueueService
     */
    protected $queueService;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $varDirectory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Locking file (locked)
     *
     * @var string
     */
    const QUEUE_LOCK_FILE_NAME_LOCKED = 'walkthechat_queue.lock';
    /**
     * Locking file (locked)
     *
     * @var string
     */
    const QUEUE_LOCK_FILE_NAME_UNLOCKED = 'walkthechat_queue';
    /**
     * Description of file
     *
     * @var string
     */
    const QUEUE_LOCK_FILE_CONTENT = 'This file was generated automatically by system to atomically prevent doubling of walkthechat entities. If this file extension is ".lock", then no queue items will be proceed';

    /**
     * How many files would be proceed per cron request
     *
     * @var int
     */
    const QUEUE_ITEMS_PER_PROCESS = 10;

    /**
     * ProcessQueue constructor.
     *
     * @param \Magento\Framework\App\State                    $state
     * @param \WalktheChat\Walkthechat\Model\QueueService         $queueService
     * @param \Magento\Framework\Registry                     $registry
     * @param \Magento\Framework\Filesystem                   $filesystem
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Psr\Log\LoggerInterface                        $logger
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        \WalktheChat\Walkthechat\Model\QueueService $queueService,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->state         = $state;
        $this->queueService  = $queueService;
        $this->registry      = $registry;
        $this->filesystem    = $filesystem;
        $this->directoryList = $directoryList;
        $this->logger        = $logger;
    }

    /**
     * Process items from queue
     *
     * @throws \Magento\Framework\Exception\CronException
     * @throws \Exception
     */
    public function execute()
    {
        $varDirectory = $this->filesystem->getDirectoryWrite(
            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR
        );

        try {
            if ($this->isCronLocked($varDirectory)) {
                return;
            }

            // prevent running many CRONs at the same time
            $isLocked = $this->lockCron($varDirectory);

            if ($isLocked) {
                $this->initAreaCode();

                // after saving walkthechat_id into product, set flag not to execute observer methods
                $this->registry->register('walkthechat_omit_update_action', true);

                $items = $this->queueService->getAllNotProcessed();

                $count = 0;

                foreach ($items as $item) {
                    if ($count++ === static::QUEUE_ITEMS_PER_PROCESS) {
                        break;
                    }

                    $this->registry->register('walkthechat_current_queue_item_id', $item->getId());

                    $this->queueService->sync($item);

                    $this->registry->unregister('walkthechat_current_queue_item_id');
                }
            }
        } catch (\Magento\Framework\Exception\FileSystemException $fileSystemException) {
            throw new \Magento\Framework\Exception\CronException(
                __('Unable to lock the cron. Please check your "var" folder permissions.')
            );
        } catch (\Exception $exception) {
            $this->logger->error(
                "WalkTheChat | Internal error occurred: {$exception->getMessage()}",
                $exception->getTrace()
            );
        } finally {
            $this->unlockCron($varDirectory);
        }
    }

    /**
     * Check if cron is locked
     * If file doesn't exists then trying to create it
     *
     * @param \Magento\Framework\Filesystem\Directory\WriteInterface $folderManager
     *
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function isCronLocked(\Magento\Framework\Filesystem\Directory\WriteInterface $folderManager)
    {
        if ($folderManager->isExist(self::QUEUE_LOCK_FILE_NAME_LOCKED)) {
            return true;
        }

        if (!$folderManager->isExist(self::QUEUE_LOCK_FILE_NAME_UNLOCKED)) {
            $folderManager->writeFile(self::QUEUE_LOCK_FILE_NAME_UNLOCKED, self::QUEUE_LOCK_FILE_CONTENT);
        }

        return false;
    }

    /**
     * Locks the cron
     *
     * @param \Magento\Framework\Filesystem\Directory\WriteInterface $folderManager
     *
     * @return bool
     */
    protected function lockCron(\Magento\Framework\Filesystem\Directory\WriteInterface $folderManager)
    {
        try {
            $folderManager->renameFile(self::QUEUE_LOCK_FILE_NAME_UNLOCKED, self::QUEUE_LOCK_FILE_NAME_LOCKED);
        } catch (\Magento\Framework\Exception\FileSystemException $e) {
            return false;
        }

        return true;
    }

    /**
     * Unlocks the cron
     *
     * @param \Magento\Framework\Filesystem\Directory\WriteInterface $folderManager
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CronException
     */
    protected function unlockCron(\Magento\Framework\Filesystem\Directory\WriteInterface $folderManager)
    {
        try {
            $folderManager->renameFile(self::QUEUE_LOCK_FILE_NAME_LOCKED, self::QUEUE_LOCK_FILE_NAME_UNLOCKED);
        } catch (\Magento\Framework\Exception\FileSystemException $e) {
            throw new \Magento\Framework\Exception\CronException(__('Unable to unlock the walkthechat queue.'));
        }

        return true;
    }

    /**
     * Initialize area code
     */
    protected function initAreaCode()
    {
        try {
            $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_CRONTAB);
        } catch (\Exception $exception) {
            // if area code was already set, then just continue work...
        }
    }
}

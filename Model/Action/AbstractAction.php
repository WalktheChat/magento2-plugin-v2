<?php
/**
 * @package   WalktheChat\Walkthechat
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Model\Action;

/**
 * Class AbstractAction
 *
 * @package WalktheChat\Walkthechat\Model\Action
 */
abstract class AbstractAction
{
    /**
     * @var \WalktheChat\Walkthechat\Api\Data\ImageSyncInterfaceFactory
     */
    protected $imageSyncFactory;

    /**
     * @var \WalktheChat\Walkthechat\Api\ImageSyncRepositoryInterface
     */
    protected $imageSyncRepository;

    /**
     * AbstractAction constructor.
     *
     * @param \WalktheChat\Walkthechat\Api\Data\ImageSyncInterfaceFactory $imageSyncFactory
     * @param \WalktheChat\Walkthechat\Api\ImageSyncRepositoryInterface   $imageSyncRepository
     */
    public function __construct(
        \WalktheChat\Walkthechat\Api\Data\ImageSyncInterfaceFactory $imageSyncFactory,
        \WalktheChat\Walkthechat\Api\ImageSyncRepositoryInterface $imageSyncRepository
    ) {
        $this->imageSyncFactory    = $imageSyncFactory;
        $this->imageSyncRepository = $imageSyncRepository;
    }

    /**
     * Execute action and return bool value depends on if process was successful
     *
     * @param \WalktheChat\Walkthechat\Api\Data\QueueInterface $queueItem
     *
     * @return bool
     */
    public abstract function execute(\WalktheChat\Walkthechat\Api\Data\QueueInterface $queueItem);

    /**
     * Saves images into image sync table
     *
     * @param array $data
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    protected function saveImagesToSyncTable(array $data)
    {
        foreach ($data as $item) {
            /** @var \WalktheChat\Walkthechat\Model\ImageSync $model */
            $model = $this->imageSyncFactory->create();

            $model->setData($item);

            $this->imageSyncRepository->save($model);
        }
    }
}

<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 Walkthechat
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model\Action;

/**
 * Class AbstractAction
 *
 * @package Walkthechat\Walkthechat\Model\Action
 */
abstract class AbstractAction
{
    /**
     * @var \Walkthechat\Walkthechat\Api\Data\ImageSyncInterfaceFactory
     */
    protected $imageSyncFactory;

    /**
     * @var \Walkthechat\Walkthechat\Api\ImageSyncRepositoryInterface
     */
    protected $imageSyncRepository;

    /**
     * AbstractAction constructor.
     *
     * @param \Walkthechat\Walkthechat\Api\Data\ImageSyncInterfaceFactory $imageSyncFactory
     * @param \Walkthechat\Walkthechat\Api\ImageSyncRepositoryInterface   $imageSyncRepository
     */
    public function __construct(
        \Walkthechat\Walkthechat\Api\Data\ImageSyncInterfaceFactory $imageSyncFactory,
        \Walkthechat\Walkthechat\Api\ImageSyncRepositoryInterface $imageSyncRepository
    ) {
        $this->imageSyncFactory    = $imageSyncFactory;
        $this->imageSyncRepository = $imageSyncRepository;
    }

    /**
     * Execute action and return bool value depends on if process was successful
     *
     * @param \Walkthechat\Walkthechat\Api\Data\QueueInterface $queueItem
     *
     * @return bool
     */
    public abstract function execute(\Walkthechat\Walkthechat\Api\Data\QueueInterface $queueItem);

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
            /** @var \Walkthechat\Walkthechat\Model\ImageSync $model */
            $model = $this->imageSyncFactory->create();

            $model->setData($item);

            $this->imageSyncRepository->save($model);
        }
    }
}

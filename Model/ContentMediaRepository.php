<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model;

/**
 * Class ContentMediaRepository
 *
 * @package Walkthechat\Walkthechat\Model
 */
class ContentMediaRepository implements \Walkthechat\Walkthechat\Api\ContentMediaRepositoryInterface
{
    /**
     * @var \Walkthechat\Walkthechat\Model\ResourceModel\ContentMedia\CollectionFactory
     */
    protected $contentMediaCollectionFactory;

    /**
     * @var \Walkthechat\Walkthechat\Api\Data\ContentMediaSearchResultsInterfaceFactory
     */
    protected $contentMediaSearchResultsInterfaceFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var \Walkthechat\Walkthechat\Model\ResourceModel\ContentMedia
     */
    protected $contentMediaResource;

    /**
     * ContentMediaRepository constructor.
     *
     * @param \Walkthechat\Walkthechat\Model\ResourceModel\ContentMedia\CollectionFactory $contentMediaCollectionFactory
     * @param \Walkthechat\Walkthechat\Api\Data\ContentMediaSearchResultsInterfaceFactory $contentMediaSearchResultsInterfaceFactory
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface   $collectionProcessor
     * @param \Walkthechat\Walkthechat\Model\ResourceModel\ContentMedia            $contentMediaResource
     */
    public function __construct(
        \Walkthechat\Walkthechat\Model\ResourceModel\ContentMedia\CollectionFactory $contentMediaCollectionFactory,
        \Walkthechat\Walkthechat\Api\Data\ContentMediaSearchResultsInterfaceFactory $contentMediaSearchResultsInterfaceFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \Walkthechat\Walkthechat\Model\ResourceModel\ContentMedia $contentMediaResource
    ) {
        $this->contentMediaCollectionFactory             = $contentMediaCollectionFactory;
        $this->contentMediaSearchResultsInterfaceFactory = $contentMediaSearchResultsInterfaceFactory;
        $this->collectionProcessor                       = $collectionProcessor;
        $this->contentMediaResource                      = $contentMediaResource;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Walkthechat\Walkthechat\Api\Data\ContentMediaInterface $contentMedia)
    {
        try {
            $this->contentMediaResource->save($contentMedia);
        } catch (\Magento\Framework\Exception\AlreadyExistsException $alreadyExistsException) {
            // if item is already exists in table then just ignore saving
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __('Could not save the image in content media sync table: %1', $exception->getMessage()),
                $exception
            );
        }

        return $contentMedia;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Walkthechat\Walkthechat\Model\ResourceModel\ContentMedia\Collection $collection */
        $collection = $this->contentMediaCollectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var \Walkthechat\Walkthechat\Api\Data\contentMediaSearchResultsInterface $searchResults */
        $searchResults = $this->contentMediaSearchResultsInterfaceFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}

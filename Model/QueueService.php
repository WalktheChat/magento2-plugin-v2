<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model;

/**
 * Class QueueService
 *
 * @package Walkthechat\Walkthechat\Model
 */
class QueueService
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Walkthechat\Walkthechat\Api\QueueRepositoryInterface
     */
    protected $queueRepository;

    /**
     * @var \Walkthechat\Walkthechat\Model\ActionFactory
     */
    protected $actionFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroupBuilder
     */
    protected $filterGroupBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    
    /**
     * @var \Walkthechat\Walkthechat\Helper\Data
     */
    protected $helper;

    /**
     * QueueService constructor.
     * @param \Magento\Catalog\Model\ProductRepository
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Walkthechat\Walkthechat\Api\QueueRepositoryInterface $queueRepository
     * @param ActionFactory $actionFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Walkthechat\Walkthechat\Helper\Data $helper
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Walkthechat\Walkthechat\Api\QueueRepositoryInterface $queueRepository,
        \Walkthechat\Walkthechat\Model\ActionFactory $actionFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Psr\Log\LoggerInterface $logger,
        \Walkthechat\Walkthechat\Helper\Data $helper
    ) {
        $this->productRepository     = $productRepository;
        $this->date                  = $date;
        $this->queueRepository       = $queueRepository;
        $this->actionFactory         = $actionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterGroupBuilder    = $filterGroupBuilder;
        $this->filterBuilder         = $filterBuilder;
        $this->logger                = $logger;
        $this->helper                = $helper;
    }

    /**
     * Count new not processed
     *
     * @return int
     */
    public function countNewNotProcessed()
    {
        $filterGroup1 = $this->filterGroupBuilder
            ->addFilter(
                $this->filterBuilder
                    ->setField('processed_at')
                    ->setConditionType('null')
                    ->setValue(true)
                    ->create()
            )
            ->create();

        $filterGroup2 = $this->filterGroupBuilder
            ->addFilter(
                $this->filterBuilder
                    ->setField('action')
                    ->setConditionType('eq')
                    ->setValue(\Walkthechat\Walkthechat\Model\Action\Add::ACTION)
                    ->create()
            )
            ->create();

        $this->searchCriteriaBuilder->setFilterGroups([$filterGroup1, $filterGroup2]);

        /** @var \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder->create();

        return $this->queueRepository->getList($searchCriteria)->getTotalCount();
    }

    /**
     * Get not processed rows
     *
     * @param int|null $pageSize
     * @return \Walkthechat\Walkthechat\Api\Data\QueueInterface[]
     */
    public function getNotProcessed($pageSize = null)
    {
        $this->searchCriteriaBuilder
            ->addFilter('processed_at', true, 'null')
            ->addFilter('status', \Walkthechat\Walkthechat\Api\Data\QueueInterface::INTERNAL_ERROR_STATUS, 'neq');

        /** @var \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder->create();

        if ($pageSize) {
            $searchCriteria->setPageSize($pageSize);
        }

        $results = $this->queueRepository->getList($searchCriteria);

        return $results->getItems();
    }

    /**
     * Check if has duplicated items
     *
     * @param int|string $id
     * @param string     $action
     * @param string     $idField
     *
     * @return bool
     */
    public function isDuplicate($id, $action, $idField)
    {
        if ($action == \Walkthechat\Walkthechat\Model\Action\Update::ACTION) {
            $this->searchCriteriaBuilder->addFilter('action', \Walkthechat\Walkthechat\Model\Action\Delete::ACTION);
            $this->searchCriteriaBuilder->addFilter($idField, $id);
            $this->searchCriteriaBuilder->addFilter('processed_at', true, 'null');
            
            /** @var \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria */
            $searchCriteria = $this->searchCriteriaBuilder->create();
            
            if ($this->queueRepository->getList($searchCriteria)->getItems()) {
                return true;
            }
        } 
        
        $this->searchCriteriaBuilder->addFilter('action', $action);
        $this->searchCriteriaBuilder->addFilter($idField, $id);
        $this->searchCriteriaBuilder->addFilter('processed_at', true, 'null');
        
        /** @var \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder->create();

        $results = $this->queueRepository->getList($searchCriteria);

        return (bool)$results->getItems();
    }
    
    
    public function deleteNotExisting()
    {
        $this->searchCriteriaBuilder->addFilter('action', \Walkthechat\Walkthechat\Model\Action\Update::ACTION);
        $this->searchCriteriaBuilder->addFilter('processed_at', true, 'null');
        
        /** @var \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder->create();
        
        foreach ($this->queueRepository->getList($searchCriteria)->getItems() as $item) {
            if ($item->getProductId()) {
                $exists = false;
                
                $product = $this->productRepository->getById($item->getProductId(), false, $this->helper->getStore()->getId());

                if ($product->getId()) {
                    $walkTheChatId = $this->helper->getWalkTheChatAttributeValue($product);
                    
                    if ($walkTheChatId) {
                        $exists = true;
                    }
                }
                
                if (!$exists) {
                    $item->delete();
                }
            }
        }
    }

    /**
     * Sync item with Walkthechat
     *
     * @param \Walkthechat\Walkthechat\Api\Data\QueueInterface $item
     *
     * @throws \Exception
     */
    public function sync(\Walkthechat\Walkthechat\Api\Data\QueueInterface $item)
    {
        $action = $this->actionFactory->create($item->getAction());

        try {
            $isSuccess = $action->execute($item);

            if ($isSuccess) {
                $item->setProcessedAt($this->date->gmtDate());
                $item->setStatus(\Walkthechat\Walkthechat\Api\Data\QueueInterface::COMPLETE_STATUS);
            }
        } catch (\Zend\Http\Client\Exception\RuntimeException $runtimeException) {
            $item->setStatus(\Walkthechat\Walkthechat\Api\Data\QueueInterface::API_ERROR_STATUS);
            $this->logger->error(
                "WalkTheChat | Bad response when trying to proceed the queue item with ID: #{$item->getId()}. Please check logs in admin panel (WalkTheChat -> Logs) for more details."
            );
        } catch (\Magento\Framework\Exception\CouldNotSaveException $exception) {
            $item->setStatus(\Walkthechat\Walkthechat\Api\Data\QueueInterface::INTERNAL_ERROR_STATUS);
        } catch (\Exception $exception) {
            $item->setStatus(\Walkthechat\Walkthechat\Api\Data\QueueInterface::INTERNAL_ERROR_STATUS);

            $this->logger->critical(
                "WalkTheChat | Internal error occurred: {$exception->getMessage()}",
                $exception->getTrace()
            );
        }

        $this->queueRepository->save($item);
    }
}

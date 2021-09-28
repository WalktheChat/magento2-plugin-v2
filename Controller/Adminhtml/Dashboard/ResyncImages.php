<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2021 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Controller\Adminhtml\Dashboard;

/**
 * Class ResyncImages
 *
 * @package Walkthechat\Walkthechat\Controller\Adminhtml\Dashboard
 */
class ResyncImages extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * {@inheritdoc}
     *
     * @param \Magento\Ui\Component\MassAction\Filter                        $filter
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Event\ManagerInterface                      $eventManager
     * @param \Psr\Log\LoggerInterface                                       $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->filter            = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->eventManager      = $eventManager;
        $this->logger            = $logger;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     *
     * Export selected products to Walkthechat
     */
    public function execute()
    {
        try {
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            
            foreach ($collection->getItems() as $product) {
                $this->eventManager->dispatch(
                    'walkthechat_reset_images',
                    ['product' => $product]
                );
            }

            $this->messageManager->addSuccessMessage(__('%1 product(s) added to refresh images queue.', count($collection->getItems())));
        } catch (\Magento\Framework\Exception\LocalizedException $localizedException) {
            $this->messageManager->addErrorMessage(__($localizedException->getMessage()));
        } catch (\Exception $exception) {
            $this->logger->critical($exception);

            $this->messageManager->addErrorMessage(__('Internal error occurred. Please see logs or contact administrator.'));
        }

        $this->_redirect('walkthechat/dashboard/products');
    }
}

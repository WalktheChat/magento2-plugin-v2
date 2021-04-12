<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Controller\Adminhtml\Dashboard;

/**
 * Class ResyncProducts
 *
 * @package Walkthechat\Walkthechat\Controller\Adminhtml\Dashboard
 */
class ResyncProducts extends \Magento\Backend\App\Action
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
     * @var \Walkthechat\Walkthechat\Model\ProductService
     */
    protected $productService;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * {@inheritdoc}
     *
     * @param \Magento\Ui\Component\MassAction\Filter                        $filter
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Walkthechat\Walkthechat\Model\ProductService                      $productService
     * @param \Psr\Log\LoggerInterface                                       $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Walkthechat\Walkthechat\Model\ProductService $productService,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->filter            = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->productService    = $productService;
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
            $bulkData   = $this->productService->processProductsExport($collection->getItems());

            $this->messageManager->addSuccessMessage(__('%1 product(s) added to queue.', count($bulkData)));
        } catch (\Magento\Framework\Exception\LocalizedException $localizedException) {
            $this->messageManager->addErrorMessage(__($localizedException->getMessage()));
        } catch (\Exception $exception) {
            $this->logger->critical($exception);

            $this->messageManager->addErrorMessage(__('Internal error occurred. Please see logs or contact administrator.'));
        }

        $this->_redirect('walkthechat/dashboard/products');
    }
}

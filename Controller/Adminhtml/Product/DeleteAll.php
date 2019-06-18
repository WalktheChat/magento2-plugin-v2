<?php
/**
 * @package   WalktheChat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Controller\Adminhtml\Product;

/**
 * Class DeleteAll
 *
 * @package WalktheChat\Walkthechat\Controller\Adminhtml\Product
 */
class DeleteAll extends \Magento\Backend\App\Action
{
    /**
     * @var \WalktheChat\Walkthechat\Model\ProductService
     */
    protected $productService;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * {@inheritdoc}
     *
     * @param \WalktheChat\Walkthechat\Model\ProductService $productService
     * @param \Psr\Log\LoggerInterface                  $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \WalktheChat\Walkthechat\Model\ProductService $productService,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->productService = $productService;
        $this->logger         = $logger;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     *
     * Delete all existing products from Walkthechat
     */
    public function execute()
    {
        try {
            $products = $this->productService->getAllForDelete();
            $bulkData = $this->productService->processProductDelete($products, $this->messageManager);

            $this->messageManager->addSuccessMessage(__('%1 product(s) added to queue.', count($bulkData)));
        } catch (\Magento\Framework\Exception\LocalizedException $localizedException) {
            $this->messageManager->addErrorMessage(__($localizedException->getMessage()));
        } catch (\Exception $exception) {
            $this->logger->critical($exception);

            $this->messageManager->addErrorMessage(__('Internal error occurred. Please see logs or contact administrator.'));
        }

        $this->_redirect('*/dashboard/index');
    }
}

<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model\Action;

/**
 * Class Delete
 *
 * @package Walkthechat\Walkthechat\Model\Action
 */
class Delete extends \Walkthechat\Walkthechat\Model\Action\AbstractAction
{
    /**
     * Action name
     *
     * @string
     */
    const ACTION = 'delete';

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Walkthechat\Walkthechat\Service\ProductsRepository
     */
    protected $queueProductRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * {@inheritdoc}
     *
     * @param \Magento\Catalog\Model\ProductRepository                       $productRepository
     * @param \Walkthechat\Walkthechat\Service\ProductsRepository                $queueProductRepository
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    public function __construct(
        \Walkthechat\Walkthechat\Api\Data\ImageSyncInterfaceFactory $imageSyncFactory,
        \Walkthechat\Walkthechat\Api\ImageSyncRepositoryInterface $imageSyncRepository,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Walkthechat\Walkthechat\Service\ProductsRepository $queueProductRepository,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        $this->productRepository        = $productRepository;
        $this->queueProductRepository   = $queueProductRepository;
        $this->productCollectionFactory = $productCollectionFactory;

        parent::__construct(
            $imageSyncFactory,
            $imageSyncRepository
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Zend_Http_Client_Exception
     * @throws \Exception
     */
    public function execute(\Walkthechat\Walkthechat\Api\Data\QueueInterface $queueItem)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $deleteProductCollection */
        $deleteProductCollection = $this->productCollectionFactory->create();

        $deleteProductCollection->addAttributeToFilter(
            \Walkthechat\Walkthechat\Helper\Data::ATTRIBUTE_CODE,
            $queueItem->getWalkthechatId()
        );

        foreach ($deleteProductCollection as $product) {
            $product = $this->productRepository->getById($product->getId(), false);
            $product->getResource()->validate($product);
        }

        $this->queueProductRepository->delete(['id' => $queueItem->getWalkthechatId()]);

        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        foreach ($deleteProductCollection as $product) {
            $product->setWalkthechatId(null);
            $product->getResource()->saveAttribute($product, \Walkthechat\Walkthechat\Helper\Data::ATTRIBUTE_CODE);
        }

        return true;
    }
}

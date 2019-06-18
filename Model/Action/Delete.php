<?php
/**
 * @package   WalktheChat\Walkthechat
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Model\Action;

/**
 * Class Delete
 *
 * @package WalktheChat\Walkthechat\Model\Action
 */
class Delete extends \WalktheChat\Walkthechat\Model\Action\AbstractAction
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
     * @var \WalktheChat\Walkthechat\Service\ProductsRepository
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
     * @param \WalktheChat\Walkthechat\Service\ProductsRepository                $queueProductRepository
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    public function __construct(
        \WalktheChat\Walkthechat\Api\Data\ImageSyncInterfaceFactory $imageSyncFactory,
        \WalktheChat\Walkthechat\Api\ImageSyncRepositoryInterface $imageSyncRepository,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \WalktheChat\Walkthechat\Service\ProductsRepository $queueProductRepository,
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
    public function execute(\WalktheChat\Walkthechat\Api\Data\QueueInterface $queueItem)
    {
        $this->queueProductRepository->delete(['id' => $queueItem->getWalkthechatId()]);

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $deleteProductCollection */
        $deleteProductCollection = $this->productCollectionFactory->create();

        $deleteProductCollection->addAttributeToFilter(
            \WalktheChat\Walkthechat\Helper\Data::ATTRIBUTE_CODE,
            $queueItem->getWalkthechatId()
        );

        $productIds = [];

        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        foreach ($deleteProductCollection as $product) {
            $product->setCustomAttribute(\WalktheChat\Walkthechat\Helper\Data::ATTRIBUTE_CODE, null);

            $this->productRepository->save($product);

            if ($product->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                /** @var \Magento\Catalog\Model\Product[] $children */
                $children = $product->getTypeInstance()->getUsedProducts($product);

                if ($children) {
                    foreach ($children as $child) {
                        $productIds[] = $child->getId();
                    }
                }
            } else {
                $productIds[] = $product->getId();
            }
        }

        if ($productIds) {
            $this->imageSyncRepository->deleteByProductIds($productIds);
        }

        return true;
    }
}

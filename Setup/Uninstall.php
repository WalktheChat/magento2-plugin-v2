<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2018 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Setup;

/**
 * Class Uninstall
 *
 * @package Walkthechat\Walkthechat\Setup
 */
class Uninstall implements \Magento\Framework\Setup\UninstallInterface
{
    /**
     * EAV setup factory
     *
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * Removing eav attributes
     *
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $dataSetup;

    /**
     * Init
     *
     * @param \Magento\Eav\Setup\EavSetupFactory                $eavSetupFactory
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $dataSetup
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Framework\Setup\ModuleDataSetupInterface $dataSetup
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->dataSetup       = $dataSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $connection = $setup->getConnection();

        // drop module tables
        $connection->dropTable(\Walkthechat\Walkthechat\Model\ResourceModel\ApiLog::TABLE_NAME);
        $connection->dropTable(\Walkthechat\Walkthechat\Model\ResourceModel\Queue::TABLE_NAME);
        $connection->dropTable(\Walkthechat\Walkthechat\Model\ResourceModel\ImageSync::TABLE_NAME);

        // drop module integrated columns
        $connection->dropColumn('sales_order', \Walkthechat\Walkthechat\Helper\Data::ATTRIBUTE_CODE);
        $connection->dropColumn('sales_order_item', 'walkthechat_item_data');
        $connection->dropColumn('sales_shipment', 'is_sent_to_walk_the_chat');
        $connection->dropColumn('sales_creditmemo', 'is_sent_to_walk_the_chat');

        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->dataSetup]);

        // remove walkthechat_id attribute from product eav entity
        $eavSetup->removeAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            \Walkthechat\Walkthechat\Helper\Data::ATTRIBUTE_CODE
        );

        $setup->endSetup();
    }
}

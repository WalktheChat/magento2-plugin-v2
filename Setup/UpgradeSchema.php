<?php
/**
 * @package   WalktheChat\Walkthechat
 *
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Setup;

/**
 * Class UpgradeSchema
 *
 * @package WalktheChat\Walkthechat\Setup
 */
class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \Zend_Db_Exception
     */
    public function upgrade(
        \Magento\Framework\Setup\SchemaSetupInterface $installer,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        if (version_compare($context->getVersion(), '0.2.0', '<')) {
            $this->createImageSyncTable($installer);
        }

        if (version_compare($context->getVersion(), '0.3.0', '<')) {
            $this->addOrderWalkthechatItemDataField($installer);
        }

        if (version_compare($context->getVersion(), '0.4.1', '<')) {
            $this->addIsSynchronizedWithWalkTheChatField($installer);
        }

        if (version_compare($context->getVersion(), '0.5.0', '<')) {
            $this->addQueueItemIdFieldInApiLogTable($installer);
        }

        if (version_compare($context->getVersion(), '0.6.0', '<')) {
            $this->addStatusFieldInQueueTable($installer);
        }
    }

    /**
     * Creates image sync table
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     *
     * @return $this
     * @throws \Zend_Db_Exception
     */
    protected function createImageSyncTable(\Magento\Framework\Setup\SchemaSetupInterface $installer)
    {
        if (!$installer->tableExists(\WalktheChat\Walkthechat\Model\ResourceModel\ImageSync::TABLE_NAME)) {
            $table = $installer
                ->getConnection()
                ->newTable($installer->getTable(\WalktheChat\Walkthechat\Model\ResourceModel\ImageSync::TABLE_NAME))
                ->addColumn(
                    \WalktheChat\Walkthechat\Api\Data\ImageSyncInterface::ID,
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary'  => true,
                    ],
                    'Entity ID'
                )
                ->addColumn(
                    \WalktheChat\Walkthechat\Api\Data\ImageSyncInterface::PRODUCT_ID,
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => true,
                        'unsigned' => true,
                        'default'  => null,
                    ],
                    'Magento Product Id'
                )
                ->addColumn(
                    \WalktheChat\Walkthechat\Api\Data\ImageSyncInterface::IMAGE_ID,
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => true,
                        'unsigned' => true,
                        'default'  => null,
                    ],
                    'Image Product Id'
                )
                ->addColumn(
                    \WalktheChat\Walkthechat\Api\Data\ImageSyncInterface::IMAGE_DATA,
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => false,
                    ],
                    'Image Data'
                )
                ->addForeignKey(
                    $installer->getFkName(
                        \WalktheChat\Walkthechat\Model\ResourceModel\ImageSync::TABLE_NAME,
                        \WalktheChat\Walkthechat\Api\Data\ImageSyncInterface::PRODUCT_ID,
                        'catalog_product_entity',
                        'entity_id'
                    ),
                    \WalktheChat\Walkthechat\Api\Data\ImageSyncInterface::PRODUCT_ID,
                    $installer->getTable('catalog_product_entity'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName(
                        \WalktheChat\Walkthechat\Model\ResourceModel\ImageSync::TABLE_NAME,
                        \WalktheChat\Walkthechat\Api\Data\ImageSyncInterface::IMAGE_ID,
                        'catalog_product_entity_media_gallery',
                        'value_id'
                    ),
                    \WalktheChat\Walkthechat\Api\Data\ImageSyncInterface::IMAGE_ID,
                    $installer->getTable('catalog_product_entity_media_gallery'),
                    'value_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addIndex(
                    $installer->getIdxName(
                        \WalktheChat\Walkthechat\Model\ResourceModel\ImageSync::TABLE_NAME,
                        [
                            \WalktheChat\Walkthechat\Api\Data\ImageSyncInterface::PRODUCT_ID,
                            \WalktheChat\Walkthechat\Api\Data\ImageSyncInterface::IMAGE_ID,
                        ],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    [
                        \WalktheChat\Walkthechat\Api\Data\ImageSyncInterface::PRODUCT_ID,
                        \WalktheChat\Walkthechat\Api\Data\ImageSyncInterface::IMAGE_ID,
                    ],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->setComment('WalkTheChat image synchronization table');

            $installer->getConnection()->createTable($table);
        }

        return $this;
    }

    /**
     * Add json formatted field for walkthechat representation item
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     */
    protected function addOrderWalkthechatItemDataField(\Magento\Framework\Setup\SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();

        if (!$connection->tableColumnExists('sales_order_item', 'walkthechat_item_data')) {
            $connection
                ->addColumn(
                    $installer->getTable('sales_order_item'),
                    'walkthechat_item_data',
                    [
                        'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length'  => null,
                        'comment' => 'WalkTheChat item data',
                    ]
                );
        }
    }

    /**
     * Add status field in queue table
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     */
    protected function addStatusFieldInQueueTable(
        \Magento\Framework\Setup\SchemaSetupInterface $installer
    ) {
        $connection = $installer->getConnection();

        if (!$connection->tableColumnExists(
            \WalktheChat\Walkthechat\Model\ResourceModel\Queue::TABLE_NAME,
            \WalktheChat\Walkthechat\Api\Data\QueueInterface::STATUS
        )) {
            $connection
                ->addColumn(
                    $installer->getTable(\WalktheChat\Walkthechat\Model\ResourceModel\Queue::TABLE_NAME),
                    \WalktheChat\Walkthechat\Api\Data\QueueInterface::STATUS,
                    [
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'nullable' => false,
                        'default'  => \WalktheChat\Walkthechat\Api\Data\QueueInterface::WAITING_IN_QUEUE_STATUS,
                        'unsigned' => true,
                        'length'   => null,
                        'comment'  => 'Queue item status',
                    ]
                );
        }
    }

    /**
     * Add queue item field for api log table
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     */
    protected function addQueueItemIdFieldInApiLogTable(\Magento\Framework\Setup\SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();

        if ($connection->isTableExists(\WalktheChat\Walkthechat\Model\ResourceModel\ApiLog::TABLE_NAME)) {
            $connection->truncateTable(\WalktheChat\Walkthechat\Model\ResourceModel\ApiLog::TABLE_NAME);
        }

        if (!$connection->tableColumnExists(
            \WalktheChat\Walkthechat\Model\ResourceModel\ApiLog::TABLE_NAME,
            \WalktheChat\Walkthechat\Api\Data\ApiLogInterface::QUEUE_ITEM_ID_FIELD
        )) {
            $connection
                ->addColumn(
                    $installer->getTable(\WalktheChat\Walkthechat\Model\ResourceModel\ApiLog::TABLE_NAME),
                    \WalktheChat\Walkthechat\Api\Data\ApiLogInterface::QUEUE_ITEM_ID_FIELD,
                    [
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => false,
                        'unsigned' => true,
                        'length'   => null,
                        'comment'  => 'Queue item ID',
                    ]
                );

            $connection
                ->addForeignKey(
                    $connection->getForeignKeyName(
                        \WalktheChat\Walkthechat\Model\ResourceModel\ApiLog::TABLE_NAME,
                        \WalktheChat\Walkthechat\Api\Data\ApiLogInterface::QUEUE_ITEM_ID_FIELD,
                        \WalktheChat\Walkthechat\Model\ResourceModel\Queue::TABLE_NAME,
                        \WalktheChat\Walkthechat\Api\Data\QueueInterface::ID
                    ),
                    \WalktheChat\Walkthechat\Model\ResourceModel\ApiLog::TABLE_NAME,
                    \WalktheChat\Walkthechat\Api\Data\ApiLogInterface::QUEUE_ITEM_ID_FIELD,
                    \WalktheChat\Walkthechat\Model\ResourceModel\Queue::TABLE_NAME,
                    \WalktheChat\Walkthechat\Api\Data\QueueInterface::ID
                );
        }
    }

    /**
     * Add field "is_synchronized_with_walk_the_chat" to "sales_shipment" and "sales_creditmemo" tables
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     */
    protected function addIsSynchronizedWithWalkTheChatField(
        \Magento\Framework\Setup\SchemaSetupInterface $installer
    ) {
        $connection = $installer->getConnection();
        $fieldName  = 'is_sent_to_walk_the_chat';

        if (!$connection->tableColumnExists('sales_shipment', $fieldName)) {
            $connection
                ->addColumn(
                    $installer->getTable('sales_shipment'),
                    $fieldName,
                    [
                        'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                        'default' => '0',
                        'comment' => 'Is parcel sent to WalkTheChat?',
                    ]
                );
        }

        if (!$connection->tableColumnExists('sales_creditmemo', $fieldName)) {
            $connection
                ->addColumn(
                    $installer->getTable('sales_creditmemo'),
                    $fieldName,
                    [
                        'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                        'default' => '0',
                        'comment' => 'Is refund sent to WalkTheChat?',
                    ]
                );
        }
    }
}

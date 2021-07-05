<?php
/**
 * @package   Walkthechat\Walkthechat
 *
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 *
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Setup;

/**
 * Class UpgradeSchema
 *
 * @package Walkthechat\Walkthechat\Setup
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

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->createContentMediaTable($installer);
        }

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->createWalkTheChatOrderNameAttribute($installer);
        }

        if (version_compare($context->getVersion(), '1.3.0', '<')) {
            $this->createWalkTheChatOrderNameOnGrid($installer);
        }

        if (version_compare($context->getVersion(), '1.4.0', '<')) {
            $this->makeQueueItemIdFieldNullable($installer);
        }

        if (version_compare($context->getVersion(), '1.5.0', '<')) {
            $this->createOrderTable($installer);
        }

        if (version_compare($context->getVersion(), '1.6.0', '<')) {
            $this->createInventoryTable($installer);
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
        if (!$installer->tableExists($installer->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\ImageSync::TABLE_NAME))) {
            $table = $installer
                ->getConnection()
                ->newTable($installer->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\ImageSync::TABLE_NAME))
                ->addColumn(
                    \Walkthechat\Walkthechat\Api\Data\ImageSyncInterface::ID,
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
                    \Walkthechat\Walkthechat\Api\Data\ImageSyncInterface::PRODUCT_ID,
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
                    \Walkthechat\Walkthechat\Api\Data\ImageSyncInterface::IMAGE_ID,
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
                    \Walkthechat\Walkthechat\Api\Data\ImageSyncInterface::IMAGE_DATA,
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => false,
                    ],
                    'Image Data'
                )
                ->addForeignKey(
                    $installer->getFkName(
                        \Walkthechat\Walkthechat\Model\ResourceModel\ImageSync::TABLE_NAME,
                        \Walkthechat\Walkthechat\Api\Data\ImageSyncInterface::PRODUCT_ID,
                        'catalog_product_entity',
                        'entity_id'
                    ),
                    \Walkthechat\Walkthechat\Api\Data\ImageSyncInterface::PRODUCT_ID,
                    $installer->getTable('catalog_product_entity'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName(
                        \Walkthechat\Walkthechat\Model\ResourceModel\ImageSync::TABLE_NAME,
                        \Walkthechat\Walkthechat\Api\Data\ImageSyncInterface::IMAGE_ID,
                        'catalog_product_entity_media_gallery',
                        'value_id'
                    ),
                    \Walkthechat\Walkthechat\Api\Data\ImageSyncInterface::IMAGE_ID,
                    $installer->getTable('catalog_product_entity_media_gallery'),
                    'value_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addIndex(
                    $installer->getIdxName(
                        \Walkthechat\Walkthechat\Model\ResourceModel\ImageSync::TABLE_NAME,
                        [
                            \Walkthechat\Walkthechat\Api\Data\ImageSyncInterface::PRODUCT_ID,
                            \Walkthechat\Walkthechat\Api\Data\ImageSyncInterface::IMAGE_ID,
                        ],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    [
                        \Walkthechat\Walkthechat\Api\Data\ImageSyncInterface::PRODUCT_ID,
                        \Walkthechat\Walkthechat\Api\Data\ImageSyncInterface::IMAGE_ID,
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

        if (!$connection->tableColumnExists($installer->getTable('sales_order_item'), 'walkthechat_item_data')) {
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
            $installer->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\Queue::TABLE_NAME),
            \Walkthechat\Walkthechat\Api\Data\QueueInterface::STATUS
        )) {
            $connection
                ->addColumn(
                    $installer->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\Queue::TABLE_NAME),
                    \Walkthechat\Walkthechat\Api\Data\QueueInterface::STATUS,
                    [
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'nullable' => false,
                        'default'  => \Walkthechat\Walkthechat\Api\Data\QueueInterface::WAITING_IN_QUEUE_STATUS,
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

        if ($connection->isTableExists($installer->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\ApiLog::TABLE_NAME))) {
            $connection->truncateTable($installer->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\ApiLog::TABLE_NAME));
        }

        if (!$connection->tableColumnExists(
            $installer->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\ApiLog::TABLE_NAME),
            \Walkthechat\Walkthechat\Api\Data\ApiLogInterface::QUEUE_ITEM_ID_FIELD
        )) {
            $connection
                ->addColumn(
                    $installer->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\ApiLog::TABLE_NAME),
                    \Walkthechat\Walkthechat\Api\Data\ApiLogInterface::QUEUE_ITEM_ID_FIELD,
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
                        $installer->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\ApiLog::TABLE_NAME),
                        \Walkthechat\Walkthechat\Api\Data\ApiLogInterface::QUEUE_ITEM_ID_FIELD,
                        $installer->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\Queue::TABLE_NAME),
                        \Walkthechat\Walkthechat\Api\Data\QueueInterface::ID
                    ),
                    $installer->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\ApiLog::TABLE_NAME),
                    \Walkthechat\Walkthechat\Api\Data\ApiLogInterface::QUEUE_ITEM_ID_FIELD,
                    $installer->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\Queue::TABLE_NAME),
                    \Walkthechat\Walkthechat\Api\Data\QueueInterface::ID
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

        if (!$connection->tableColumnExists($installer->getTable('sales_shipment'), $fieldName)) {
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

        if (!$connection->tableColumnExists($installer->getTable('sales_creditmemo'), $fieldName)) {
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


    /**
     * Creates content media table
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     *
     * @return $this
     * @throws \Zend_Db_Exception
     */
    protected function createContentMediaTable(\Magento\Framework\Setup\SchemaSetupInterface $installer)
    {
        if (!$installer->tableExists($installer->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\ContentMedia::TABLE_NAME))) {
            $table = $installer
                ->getConnection()
                ->newTable($installer->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\ContentMedia::TABLE_NAME))
                ->addColumn(
                    \Walkthechat\Walkthechat\Api\Data\ContentMediaInterface::ID,
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
                    \Walkthechat\Walkthechat\Api\Data\ContentMediaInterface::IMAGE_PATH,
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false,
                    ],
                    'Image Path'
                )
                ->addColumn(
                    \Walkthechat\Walkthechat\Api\Data\ImageSyncInterface::IMAGE_DATA,
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => false,
                    ],
                    'Image Data'
                )
                ->setComment('WalkTheChat content media synchronization table');

            $installer->getConnection()->createTable($table);
        }

        return $this;
    }

    /**
     * Create new walkthechat name column in order table
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     */
    protected function createWalkTheChatOrderNameAttribute(\Magento\Framework\Setup\SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();

        if (!$connection->tableColumnExists($installer->getTable('sales_order'), \Walkthechat\Walkthechat\Helper\Data::ATTRIBUTE_NAME_CODE)) {
            $connection
                ->addColumn(
                    $installer->getTable('sales_order'),
                    \Walkthechat\Walkthechat\Helper\Data::ATTRIBUTE_NAME_CODE,
                    [
                        'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length'  => 255,
                        'comment' => 'WalkTheChat Name',
                    ]
                );
        }
    }

    /**
     * Create new walkthechat name column in order grid table
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     */
    protected function createWalkTheChatOrderNameOnGrid(\Magento\Framework\Setup\SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();

        if (!$connection->tableColumnExists($installer->getTable('sales_order_grid'), \Walkthechat\Walkthechat\Helper\Data::ATTRIBUTE_NAME_CODE)) {
            $connection
                ->addColumn(
                    $installer->getTable('sales_order_grid'),
                    \Walkthechat\Walkthechat\Helper\Data::ATTRIBUTE_NAME_CODE,
                    [
                        'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length'  => 255,
                        'comment' => 'WalkTheChat Name',
                    ]
                );
        }
    }

    /**
     * Make queue item field nullable in api log table
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     */
    protected function makeQueueItemIdFieldNullable(\Magento\Framework\Setup\SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();

        if ($connection->tableColumnExists(
            $installer->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\ApiLog::TABLE_NAME),
            \Walkthechat\Walkthechat\Api\Data\ApiLogInterface::QUEUE_ITEM_ID_FIELD
        )) {
            $connection
                ->changeColumn(
                    $installer->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\ApiLog::TABLE_NAME),
                    \Walkthechat\Walkthechat\Api\Data\ApiLogInterface::QUEUE_ITEM_ID_FIELD,
                    \Walkthechat\Walkthechat\Api\Data\ApiLogInterface::QUEUE_ITEM_ID_FIELD,
                    [
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'unsigned' => true,
                        'length'   => null,
                        'comment'  => 'Queue item ID',
                        'default'  => null
                    ]
                );
        }
    }

    /**
     * Creates order table
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     *
     * @return $this
     * @throws \Zend_Db_Exception
     */
    protected function createOrderTable(\Magento\Framework\Setup\SchemaSetupInterface $installer)
    {
        if (!$installer->tableExists($installer->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\Order::TABLE_NAME))) {
            $table = $installer
                ->getConnection()
                ->newTable($installer->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\Order::TABLE_NAME))
                ->addColumn(
                    \Walkthechat\Walkthechat\Api\Data\OrderInterface::ID,
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
                    \Walkthechat\Walkthechat\Api\Data\OrderInterface::WALKTHECHAT_ID,
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default'  => null,
                    ],
                    'WalkTheChat Id'
                )
                ->addColumn(
                    \Walkthechat\Walkthechat\Api\Data\OrderInterface::WALKTHECHAT_NAME,
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default'  => null,
                    ],
                    'WalkTheChat Name'
                )
                ->addColumn(
                    \Walkthechat\Walkthechat\Api\Data\OrderInterface::ORDER_ID,
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => true,
                        'unsigned' => true,
                        'default'  => null,
                    ],
                    'Magento Order Id'
                )
                ->addColumn(
                    \Walkthechat\Walkthechat\Api\Data\OrderInterface::CREATED_AT,
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    [
                        'nullable' => false,
                        'default'  => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                    ],
                    'Created At'
                )
                ->addColumn(
                    \Walkthechat\Walkthechat\Api\Data\OrderInterface::UPDATED_AT,
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    [
                        'nullable' => false,
                        'default'  => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE,
                    ],
                    'Updated At'
                )
                ->addColumn(
                    \Walkthechat\Walkthechat\Api\Data\OrderInterface::STATUS,
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                        'length'   => null,
                    ],
                    'Status'
                )
                ->addColumn(
                    \Walkthechat\Walkthechat\Api\Data\OrderInterface::MESSAGE,
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true,
                    ],
                    'Message'
                )
                ->addForeignKey(
                    $installer->getFkName(
                        \Walkthechat\Walkthechat\Model\ResourceModel\Order::TABLE_NAME,
                        \Walkthechat\Walkthechat\Api\Data\OrderInterface::ORDER_ID,
                        $installer->getTable('sales_order'),
                        'entity_id'
                    ),
                    \Walkthechat\Walkthechat\Api\Data\OrderInterface::ORDER_ID,
                    $installer->getTable('sales_order'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('WalkTheChat Order Table');

            $installer->getConnection()->createTable($table);
        }

        return $this;
    }

    /**
     * Creates inventory table
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     *
     * @return $this
     * @throws \Zend_Db_Exception
     */
    protected function createInventoryTable(\Magento\Framework\Setup\SchemaSetupInterface $installer)
    {
        if (!$installer->tableExists($installer->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\Inventory::TABLE_NAME))) {
            $table = $installer
                ->getConnection()
                ->newTable($installer->getTable(\Walkthechat\Walkthechat\Model\ResourceModel\Inventory::TABLE_NAME))
                ->addColumn(
                    \Walkthechat\Walkthechat\Api\Data\InventoryInterface::ID,
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
                    \Walkthechat\Walkthechat\Api\Data\InventoryInterface::PRODUCT_ID,
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => false,
                        'unsigned' => true
                    ],
                    'Product Id'
                )
                ->addColumn(
                    \Walkthechat\Walkthechat\Api\Data\InventoryInterface::WALKTHECHAT_ID,
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default'  => null,
                    ],
                    'WalkTheChat Id'
                )
                ->addColumn(
                    \Walkthechat\Walkthechat\Api\Data\InventoryInterface::QTY,
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => false,
                        'unsigned' => true
                    ],
                    'Qty'
                )
                ->addForeignKey(
                    $installer->getFkName(
                        \Walkthechat\Walkthechat\Model\ResourceModel\Inventory::TABLE_NAME,
                        \Walkthechat\Walkthechat\Api\Data\InventoryInterface::PRODUCT_ID,
                        $installer->getTable('catalog_product_entity'),
                        'entity_id'
                    ),
                    \Walkthechat\Walkthechat\Api\Data\InventoryInterface::PRODUCT_ID,
                    $installer->getTable('catalog_product_entity'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('WalkTheChat Inventory Table');

            $installer->getConnection()->createTable($table);
        }

        return $this;
    }
}

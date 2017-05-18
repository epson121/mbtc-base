<?php

namespace Mbtc\Base\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Fiscal Entity
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('mbtc_transactions')
                )->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true],
                    'Entity id'
                )->addColumn(
                    'order_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Order Id'
                )->addColumn(
                    'address',
                    Table::TYPE_TEXT,
                    '35',
                    ['nullable' => false],
                    'Bitcoin address to verify'
                )->addColumn(
                    'tx_id',
                    Table::TYPE_TEXT,
                    '64',
                    ['nullable' => true, 'default' => null],
                    'Transaction to track'
                )->addColumn(
                    'status',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0],
                    'Transaction status'
                )->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Created at'
                )->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                    'Updated at'
                )->setComment(
                    'Bitcoin transactions table'
                )->addIndex(
                    $installer->getIdxName(
                        'mbtc_transactions',
                        ['order_id', 'address', 'tx_id'],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['order_id', 'address', 'tx_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )->addForeignKey(
                    $installer->getFkName(
                        'mbtc_transactions',
                        'order_id',
                        'sales_order',
                        'entity_id'
                    ),
                    'order_id',
                    $installer->getTable('sales_order'),
                    'entity_id',
                    Table::ACTION_CASCADE
                );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}

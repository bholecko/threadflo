<?php

namespace Peakk\Threadflo\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * Install Threadflo DB tables.
     * 
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $tableName = $installer->getTable('threadflo_item');

        if ($installer->getConnection()->isTableExists($tableName) != true)
        {
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Entity ID'
                )
                ->addColumn(
                    'threadflo_item_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    [
                        'unsigned' => true,
                        'nullable' => false
                    ],
                    'Threadflo Item ID'
                )
                ->addColumn(
                    'item_name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_VARBINARY,
                    80,
                    [
                        'nullable' => true
                    ],
                    'Item Name'
                )
                ->addColumn(
                    'category_name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_VARBINARY,
                    50,
                    [
                        'nullable' => true
                    ],
                    'Category'
                )
                ->addColumn(
                    'parent_sku',
                    \Magento\Framework\DB\Ddl\Table::TYPE_VARBINARY,
                    25,
                    [
                        'nullable' => true
                    ],
                    'Parent SKU'
                )
                ->addColumn(
                    'sku',
                    \Magento\Framework\DB\Ddl\Table::TYPE_VARBINARY,
                    25,
                    [
                        'nullable' => true
                    ],
                    'SKU'
                )
                ->addColumn(
                    'color_name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_VARBINARY,
                    20,
                    [
                        'nullable' => true
                    ],
                    'Color Name'
                )
                ->addColumn(
                    'size_name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_VARBINARY,
                    10,
                    [
                        'nullable' => true
                    ],
                    'Size Name'
                )
                ->addColumn(
                    'price',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '12,4',
                    [
                        'default' => '0.0000',
                        'nullable' => true
                    ],
                    'Price'
                );

            $installer->getConnection()->createTable($table);
        }

        $tableName = $installer->getTable('threadflo_item_image');

        if ($installer->getConnection()->isTableExists($tableName) != true)
        {
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Entity ID'
                )
                ->addColumn(
                    'threadflo_item_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    [
                        'unsigned' => true,
                        'nullable' => false
                    ],
                    'Threadflo Item ID'
                )
                ->addColumn(
                    'name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_VARBINARY,
                    25,
                    [
                        'nullable' => true
                    ],
                    'Name'
                )
                ->addColumn(
                    'url',
                    \Magento\Framework\DB\Ddl\Table::TYPE_VARBINARY,
                    100,
                    [
                        'nullable' => true
                    ],
                    'URL'
                );
            
            $installer->getConnection()->createTable($table);
        }

        $installer->getConnection()->addColumn($installer->getTable('quote_item'), 'threadflo_item_id', 'int(10) UNSIGNED');
        $installer->getConnection()->addColumn($installer->getTable('quote_item'), 'threadflo_item_sku', 'varbinary(25) NULL');
        $installer->getConnection()->addColumn($installer->getTable('sales_order_item'), 'threadflo_item_id', 'int(10) UNSIGNED');
        $installer->getConnection()->addColumn($installer->getTable('sales_order_item'), 'threadflo_item_sku', 'varbinary(25) NULL');
        $installer->getConnection()->addColumn($installer->getTable('sales_order'), 'threadflo_order_id', 'int(10) UNSIGNED');
        $installer->getConnection()->addColumn($installer->getTable('sales_order'), 'threadflo_order_status', 'varbinary(10) NULL');

        $installer->endSetup();
    }

}
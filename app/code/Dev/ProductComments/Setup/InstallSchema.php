<?php

namespace Dev\ProductComments\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */

    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {

        try {
            $table = $setup->getConnection()
                ->newTable($setup->getTable('product_comments'))
                ->addColumn(
                    'comment_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true,
                        'nullable' => false, 'primary' => true],
                    'Comment ID'
                )->addColumn(
                    'product_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Product ID'
                )
                ->addColumn(
                    'email',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => ''],
                    'Email'
                )
                ->addColumn(
                    'comment',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => ''],
                    'Comment'
                )
                ->addColumn(
                    'date',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false,
                        'default' =>
                            Table::TIMESTAMP_INIT],
                    'Date'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => ''],
                    'Status'
                )
                ->addColumn(
                    'name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => ''],
                    'Name'
                )->setComment('Product Comments table');

            $setup->getConnection()->createTable($table);
        } catch (Zend_Db_Exception $e) {
        }
    }
}

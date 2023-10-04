<?php

namespace Ecommage\ViewProduct\Setup;

use Magento\Framework\DB\Ddl\Table as TableDdl;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('am_label'),
                'is_hot_sale',
                TableDdl::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => 0],
                'Is Hot Sale'
            );
        }

        $setup->endSetup();
    }
}

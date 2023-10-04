<?php
/**
 * Copyright © 2016 Codazon. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ecommage\CustomMegaMenu\Setup;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.1', '<'))
        {
            $installer->getConnection()->addColumn(
                $setup->getTable('codazon_megamenu'),
                'store_id',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'unsigned' => true,
                    'comment' => 'Store Id'
                ]
            );
        }
        $installer->endSetup();
	}
}

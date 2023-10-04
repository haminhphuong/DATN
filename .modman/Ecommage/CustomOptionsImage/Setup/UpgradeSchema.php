<?php

namespace Ecommage\CustomOptionsImage\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.0', '<')) {
            $this->addDescriptionAndImage($setup);
        }
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addTitle($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    protected function addDescriptionAndImage(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn($setup->getTable('catalog_product_option'), 'option_description', [
            'type'    => Table::TYPE_TEXT,
            'default' => '',
            'comment' => 'Option Description',
        ]);
        $setup->getConnection()->addColumn($setup->getTable('catalog_product_option_type_value'), 'image', [
            'type'    => Table::TYPE_TEXT,
            'default' => '',
            'comment' => 'Image',
        ]);
        $setup->getConnection()->addColumn($setup->getTable('catalog_product_option_type_value'), 'description', [
            'type'    => Table::TYPE_TEXT,
            'default' => '',
            'comment' => 'Description',
        ]);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    protected function addTitle(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn($setup->getTable('catalog_product_option'), 'option_title', [
            'type'    => Table::TYPE_TEXT,
            'default' => '',
            'comment' => 'Option Title',
        ]);
    }
}

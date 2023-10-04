<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ecommage\CustomStoreLocation\Setup\Patch\Schema;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Schema patch to add unique constraint (entity_pk_value, store_id, entity_type) to review_entity_summary.
 */
class AddCityIdToTable implements SchemaPatchInterface
{
    /**
     * Table name to modify
     */
    private const TABLE = 'amasty_amlocator_location';

    /**
     * @var SchemaSetupInterface
     */
    private $setup;

    /**
     * @param SchemaSetupInterface $setup
     */
    public function __construct(
        SchemaSetupInterface $setup
    ) {
        $this->setup = $setup;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->setup->startSetup();
        $this->addUniqueKey();
        $this->setup->endSetup();
        return $this;
    }

    /**
     * Add unique constraint (entity_pk_value, store_id, entity_type) to review_entity_summary.
     *
     * Remove duplicate entries if any and retries until the unique key is successfully added.
     */
    private function addUniqueKey(): void
    {
        $this->setup->getConnection()->addColumn(
            $this->setup->getTable(self::TABLE),
            'city_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null
        );
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}

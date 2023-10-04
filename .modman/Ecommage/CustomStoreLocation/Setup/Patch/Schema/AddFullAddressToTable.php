<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ecommage\CustomStoreLocation\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class AddFullAddressToTable
 *
 * @package Ecommage\CustomStoreLocation\Setup\Patch\Schema
 */
class AddFullAddressToTable implements SchemaPatchInterface
{
    /**
     * Table name to modify
     */
    const TABLE = 'amasty_amlocator_location';

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
        $this->addAddressFull();
        $this->setup->endSetup();
        return $this;
    }

    private function addAddressFull()
    {
        $this->setup->getConnection()->addColumn(
            $this->setup->getTable(self::TABLE),
            'address_full',
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

<?php /** @noinspection ALL */

namespace Ecommage\CustomCatalogPriceRules\Setup\Patch\Data;

use Ecommage\CustomCatalogPriceRules\Model\Config\SamePrice;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class AddAttributeCatalogRule implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * ModuleDataSetupInterface
     *
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * EavSetupFactory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * AddProductAttribute constructor.
     *
     * @param ModuleDataSetupInterface  $moduleDataSetup
     * @param EavSetupFactory           $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'catalog_rule',
            [
                'type'                    => 'int',
                'frontend'                => '',
                'label'                   => __('Catalog Rule'),
                'input'                   => 'select',
                'class'                   => '',
                'source'                  => 'Ecommage\CustomCatalogPriceRules\Model\Config\Source\Rules',
                'global'                  => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible'                 => true,
                'required'                => false,
                'user_defined'            => false,
                'default'                 => '',
                'searchable'              => false,
                'filterable'              => true,
                'comparable'              => false,
                'visible_on_front'        => true,
                'unique'                  => false,
                'is_used_in_grid'         => true,
                'is_visible_in_grid'      => true,
                'is_filterable_in_grid'   => true,
                'is_filterable_in_search' => true,
                'used_in_product_listing' => true,
                'apply_to'                => '',
                'is_configurable'         => false
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    public function revert()
    {
        // TODO: Implement revert() method.
    }
}

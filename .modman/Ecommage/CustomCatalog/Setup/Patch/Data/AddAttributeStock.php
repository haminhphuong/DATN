<?php
declare(strict_types=1);

namespace Ecommage\CustomCatalog\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\State;

class AddAttributeStock implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var State
     */
    protected $state;

    /**
     * AddAttributeStock constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param CollectionFactory $collectionFactory
     * @param State $state
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        CollectionFactory $collectionFactory,
        State $state
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->productCollectionFactory = $collectionFactory;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'stock_status',
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Stock Status',
                'input' => 'select',
                'class' => '',
                'source' => 'Ecommage\CustomCatalog\Model\Config\Source\StockStatus',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => ''
            ]
        );

        $ATTRIBUTE_GROUP = 'General'; // Attribute Group Name
        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $allAttributeSetIds = $eavSetup->getAllAttributeSetIds($entityTypeId);
        foreach ($allAttributeSetIds as $attributeSetId) {
            $groupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, $ATTRIBUTE_GROUP);
            $eavSetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $groupId,
                'stock_status',
                1000
            );
        }
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function revert()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }
}


<?php

namespace Ecommage\CustomCatalogPriceRules\Helper;

use Exception;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Eav\Model\Cache\Type;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Config\CacheInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class Data extends AbstractHelper
{
    const ATTRIBUTE_CODE = 'catalog_rule';
    /**
     * @var int
     */
    static $count = [];
    /**
     * @var CacheInterface
     */
    private $cache;
    /**
     * @var array
     */
    protected $products = [];
    /**
     * @var null
     */
    protected $attributeId = null;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var array
     */
    private $catalogRuleFilter = [];

    /**
     * Data constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param CacheInterface     $cache
     * @param Context            $context
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        CacheInterface $cache,
        Context $context
    ) {
        $this->cache              = $cache;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }

    /**
     * @return AdapterInterface
     */
    public function getConnection()
    {
        return $this->resourceConnection->getConnection();
    }

    /**
     * @return int
     */
    public function getAttributeId()
    {
        if (!$this->attributeId) {
            $this->attributeId = $this->cache->load(self::ATTRIBUTE_CODE);
        }

        if ($this->attributeId) {
            return (int)$this->attributeId;
        }

        $cacheTags         = [
            Type::CACHE_TAG,
            Attribute::CACHE_TAG,
        ];
        $connection        = $this->getConnection();
        $select            = $connection->select()
                                        ->from(['e' => $connection->getTableName('eav_attribute')], ['e.attribute_id'])
                                        ->where('e.entity_type_id = ?', CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID)
                                        ->where('e.attribute_code = ?', self::ATTRIBUTE_CODE);
        $this->attributeId = $connection->fetchOne($select);
        $this->cache->save($this->attributeId, self::ATTRIBUTE_CODE, $cacheTags);
        return (int)$this->attributeId;
    }

    /**
     * @param $ids
     *
     * @return array
     */
    public function getCatalogRuleIds(array $ids = [])
    {
        $connection = $this->getConnection();
        $select     = $connection->select()
                                 ->from(
                                     ['rp' => $connection->getTableName('catalogrule_product')],
                                     [
                                         'rp.rule_id',
                                         'rp.product_id'
                                     ]
                                 )
                                 ->joinLeft(
                                     ['cr' => $connection->getTableName('catalogrule')],
                                     'cr.rule_id = rp.rule_id',
                                     []
                                 )->where('rp.product_id IN (?)', $ids)
                                 ->where('cr.simple_action = ?', 'to_fixed')
                                 ->where('cr.from_date <= CURRENT_DATE()')
                                 ->where('cr.to_date >= CURRENT_DATE()')
                                 ->where('cr.is_active = ?', 1)
                                 ->group(['rp.rule_id', 'rp.product_id']);
        $items      = $connection->fetchAll($select);
        if ($items) {
            foreach ($items as $item) {
                $ruleId                     = $item['rule_id'];
                $productId                  = $item['product_id'];
                $this->products[$productId] = $ruleId;
            }
        }

        return $this->products;
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function indexRow($id)
    {
        if (isset(static::$count[$id])) {
            return $this;
        }

        try {
            $connection = $this->getConnection();
            $values     = $this->getCatalogRuleIds([$id]);
            $value      = $values[$id] ?? null;
            $tableName  = $connection->getTableName('catalog_product_entity_int');
            $conditions = [
                'attribute_id' => $this->getAttributeId(),
                'entity_id'    => $id,
                'store_id'     => 0,
                'value'        => $value
            ];
            $connection->insertOnDuplicate($tableName, $conditions);
            if (!isset(static::$count[$id])) {
                static::$count[$id] = 0;
            }

            static::$count[$id]++;
        } catch (Exception $exception) {
            $this->_logger->debug($exception->getMessage());
        }

        return $this;
    }

    /**
     * @param $id
     */
    public function indexRows($ids)
    {
        $data        = [];
        $attributeId = $this->getAttributeId();
        $connection  = $this->getConnection();
        $values      = $this->getCatalogRuleIds($ids);
        $tableName   = $connection->getTableName('catalog_product_entity_int');
        $connection->delete(
            $tableName,
            [
                'attribute_id = ?' => $attributeId,
                'entity_id IN (?)' => $ids,
                'store_id = ?'     => 0,
            ]
        );
        foreach ($ids as $id) {
            $value  = $values[$id] ?? null;
            $data[] = [
                'attribute_id = ?' => $attributeId,
                'entity_id = ?'    => $id,
                'store_id = ?'     => 0,
                'value = ?'        => $value
            ];
        }

        if (!empty($data)) {
            $connection->insertMultiple($tableName, $data);
        }
    }

    /**
     * @return array
     */
    public function getAllProductId()
    {
        $connection = $this->getConnection();
        $select     = $connection->select()
                                 ->from(['e' => $connection->getTableName('catalog_product_entity')], ['e.entity_id'])
                                 ->where('e.type_id IN (?)', ['simple', 'virtual']);
        return $connection->fetchCol($select);
    }

    /**
     * @param $filterCode
     * @param $filterItem
     * @return array
     */
    public function sortCatalogRule($filterCode,$filterItem){
        $optionLabel = $filterItem->getOptionLabel();
        if($filterCode == 'catalog_rule'){
            $pieces = explode(' ', trim($optionLabel));
            $last_word = array_pop($pieces);
            $value = (int)filter_var($last_word, FILTER_SANITIZE_NUMBER_INT);
            $this->catalogRuleFilter[$value] = $filterItem;
        }
        ksort($this->catalogRuleFilter);
        return $this->catalogRuleFilter;
    }
}

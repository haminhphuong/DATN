<?php

namespace Ecommage\CustomAmastyXsearch\Plugin\Model\Indexer\Product;

use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Indexer\Model\Indexer;

/**
 * Class IsSale
 *
 * @package Ecommage\CustomAmastyXsearch\Plugin\Model\Indexer\Product
 */
class IsSale
{
    /**
     * @var int
     */
    public static $count = 0;
    /**
     * @var Product
     */
    protected $_productResource;

    /**
     * IsSale constructor.
     *
     * @param Product $productResource
     */
    public function __construct(Product $productResource)
    {
        $this->_productResource = $productResource;
    }

    /**
     * @param Indexer $subject
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.ElseExpression)
     *
     * @throws LocalizedException
     */
    public function beforeGetActionClass(Indexer $subject)
    {
        if (self::$count < 1) {
            $connection   = $this->_productResource->getConnection();
            $tableIndex   = $this->_productResource->getTable('catalog_product_index_price');
            $tableCatalog = $this->_productResource->getTable('catalog_product_entity');
            if ($this->_productResource->getAttribute('sale')) {
                $attributeId = $this->_productResource->getAttribute('sale')->getId();
                $tableUpdate = $this->_productResource->getTable('catalog_product_entity_int');
                $sql = $connection->select()
                                  ->distinct(true)
                                  ->from(['catalog' => $tableCatalog], ['entity_id'])
                                  ->joinLeft(
                                      ['index_price' => $tableIndex],
                                      'catalog.entity_id = index_price.entity_id',
                                      []
                                  )
                                  ->where('index_price.price > index_price.final_price')
                                  ->where('index_price.final_price > 0');
                $sql1 = $connection->select()->distinct(true)->from($tableUpdate, ['value_id'])
                                   ->where('entity_id IN (?)', $sql)
                                   ->where('attribute_id = ?', $attributeId);
                $isProductId   = $connection->fetchCol($sql1);
                $connection->update(
                    $tableUpdate,
                    ['value' => 1],
                    [
                        'value_id IN (?)'  => $isProductId,
                    ]
                );
                $sql2 = $connection->select()
                                   ->distinct(true)
                                   ->from(['catalog' => $tableCatalog], ['entity_id'])
                                   ->joinLeft(
                                       ['index_price' => $tableIndex],
                                       'catalog.entity_id = index_price.entity_id',
                                       []
                                   )
                                   ->where('index_price.price = index_price.final_price')
                                   ->where('index_price.final_price > 0');
                $sql3 = $connection->select()->distinct(true)->from($tableUpdate, ['value_id'])
                                   ->where('entity_id IN (?)', $sql2)
                                   ->where('attribute_id = ?', $attributeId);
                $isNotProductId   = $connection->fetchCol($sql3);
                $connection->update(
                    $tableUpdate,
                    ['value' => 0],
                    [
                        'value_id IN (?)'  => $isNotProductId,
                    ]
                );
            }
            self::$count++;
        }
    }
}

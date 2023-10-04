<?php
/**
 * Collection
 *
 * @copyright Copyright © 2022 Ecommage. All rights reserved.
 * @author    phuonghm@ecommage.com
 */
namespace Ecommage\CustomCatalog\Plugin\Model\ResourceModel\Product\Compare\Item;

use Magento\Eav\Model\Config;
use Magento\Store\Model\StoreManagerInterface;

class Collection
{
    /**
     * @var Config
     */
    protected $_eavConfig;

    /**
     * Comparable attributes cache
     *
     * @var array
     */
    protected $_comparableAttributes;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Customer Filter
     *
     * @var int
     */
    protected $_customerId = 0;

    /**
     * @param Config $eavConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct
    (
        Config $eavConfig,
        StoreManagerInterface $storeManager
    )
    {
        $this->_eavConfig = $eavConfig;
        $this->_storeManager = $storeManager;
    }

    /**
     * Retrieve Merged comparable attributes for compared product items
     *
     * @return array
     */
    public function aroundGetComparableAttributes(\Magento\Catalog\Model\ResourceModel\Product\Compare\Item\Collection $subject)
    {
        if ($this->_comparableAttributes === null) {
            $this->_comparableAttributes = [];
            $setIds = $this->_getAttributeSetIds($subject);
            if ($setIds) {
                $attributeIds = $this->_getAttributeIdsBySetIds($subject, $setIds);

                $select = $subject->getConnection()->select()->from(
                    ['main_table' => $subject->getTable('eav_attribute')]
                )->join(
                    ['additional_table' => $subject->getTable('catalog_eav_attribute')],
                    'additional_table.attribute_id=main_table.attribute_id'
                )->joinLeft(
                    ['al' => $subject->getTable('eav_attribute_label')],
                    'al.attribute_id = main_table.attribute_id AND al.store_id = ' . (int)$subject->getStoreId(),
                    [
                        'store_label' => $subject->getConnection()->getCheckSql(
                            'al.value IS NULL',
                            'main_table.frontend_label',
                            'al.value'
                        )
                    ]
                )->where(
                    'additional_table.is_comparable=?',
                    1
                )->where(
                    'main_table.attribute_id IN(?)',
                    $attributeIds
                )->order('position_compare asc');
                $attributesData = $subject->getConnection()->fetchAll($select);
                if ($attributesData) {
                    $entityType = \Magento\Catalog\Model\Product::ENTITY;
                    $this->_eavConfig->importAttributesData($entityType, $attributesData);
                    foreach ($attributesData as $data) {
                        $attribute = $this->_eavConfig->getAttribute($entityType, $data['attribute_code']);
                        $this->_comparableAttributes[$attribute->getAttributeCode()] = $attribute;
                    }
                    unset($attributesData);
                }
            }
        }
        return $this->_comparableAttributes;
    }

    /**
     * Retrieve comapre products attribute set ids
     *
     * @return array
     */
    protected function _getAttributeSetIds($subject)
    {
        // prepare compare items table conditions
        $compareConds = ['compare.product_id=entity.entity_id'];
        if ($subject->getCustomerId()) {
            $compareConds[] = $subject->getConnection()->quoteInto('compare.customer_id = ?', $subject->getCustomerId());
        } else {
            $compareConds[] = $subject->getConnection()->quoteInto('compare.visitor_id = ?', $subject->getVisitorId());
        }

        // prepare website filter
        $websiteId = (int)$this->_storeManager->getStore($subject->getStoreId())->getWebsiteId();
        $websiteConds = [
            'website.product_id = entity.entity_id',
            $subject->getConnection()->quoteInto('website.website_id = ?', $websiteId),
        ];

        // retrieve attribute sets
        $select = $subject->getConnection()->select()->distinct(
            true
        )->from(
            ['entity' => $subject->getEntity()->getEntityTable()],
            'attribute_set_id'
        )->join(
            ['website' => $subject->getTable('catalog_product_website')],
            join(' AND ', $websiteConds),
            []
        )->join(
            ['compare' => $subject->getTable('catalog_compare_item')],
            join(' AND ', $compareConds),
            []
        );
        return $subject->getConnection()->fetchCol($select);
    }

    /**
     * Retrieve attribute ids by set ids
     *
     * @param array $setIds
     * @return array
     */
    protected function _getAttributeIdsBySetIds($subject, array $setIds)
    {
        $select = $subject->getConnection()->select()->distinct(
            true
        )->from(
            $subject->getTable('eav_entity_attribute'),
            'attribute_id'
        )->where(
            'attribute_set_id IN(?)',
            $setIds
        );
        return $subject->getConnection()->fetchCol($select);
    }

}

<?php

namespace Ecommage\CustomProductAttributeExport\Model\Rewrite\CatalogImportExport\Export;

class Product extends \Magento\CatalogImportExport\Model\Export\Product
{
    /**
     * Set header columns
     *
     * @param array $customOptionsData
     * @param array $stockItemRows
     */
    protected function setHeaderColumns($customOptionsData, $stockItemRows)
    {
        $config = \Magento\Framework\App\ObjectManager::getInstance()
               ->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $moduleEnabled = (bool)$config->getValue('customproductattributeexport/configuration/enable');
        $merge = [];
        if ($moduleEnabled) {
            $attr = $config->getValue('customproductattributeexport/configuration/allowedattribute');
            $merge = explode(',', $attr);
        }

        if (!$this->_headerColumns) {
            $customOptCols = [
                'custom_options',
            ];
            $this->_headerColumns = array_merge(
                [
                    self::COL_SKU,
                    self::COL_STORE,
                    self::COL_ATTR_SET,
                    self::COL_TYPE,
                    self::COL_CATEGORY,
                    self::COL_PRODUCT_WEBSITES,
                ],
                $this->_getExportMainAttrCodes(),
                $merge,
                reset($stockItemRows) ? array_keys(end($stockItemRows)) : [],
                [],
                [
                    'related_skus',
                    'related_position',
                    'crosssell_skus',
                    'crosssell_position',
                    'upsell_skus',
                    'upsell_position'
                ],
                ['additional_images', 'additional_image_labels', 'hide_from_product_page']
            );
            // have we merge custom options columns
            if ($customOptionsData) {
                $this->_headerColumns = array_merge($this->_headerColumns, $customOptCols);
            }
        }
    }
}

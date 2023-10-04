<?php

namespace Ecommage\CustomCatalog\Plugin\Catalog\Model;

use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config
 *
 * @package Ecommage\CustomCatalog\Plugin\Catalog\Model
 */
class Config
{

    /**
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param                               $result
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function afterGetAttributeUsedForSortByArray(
        \Magento\Catalog\Model\Config $catalogConfig,
        $result
    ) {
        $result['position'] = __('Sorted by');
        $result['low_to_high'] = __('Price - Low To High');
        $result['high_to_low'] = __('Price - High To Low');
        $result['name_az'] = __('Name: A->Z');
        $result['name_za'] = __('Name: Z->A');
        if (array_key_exists('name',$result)) {
            unset($result['name']);
        }
        return $result;
    }
}

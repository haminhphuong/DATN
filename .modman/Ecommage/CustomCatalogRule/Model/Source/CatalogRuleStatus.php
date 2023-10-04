<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Ecommage\CustomCatalogRule\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**

 * Class CatalogRuleStatus
 * @package Ecommage\CustomCatalogRule\Model\Source
 */
class CatalogRuleStatus implements ArrayInterface
{
    const STATUS_ACTIVE = 1;

    const STATUS_INACTIVE = 0;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::STATUS_INACTIVE, 'label' => __('Inactive')],
            ['value' => self::STATUS_ACTIVE, 'label' => __('Active')]
        ];
    }
}

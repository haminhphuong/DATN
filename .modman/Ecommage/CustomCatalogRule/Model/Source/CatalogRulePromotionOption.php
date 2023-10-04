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
class CatalogRulePromotionOption implements ArrayInterface
{
    const PROMOTION = 1;

    const  SPECIAL_OFFERS = 0;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::PROMOTION, 'label' => __('Promotion')],
            ['value' => self::SPECIAL_OFFERS, 'label' => __('Special offers')]
        ];
    }
}

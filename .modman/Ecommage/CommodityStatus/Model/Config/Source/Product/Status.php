<?php

namespace Ecommage\CommodityStatus\Model\Config\Source\Product;

/**
 * Class Status
 *
 * @package Ecommage\CommodityStatus\Model\Config\Source\Product
 */
class Status extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const ATTRIBUTE_CODE      = 'commodity_status';
    const STATUS_IN_STOCK     = 1;
    const STATUS_OUT_OF_STOCK = 2;
    const STATUS_COMING_SOON  = 3;

    /**
     * @inheritDoc
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['value' => '', 'label' => __('Please Select')],
                ['value' => self::STATUS_IN_STOCK, 'label' => __('In Stock')],
                ['value' => self::STATUS_OUT_OF_STOCK, 'label' => __('Out Of Stock')],
                ['value' => self::STATUS_COMING_SOON, 'label' => __('Coming Soon')]
            ];
        }
        return $this->_options;
    }
}

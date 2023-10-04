<?php

namespace Ecommage\CustomAmastyOrderattr\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class CheckoutStep extends \Amasty\Orderattr\Model\Config\Source\CheckoutStep implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        foreach ($this->toArray() as $stepId => $label) {
            $optionArray[] = ['value' => $stepId, 'label' => $label];
        }
        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::SHIPPING_STEP => __('Shipping Address'),
            self::SHIPPING_METHODS => __('Shipping Methods'),
            self::NONE => __('None'),
        ];
    }
}

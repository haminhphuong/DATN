<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Ecommage\BookingViewProduct\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class NeedStatus
 * @package Ecommage\PopupNeedAdvice\Model\Source
 */
class BookingStatus implements ArrayInterface
{
    const STATUS_PENDING = 1;

    const STATUS_COMPLETE = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::STATUS_PENDING, 'label' => __('Pending')],
            ['value' => self::STATUS_COMPLETE, 'label' => __('Complete')]
        ];
    }
}

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
class BookingType implements ArrayInterface
{
    const TYPE_OFFLINE = 0;

    const TYPE_ONLINE = 1;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::TYPE_OFFLINE, 'label' => __('Offline')],
            ['value' => self::TYPE_ONLINE, 'label' => __('Online')]
        ];
    }
}

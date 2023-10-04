<?php

namespace Ecommage\BannerManager\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class Type implements ArrayInterface
{
    const TYPE_SLIDER = 1;
    const TYPE_BANNER = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::TYPE_SLIDER,
                'label' => __('Slider')
            ],
            [
                'value' => self::TYPE_BANNER,
                'label' => __('Banner')
            ]
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            self::TYPE_SLIDER => __('Slider'),
            self::TYPE_BANNER => __('Banner')
        ];
    }
}

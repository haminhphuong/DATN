<?php

namespace Ecommage\BannerManager\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class Orientation implements ArrayInterface
{
    const HORIZONTAL = 'horizontal';
    const VERTICAL   = 'vertical';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::HORIZONTAL,
                'label' => __('Horizontal')
            ],
            [
                'value' => self::VERTICAL,
                'label' => __('Vertical')
            ]
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            self::HORIZONTAL => __('Horizontal'),
            self::VERTICAL   => __('Vertical')
        ];
    }
}

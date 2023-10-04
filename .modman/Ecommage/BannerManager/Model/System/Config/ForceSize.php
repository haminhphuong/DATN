<?php

namespace Ecommage\BannerManager\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class ForceSize implements ArrayInterface
{
    const NONE         = 'none';
    const FULL_WIDTH   = 'fullWidth';
    const FULL_WINDOWN = 'fullWindow';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::NONE,
                'label' => __('none')
            ],
            [
                'value' => self::FULL_WIDTH,
                'label' => __('fullWidth')
            ],
            [
                'value' => self::FULL_WINDOWN,
                'label' => __('fullWindow')
            ]
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            self::NONE => __('none'),
            self::FULL_WIDTH => __('fullWidth'),
            self::FULL_WINDOWN => __('fullWindow')
        ];
    }
}

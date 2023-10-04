<?php

namespace Ecommage\BannerManager\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class Status implements ArrayInterface
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::STATUS_DISABLED,
                'label' => __('Disable')
            ],
            [
                'value' => self::STATUS_ENABLED,
                'label' => __('Enable')
            ]
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            self::STATUS_DISABLED => __('Disable'),
            self::STATUS_ENABLED => __('Enable')
        ];
    }
}

<?php

namespace Ecommage\BannerManager\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class Target implements ArrayInterface
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => '0',
                'label' => __('None')
            ],
            [
                'value' => '1',
                'label' => __('New Window')
            ]
        ];
    }

}

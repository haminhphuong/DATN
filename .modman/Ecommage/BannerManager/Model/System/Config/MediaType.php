<?php

namespace Ecommage\BannerManager\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class MediaType implements ArrayInterface
{
    const MEDIA_TYPE_IMAGE = 'image';
    const MEDIA_TYPE_VIDEO = 'video';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::MEDIA_TYPE_IMAGE,
                'label' => __('Image')
            ],
            [
                'value' => self::MEDIA_TYPE_VIDEO,
                'label' => __('Video')
            ]
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            self::MEDIA_TYPE_IMAGE => __('Image'),
            self::MEDIA_TYPE_VIDEO => __('Video')
        ];
    }
}

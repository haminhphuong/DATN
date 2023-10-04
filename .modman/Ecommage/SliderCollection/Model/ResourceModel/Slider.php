<?php

namespace Ecommage\SliderCollection\Model\ResourceModel;

/**
 * Class Slideshow
 *
 * @package Ecommage\BackgroundSlideshow\Model\ResourceModel
 */
class Slider extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('slider_collection', 'slider_id');
    }
}

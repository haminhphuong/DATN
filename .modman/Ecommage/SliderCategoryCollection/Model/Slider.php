<?php

namespace Ecommage\SliderCategoryCollection\Model;

use Magento\Framework\Model\AbstractModel;


class Slider extends AbstractModel
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'slider';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Ecommage\SliderCategoryCollection\Model\ResourceModel\Slider::class);
    }
}

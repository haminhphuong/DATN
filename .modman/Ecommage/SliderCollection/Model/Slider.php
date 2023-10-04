<?php

namespace Ecommage\SliderCollection\Model;

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
        $this->_init(\Ecommage\SliderCollection\Model\ResourceModel\Slider::class);
    }
}

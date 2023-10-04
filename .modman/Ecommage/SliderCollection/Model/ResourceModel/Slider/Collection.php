<?php

namespace Ecommage\SliderCollection\Model\ResourceModel\Slider;

/**
 * Class Collection
 *
 * @package Ecommage\SliderCollection\Model\ResourceModel\Slider
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'slider_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'slider_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'slider_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(\Ecommage\SliderCollection\Model\Slider::class, \Ecommage\SliderCollection\Model\ResourceModel\Slider::class);
    }
}

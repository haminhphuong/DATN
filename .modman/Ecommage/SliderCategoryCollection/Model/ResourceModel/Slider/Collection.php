<?php

namespace Ecommage\SliderCategoryCollection\Model\ResourceModel\Slider;

/**
 * Class Collection
 *
 * @package Ecommage\SliderCategoryCollection\Model\ResourceModel\Slider
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'slider_ctg_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'slider_category_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'slider_category_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(\Ecommage\SliderCategoryCollection\Model\Slider::class, \Ecommage\SliderCategoryCollection\Model\ResourceModel\Slider::class);
    }
}

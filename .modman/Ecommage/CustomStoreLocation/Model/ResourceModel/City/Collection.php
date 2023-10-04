<?php
namespace Ecommage\CustomStoreLocation\Model\ResourceModel\City;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'city_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Ecommage\Address\Model\City::class,
            \Ecommage\Address\Model\ResourceModel\City::class
        );
    }

    /**
     * @param null $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray($valueField = null, $labelField = 'name', $additional = [])
    {
        $additional = [
            'value' => 'city_id',
            'title' => 'name',
            'region_id' => 'region_id',
        ];
        $options = parent::_toOptionArray($valueField, $labelField, $additional);
        if (count($options) > 0) {
            array_unshift(
                $options,
                ['title' => '', 'value' => '', 'label' => __('Please select a district.')]
            );
        }
        return $options;
    }

}
<?php

namespace Ecommage\BookingViewProduct\Model\ResourceModel\BookingView;

use Ecommage\BookingViewProduct\Model\ResourceModel\AbstractCollection;

/**
 * Class Collection
 * @package Ecommage\BookingViewProduct\Model\ResourceModel\BookingView
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';
    /**
     * @var string
     */
    protected $_eventPrefix = 'booking_view_product_collection';
    /**
     * @var string
     */
    protected $_eventObject = 'collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ecommage\BookingViewProduct\Model\BookingView', 'Ecommage\BookingViewProduct\Model\ResourceModel\BookingView');
        parent::_construct();
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @return \Ecommage\BookingViewProduct\Model\ResourceModel\BookingView\Collection
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        $this->performAddStoreFilter($store, $withAdmin);

        return $this;
    }

}

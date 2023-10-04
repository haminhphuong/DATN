<?php

namespace Ecommage\ContactViewProduct\Model\ResourceModel\ContactView;

use Ecommage\ContactViewProduct\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';
    /**
     * @var string
     */
    protected $_eventPrefix = 'contact_view_product_collection';
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
        $this->_init('Ecommage\ContactViewProduct\Model\ContactView', 'Ecommage\ContactViewProduct\Model\ResourceModel\ContactView');
        parent::_construct();
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @return \Ecommage\ContactViewProduct\Model\ResourceModel\ContactView\Collection
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        $this->performAddStoreFilter($store, $withAdmin);

        return $this;
    }

}

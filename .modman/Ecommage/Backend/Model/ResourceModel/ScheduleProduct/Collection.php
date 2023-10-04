<?php
namespace Ecommage\Backend\Model\ResourceModel\ScheduleProduct;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'schedule_id';
    /**
     * @var string
     */
    protected $_eventPrefix = 'ecommage_schedule_update_product_collection';
    /**
     * @var string
     */
    protected $_eventObject = 'schedule_product_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ecommage\Backend\Model\ScheduleProduct', 'Ecommage\Backend\Model\ResourceModel\ScheduleProduct');
    }
}

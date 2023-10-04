<?php
namespace Ecommage\Backend\Model\ResourceModel;

class ScheduleProduct extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     */
    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context)
    {
        parent::__construct($context);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ecommage_schedule_update_product', 'schedule_id');
    }
}

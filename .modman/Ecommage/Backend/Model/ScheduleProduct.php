<?php
namespace Ecommage\Backend\Model;

class ScheduleProduct extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{

    const CACHE_TAG = 'ecommage_schedule_update_product';
    /**
     * @var string
     */
    protected $_cacheTag = 'ecommage_schedule_update_product';
    /**
     * @var string
     */
    protected $_eventPrefix = 'ecommage_schedule_update_product';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ecommage\Backend\Model\ResourceModel\ScheduleProduct');
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}

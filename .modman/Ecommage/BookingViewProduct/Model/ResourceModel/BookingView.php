<?php

namespace Ecommage\BookingViewProduct\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class BookingView
 * @package Ecommage\BookingViewProduct\Model\ResourceModel
 */
class BookingView extends AbstractDb
{
    /**
     * ExperiencePosition constructor.
     *
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('booking_view_product', 'id');
    }
}

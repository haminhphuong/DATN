<?php

namespace Ecommage\ContactViewProduct\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class BookingView
 * @package Ecommage\ContactViewProduct\Model\ResourceModel
 */
class ContactView extends AbstractDb
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
        $this->_init('contact_view_product', 'id');
    }
}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ecommage\ContactViewProduct\Model;

/**
 * Class BookingView
 * @package Ecommage\PopupNeedAdvice\Model
 */
class ContactView extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ecommage\ContactViewProduct\Model\ResourceModel\ContactView');
    }

}

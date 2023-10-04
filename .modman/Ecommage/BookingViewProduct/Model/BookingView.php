<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ecommage\BookingViewProduct\Model;

/**
 * Class BookingView
 * @package Ecommage\PopupNeedAdvice\Model
 */
class BookingView extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ecommage\BookingViewProduct\Model\ResourceModel\BookingView');
    }

}

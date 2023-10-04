<?php

namespace Ecommage\CustomerReview\Model\ResourceModel;

/**
 * Class Slideshow
 *
 * @package Ecommage\BackgroundSlideshow\Model\ResourceModel
 */
class Review extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('customer_review', 'review_id');
    }
}
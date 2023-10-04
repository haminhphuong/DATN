<?php

namespace Ecommage\CustomerReview\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Review
 *
 * @package Ecommage\CustomerReview\Model
 */
class Review extends AbstractModel
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'review';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Ecommage\CustomerReview\Model\ResourceModel\Review::class);
    }
}

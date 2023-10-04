<?php

namespace Ecommage\CustomerReview\Model\ResourceModel\Review;

/**
 * Class Collection
 *
 * @package Ecommage\CustomerReview\Model\ResourceModel\Review
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'review_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'review_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'review_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(\Ecommage\CustomerReview\Model\Review::class, \Ecommage\CustomerReview\Model\ResourceModel\Review::class);
    }
}

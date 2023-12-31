<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ecommage\RelatedCategory\Model\ResourceModel\CategorySlider;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * App page collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';


    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ecommage\RelatedCategory\Model\CategorySlider', 'Ecommage\RelatedCategory\Model\ResourceModel\CategorySlider');
    }
}

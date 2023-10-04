<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ecommage\RelatedCategory\Model;

/**
 * Class CategoryPosts
 *
 * @package Ecommage\RelatedCategory\Model
 */
class CategorySlider extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ecommage\RelatedCategory\Model\ResourceModel\CategorySlider');
    }

}

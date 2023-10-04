<?php
/**
 * Copyright Â© 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ecommage\RelatedCategory\Observer;

use Ecommage\RelatedCategory\Model\ResourceModel\CategorySliderFactory;
use Exception;
use Magento\Catalog\Model\Category;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CategoryPrepareSave implements ObserverInterface
{
    /**
     * @var CategorySliderFactory
     */
    protected $categorySliderFactory;

    /**
     * @param CategorySliderFactory $categorySliderFactory
     */
    public function __construct(CategorySliderFactory $categorySliderFactory)
    {
        $this->categorySliderFactory = $categorySliderFactory;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $request = $observer->getRequest();
        /** @var Category $category */
        $category = $observer->getCategory();
        $postsIds = json_decode($request->getParam('rh_categories_slider'), true);
        try {
            $this->categorySliderFactory->create()->updatePostForCategory($category, $postsIds);
        } catch (Exception $e) {
            // do something here
        }
    }
}

<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ecommage\RelatedCategory\ViewModel\Category;

use Ecommage\SliderCategoryCollection\Model\ResourceModel\Slider\CollectionFactory;;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Category image view model
 */
class RelatedImage implements ArgumentInterface
{

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * Initialize dependencies.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param string $categoryId
     * @return array
     */
    public function getListImage(string $categoryId)
    {
        $images = [];
        if ($categoryId) {
            /** @var \Amasty\Blog\Model\ResourceModel\Posts\Collection $collection */
            $collection = $this->collectionFactory->create();
            $collection->getSelect()->joinLeft(
                ['assign_slider' => $collection->getTable('category_assign_slider')],
                'main_table.slider_ctg_id = assign_slider.slider_ctg_id',
                []
            )->where('assign_slider.category_id = ?', $categoryId);

            if(!empty($collection)){
                foreach ($collection as $slider) {
                    $images[$slider->getSliderCtgId()] = [
                        'name' => $slider->getImage(),
                        'href' => $slider->getTextLink()
                    ];
                }
            }
        }
        return $images;

    }

}

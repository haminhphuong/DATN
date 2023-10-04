<?php

namespace Ecommage\WidgetRecentlyView\Block;

class AfterGetCollection
{

    /**
     * @param \NiceForNow\RecentlyProduct\Block\RecentlyViewProduct $subject
     * @param $collection
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetCollection(\NiceForNow\RecentlyProduct\Block\RecentlyViewProduct $subject, $collection)
    {
        return $collection->addAttributeToSelect('brand');
    }
}

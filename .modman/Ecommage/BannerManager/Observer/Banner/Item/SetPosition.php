<?php

namespace Ecommage\BannerManager\Observer\Banner\Item;

use Ecommage\BannerManager\Model\Banner\Item;

/**
 * Class SetPosition
 */
class SetPosition implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var Item $bannerItem */
        $bannerItem = $observer->getEvent()->getBannerItem();
        if (!$bannerItem->getPosition()) {
            $bannerItemId = $bannerItem->getId();
            $bannerItem->setPosition($bannerItemId);
            $bannerItem->save();
        }
    }
}

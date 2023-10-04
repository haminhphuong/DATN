<?php
/**
 * @author    Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package   Amasty_Storelocator
 */

declare(strict_types=1);

namespace Ecommage\CustomStoreLocation\Observer;

use Amasty\Storelocator\Model\ConfigProvider;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\LayoutInterface;

class LocationSaveBefore implements ObserverInterface
{
    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $location = $observer->getObject();
        if ($location->getCityId()) {
            $location->setCity($location->getCityId());
        }
    }
}

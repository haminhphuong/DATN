<?php

namespace Ecommage\CustomCatalogPriceRules\Observer\Controller\Adminhtml\Promo;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Save implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        /** @var \Magento\CatalogRule\Model\Rule $rule */
        $rule = $observer->getEvent()->getRule();
        if (!empty($rule->getIsActive())){
            $rule->setIsActive(0);
            $rule->setStatus(1);
        }
    }
}
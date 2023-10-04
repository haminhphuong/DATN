<?php
namespace Ecommage\CustomCheckout\Observer\Submit;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Before implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $telephone = $quote->getBillingAddress()->getTelephone();
        if(!$quote->getCustomerEmail()){
            $quote->setCustomerEmail($telephone.'@gmail.com');
        }
        if($quote->getCustomerIsGuest()){
            $quote->setCustomerLastname('');
        }
        return $this;
    }
}

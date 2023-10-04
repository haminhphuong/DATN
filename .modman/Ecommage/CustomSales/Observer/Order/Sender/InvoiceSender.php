<?php

namespace Ecommage\CustomSales\Observer\Order\Sender;

use Magento\Framework\Event\ObserverInterface;

class InvoiceSender implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $transportObject = $observer->getEvent()->getData('transportObject');
        $parentClass = $observer->getEvent()->getData('sender');
        $order = $transportObject->getOrder();
        $paymentHtml = $transportObject->getPaymentHtml();

        /* check remove duplicate text*/
        $payment = $order->getPayment();
        $method = $payment->getMethodInstance();
        $methodTitle = $method->getTitle();

        $pattern = "/".$methodTitle."/i";
        $countFind = preg_match_all($pattern, $paymentHtml);

        if($countFind > 1){
            $paymentHtml = preg_replace('#<caption class="table-caption">(.*?)</caption>#', '', $paymentHtml);
            $transportObject->setData('payment_html',$paymentHtml);
        }

    }
}


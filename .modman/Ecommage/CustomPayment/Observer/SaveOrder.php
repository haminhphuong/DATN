<?php
namespace Ecommage\CustomPayment\Observer;

use Ecommage\Alepay\Model\Payment\Alepay;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class SaveOrder implements ObserverInterface
{
    /**
     * Execute observer.
     *
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order   = $observer->getEvent()->getOrder();
        $payment = $order->getPayment();
        if ($payment && $payment->getMethod() === 'alepay') {
            /** @var Alepay $alepay */
            $alepay  = $payment->getMethodInstance();
            $orderStats = $alepay->getConfigData('order_status');
            $order->setStatus($orderStats);
            $order->setCanSendNewEmailFlag(false);
        }
    }
}

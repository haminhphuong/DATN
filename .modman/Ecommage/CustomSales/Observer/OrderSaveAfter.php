<?php

namespace Ecommage\CustomSales\Observer;

use Magento\Framework\Event\ObserverInterface;
use Ecommage\CustomSales\Model\Order\Email\Sender\CancelOrderSender;

class OrderSaveAfter implements ObserverInterface
{
    /**
     * @var CancelOrderSender
     */
    protected $cancelOrderSender;


    /**
     * @param CancelOrderSender $cancelOrderSender
     */
    public function __construct(
        CancelOrderSender $cancelOrderSender
    )
    {
        $this->cancelOrderSender = $cancelOrderSender;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order->getState() == 'canceled') {
            $this->cancelOrderSender->send($order, true);
        }
    }
}


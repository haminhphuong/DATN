<?php
/***********************************************************************
 * *
 *  *
 *  * @copyright Copyright © Ecommage. All rights reserved.
 *  * See COPYING.txt for license details.
 *  * @author    info@ecommage.com
 * *
 */
namespace Ecommage\CustomCheckout\Controller\Payment;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action as AppAction;
use Magento\Framework\App\Action\Context;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Payment\Model\MethodInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\Order;
use Magento\Framework\App\CacheInterface;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use PHPUnit\Exception;

/**
 * Class ReturnAction
 * @package Ecommage\MomoWallet\Controller\Payment
 */
class ReturnAction extends \Ecommage\MomoWallet\Controller\Payment\ReturnAction
{
    /**
     * @var CommandPoolInterface
     */
    private $commandPool;
    /**
     * @var Session
     */
    private $checkoutSession;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var MethodInterface
     */
    private $method;
    /**
     * @var PaymentDataObjectFactory
     */
    private $paymentDataObjectFactory;
    /**
     * @var CacheInterface
     */
    private $cacheManager;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;
    /**
     * @var \Magento\Sales\Api\InvoiceRepositoryInterface
     */
    protected $_invoiceRepositoryInterface;
    /** @var InvoiceSender */
    private $invoiceSender;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\OrderSender
     */
    protected $orderSender;
    /**
     * ReturnAction constructor.
     *
     * @param Context                  $context
     * @param Session                  $checkoutSession
     * @param MethodInterface          $method
     * @param PaymentDataObjectFactory $paymentDataObjectFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param CommandPoolInterface     $commandPool
     */
    public function __construct
    (
        Context $context, Session $checkoutSession, MethodInterface $method,
        PaymentDataObjectFactory $paymentDataObjectFactory, OrderRepositoryInterface $orderRepository,
        CommandPoolInterface $commandPool, CacheInterface $cacheManager,
        \Magento\Framework\Api\SearchCriteriaBuilder        $searchCriteriaBuilder,
        \Magento\Sales\Api\InvoiceRepositoryInterface       $invoiceRepositoryInterface,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Psr\Log\LoggerInterface                            $logger,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
    )
    {
        parent::__construct($context, $checkoutSession, $method, $paymentDataObjectFactory, $orderRepository, $commandPool, $cacheManager);
        $this->commandPool              = $commandPool;
        $this->checkoutSession          = $checkoutSession;
        $this->orderRepository          = $orderRepository;
        $this->method                   = $method;
        $this->paymentDataObjectFactory = $paymentDataObjectFactory;
        $this->cacheManager = $cacheManager;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_invoiceRepositoryInterface = $invoiceRepositoryInterface;
        $this->invoiceSender = $invoiceSender;
        $this->logger = $logger;
        $this->orderSender = $orderSender;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $orderIdCache = $this->cacheManager->load('order_id');
        $orderId = $this->checkoutSession->getLastOrderId() ?: $orderIdCache;
        $order   = $this->orderRepository->get($orderId);
        try {
            if ($orderId) {
                $response = $this->getRequest()->getParams();
                /** @var \Magento\Sales\Model\Order $order */
                $payment = $order->getPayment();
                ContextHelper::assertOrderPayment($payment);
                if ($payment->getMethod() === $this->method->getCode()) {
                    if ($order->getState() == Order::STATE_PENDING_PAYMENT) {
                        $paymentDataObject = $this->paymentDataObjectFactory->create($payment);
                        $this->commandPool->get('complete')->execute(
                            [
                                'payment' => $paymentDataObject,
                                'response' => $response,
                                'amount' => $order->getTotalDue()
                            ]
                        );
                    }
                    $this->reCheckAndUpdateSession($order);

                    /*  send mail order */
                    try{
                        $this->checkoutSession->setForceOrderMailSentOnSuccess(true);
                        $this->orderSender->send($order, true);
                        $invoice = $this->getInvoiceByOrderId($orderId);
                        if(!empty($invoice)){
                            $this->invoiceSender->send(end($invoice));
                        }
                    }catch (\Exception $e) {
                        $this->logger->error($e->getMessage());
                    }

                    $this->cacheManager->remove('order_id');
                    $this->_redirect('checkout/onepage/success');
                    return;
                }
            }
        } catch (\Exception $e) {
            if($orderId){
                /* cancel order */
                $order->cancel();
                $order->addStatusToHistory(Order::STATE_CANCELED, __('Canceled by customer.'));
                $this->orderRepository->save($order);
            }
            $this->messageManager->addErrorMessage(__('Transaction has been declined. Please try again later.'));
        }

        $this->_redirect('checkout/onepage/failure');
        return;
    }

    /**
     * @param $order
     */
    private function reCheckAndUpdateSession($order)
    {
        if (!$this->checkoutSession->getLastQuoteId()) {
            if ($order && $order->getId()) {
                $this->checkoutSession->setLastQuoteId($order->getQuoteId());
                $this->checkoutSession->setLastSuccessQuoteId($order->getQuoteId());
                $this->checkoutSession->setLastOrderId($order->getId());
                $this->checkoutSession->setLastRealOrderId($order->getIncrementId());
                $this->checkoutSession->setLastOrderStatus($order->getStatus());
            }
        }
    }

    /**
     * @param $orderId
     * @return \Magento\Sales\Api\Data\InvoiceInterface[]|null
     */
    protected function getInvoiceByOrderId($orderId)
    {
        $invoiceData = array();
        $searchCriteria = $this->_searchCriteriaBuilder->addFilter('order_id', $orderId)->create();
        try {
            $invoices = $this->_invoiceRepositoryInterface->getList($searchCriteria);
            $invoiceData = $invoices->getItems();
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage());
            $invoiceData = null;
        }

        return $invoiceData;
    }
}

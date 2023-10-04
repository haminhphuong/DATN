<?php

namespace Ecommage\CustomPayment\Controller\Alepay\Order;

use AlepayUtils;
use Ecommage\Alepay\Helper\Data as DataHelper;
use Ecommage\Alepay\Logger\Logger;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;

class Response extends \Ecommage\Alepay\Controller\Order\Response
{

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\OrderSender
     */
    protected $orderSender;
    /**
     * @var \Magento\Checkout\Model\Session $checkoutSession
     */
    protected $checkoutSession;

    /**
     * @param Order\Email\Sender\OrderSender $orderSender
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Json $json
     * @param Order $order
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param Session $checkoutSession
     * @param Logger $logger
     * @param CurlFactory $curlFactory
     * @param Data $jsonHelper
     * @param OrderManagementInterface $orderManagement
     * @param DataHelper $dataHelper
     * @param EncryptorInterface $encryptor
     */
    public function __construct(\Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,Context $context, PageFactory $resultPageFactory, Json $json, Order $order, ScopeConfigInterface $scopeConfig, StoreManagerInterface $storeManager, Session $checkoutSession, Logger $logger, CurlFactory $curlFactory, Data $jsonHelper, OrderManagementInterface $orderManagement, DataHelper $dataHelper, EncryptorInterface $encryptor)
    {
        $this->orderSender = $orderSender;
        parent::__construct($context, $resultPageFactory, $json, $order, $scopeConfig, $storeManager, $checkoutSession, $logger, $curlFactory, $jsonHelper, $orderManagement, $dataHelper, $encryptor);
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function execute()
    {
        $responseParams = $this->getRequest()->getParams();
        $this->logger->info($this->_url->getCurrentUrl());
        $this->logger->info("#### Response Payment ######");
        $this->logger->info(print_r($responseParams, true));
        /** @codingStandardsIgnoreLine * */
        $this->logger->info("#############################");
        $checksum     = $responseParams['checksum'];
        $responseData = base64_decode($responseParams['data']);
        $rawResponse  = md5($responseData . $this->_dataHelper->getChecksumKey());
        if ($checksum == $rawResponse) {
            $alepayUtils  = new AlepayUtils();
            $responseData = $alepayUtils->decryptData($responseData, $this->_dataHelper->getEncryptKey());
            $responseData = $this->_jsonHelper->jsonDecode($responseData);
            /** @var Order $order */
            $order     = $this->order->getCollection()->addFieldToFilter('alepay_token', $responseData['data'])->getFirstItem();
            $errorCode = $responseData['errorCode'];
            if ($responseData['cancel']) {
                $this->cancelOrder($order, $errorCode);
            } elseif (!$responseData['cancel']) {
                $getTransactionInfoParams = ['transactionCode' => $responseData['data']];
                $urlGetTransactionInfo    = $this->_dataHelper->getApiUrl() . "/checkout/v1/get-transaction-info";
                $transInfo                = $this->callAlepayApi($urlGetTransactionInfo, $getTransactionInfoParams);
                if ($errorCode == "000") {
                    if ($transInfo['status'] == '000' && !$transInfo['installment']) {
                        if ($order->getId()) {

                            // send mail order confirm
                            try {
                                $this->logger->info('Send mail confirm =======> start...');
                                $this->checkoutSession->setForceOrderMailSentOnSuccess(true);
                                $this->orderSender->send($order, true);

                                // create processing status and invoice
                                $this->logger->info('Create invoice in response => started ...');
                                $this->_dataHelper->invoiceOrder($order, $errorCode, $transInfo);
                                $this->logger->info(
                                    'Order status for: ' . $order->getIncrementId() . ' has been changed to processing'
                                );
                            }catch (\Exception $e) {
                                $this->logger->error('Error send mail confirm =======>');
                                $this->logger->error($e->getMessage());
                            }
                        }
                    }
                    $this->messageManager->addSuccess(__('Successfully Paid By Alepay Gateway'));
                    return $this->resultRedirectFactory->create()->setPath('checkout/onepage/success');
                } elseif ($errorCode != "000") {
                    if ($transInfo['installment']) {
                        if ($errorCode == '155') {
                            $this->_orderManagement->hold($order->getEntityId());
                        }
                        $this->messageManager->addWarning($this->_dataHelper->getMessage($errorCode));
                        return $this->resultRedirectFactory->create()->setPath('checkout/onepage/success');
                    }
                    $this->messageManager->addWarning($this->_dataHelper->getMessage($errorCode));
                    return $this->resultRedirectFactory->create()->setPath('checkout/onepage/failure');
                }
            }
        } elseif ($checksum != $rawResponse) {
            $orders = $this->order->loadByIncrementId($responseParams['orderId']);
            if ($orders->getId()) {
                $this->cancelOrder($orders, $responseParams);
            }
            $this->messageManager->addError(__('Incorrect checksum, suspected fraud, cancel order.'));
            return $this->resultRedirectFactory->create()->setPath('checkout/onepage/failure');
        }
    }
}

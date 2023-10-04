<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ecommage\CustomPayment\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\OfflinePayments\Model\Cashondelivery;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\OfflinePayments\Model\Banktransfer;

class CommentsConfigProvider implements ConfigProviderInterface
{
    const ALEPAY_CODE = 'alepay';
    const MOMO_CODE = 'momo';

    /**
     * @var string[]
     */
    protected $methodCodes = [
        Cashondelivery::PAYMENT_METHOD_CASHONDELIVERY_CODE,
        Banktransfer::PAYMENT_METHOD_BANKTRANSFER_CODE,
        self::ALEPAY_CODE,
        self::MOMO_CODE
    ];

    /**
     * @var \Magento\Payment\Model\Method\AbstractMethod[]
     */
    protected $methods = [];

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param PaymentHelper $paymentHelper
     * @param Escaper $escaper
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        Escaper $escaper,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->escaper = $escaper;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        foreach ($this->methodCodes as $code) {
            $this->methods[$code] = $paymentHelper->getMethodInstance($code);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [];
        foreach ($this->methodCodes as $code) {
            if ($this->methods[$code]->isAvailable()) {
                $config['payment']['comments'][$code] = $this->getComments($code);
            }
        }
        return $config;
    }

    /**
     * Get instructions text from config
     *
     * @param string $code
     * @return string
     */
    protected function getComments($code)
    {
        $path = 'payment/' . $code . '/comments';
        $storeId = $this->storeManager->getStore()->getId();
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }
}

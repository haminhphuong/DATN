/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
var config = {
    map: {
        "*": {
            'Ecommage_Alepay/js/view/payment/method-renderer/alepay-method':'Ecommage_CustomPayment/js/view/payment/method-renderer/alepay-method',
            'Magento_OfflinePayments/js/view/payment/method-renderer/cashondelivery-method':'Ecommage_CustomPayment/js/view/payment/method-renderer/cashondelivery-method',
            'Magento_OfflinePayments/js/view/payment/method-renderer/banktransfer-method':'Ecommage_CustomPayment/js/view/payment/method-renderer/banktransfer-method',
            'Ecommage_MomoWallet/js/view/payment/method-renderer/momo-wallet':'Ecommage_CustomPayment/js/view/payment/method-renderer/momo-wallet'
        }
    }
};


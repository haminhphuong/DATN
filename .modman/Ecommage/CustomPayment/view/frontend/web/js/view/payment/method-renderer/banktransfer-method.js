/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* @api */
define([
    'ko',
    'Magento_Checkout/js/view/payment/default'
], function (ko, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Ecommage_CustomPayment/payment/banktransfer'
        },

        /**
         * Get value of instruction field.
         * @returns {String}
         */
        getInstructions: function () {
            return window.checkoutConfig.payment.instructions[this.item.method];
        },
        /**
         * Returns payment method comments.
         *
         * @return {*}
         */
        getComments: function () {
            return window.checkoutConfig.payment.comments[this.item.method];
        }
    });
});

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define(['jquery', 'uiComponent'], function ($, Component) {
    'use strict';

    var quoteMessages = window.checkoutConfig.quoteMessages;
    var quoteItemData = window.checkoutConfig.quoteItemData;

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/summary/item/details/message'
        },
        displayArea: 'item_message',
        quoteMessages: quoteMessages,
        quoteItemData: quoteItemData,

        /**
         * @param {Object} item
         * @return {null}
         */
        getMessage: function (item) {
            if (this.quoteMessages[item['item_id']]) {
                return this.quoteMessages[item['item_id']];
            }

            return null;
        },

        getClassName: function (item) {
            var className = '';
            $.each(this.quoteItemData, function (index, value) {
                if (value['item_id'] == item['item_id']) {
                    if (value['ampromo_rule_id']) {
                        className = 'free';
                        return false;
                    }
                }
            });

            return className;
        }
    });
});

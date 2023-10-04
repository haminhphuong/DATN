/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/form/components/insert-form'
], function ($, Insert) {
    'use strict';

    return Insert.extend({
        defaults: {
            listens: {
                responseData: 'onResponse'
            },
            modules: {
                bannerItemListing: '${ $.bannerItemListingProvider }',
                bannerItemModal: '${ $.bannerItemModalProvider }'
            }
        },

        /**
         * Close modal, reload customer address listing and save customer address
         *
         * @param {Object} responseData
         */
        onResponse: function (responseData) {
            var data;
            if (!responseData.error) {
                this.bannerItemModal().closeModal();
                this.bannerItemListing().reload({
                    refresh: true
                });
                data = this.externalSource().get('data');
                this.saveBannerItem(responseData, data);
            }
        },

        /**
         * Save banner item to banner form data source
         *
         * @param {Object} responseData
         * @param {Object} data - customer address
         */
        saveBannerItem: function (responseData, data) {
            data['entity_id'] = responseData.data['entity_id'];
        },

        /**
         * Event method that closes "Edit customer address" modal and refreshes grid after customer address
         * was removed through "Delete" button on the "Edit customer address" modal
         *
         * @param {String} id - banner item ID to delete
         */
        onBannerItemDelete: function (id) {
            this.bannerItemModal().closeModal();
            this.bannerItemListing().reload({
                refresh: true
            });
            this.bannerItemListing()._delete([parseFloat(id)]);
        }
    });
});

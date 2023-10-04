define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
], function (_, uiRegistry, select) {
    'use strict';
    return select.extend({

        initialize: function () {
            this._super();
            var value = this.initialValue;
            if (value === 'image') {
                uiRegistry.promise('cucustomer_review_review_form.areas.general.general.image').done(function (component) {
                    component.show();
                });
                uiRegistry.promise('customer_review_review_form.areas.general.general.video').done(function (component) {
                    component.hide();
                });
            } else if (value === 'video') {
                uiRegistry.promise('customer_review_review_form.areas.general.general.image').done(function (component) {
                    component.hide();
                });
                uiRegistry.promise('customer_review_review_form.areas.general.general.video').done(function (component) {
                    component.show();
                });
            }
            return this;

        },

        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {

            var image = uiRegistry.get('customer_review_review_form.areas.general.general.image');
            var video = uiRegistry.get('customer_review_review_form.areas.general.general.video');

            if (value === 'image') {
                image.show();
                video.hide();
            } else if (value === 'video') {
                image.hide();
                video.show();
            }
            return this._super();
        },
    });
});

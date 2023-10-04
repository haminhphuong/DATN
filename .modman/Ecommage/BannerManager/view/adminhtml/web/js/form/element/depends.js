define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/modal/modal'
], function (_, uiRegistry, select, modal) {
    'use strict';
    return select.extend({
        /**
         * @returns {*}
         */
        initialize: function () {
            var value = this._super().initialValue;
            this.onUpdateField(value)
            return this;

        },
        /**
         * @param value
         */
        onUpdateField: function (value) {
            var self = this;
            _.each(this.imports, function (obj, key) {
                let field = uiRegistry.get('index = ' + key),
                    isShowEl = (value == obj);
                if (field && typeof field !== "undefined") {
                    self.visibleElement(field, isShowEl);
                }
            });
        },
        /**
         * @param element
         * @param value
         */
        visibleElement: function (element, value) {
            try {
                if (element.hasOwnProperty('visible')) {
                    if (typeof element.visible === "function") {
                        element.visible(value);
                    } else {
                        element.visible = value;
                    }
                } else {
                    if (value === true) {
                        element.show();
                    } else {
                        element.hide();
                    }
                }
            } catch (e) {
                console.log(e);
            }

        },

        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {
            this.onUpdateField(value);
            return this._super();
        },
    });
});

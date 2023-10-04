define([
    'Magento_Ui/js/form/element/abstract',
    'mageUtils',
    'jquery',
    'mage/url'
], function (Element, utils, $) {
    'use strict';
    return Element.extend({
        initialize: function () {
            this._super();
            if (this.initialValue === '') {
                this.base_url = "";
            } else {
                this.base_url = this.imports.base_url + 'media/' + this.initialValue;
            }
        },

        getFileName: function () {
            var preview = $('#preview-' + this.uid);
            preview.attr('src', this.imports.base_url + 'media/' + $('#' + this.uid).val());
            preview.show();
        },
    });
});

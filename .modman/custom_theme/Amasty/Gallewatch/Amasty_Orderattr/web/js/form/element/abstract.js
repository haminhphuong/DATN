define([
    'ko',
    'jquery',
    'underscore',
    'mageUtils',
    'Magento_Ui/js/form/element/abstract',
    'Amasty_Orderattr/js/form/relationAbstract'
], function (ko, $, _, utils, Abstract, relationAbstract) {
    'use strict';

    // relationAbstract - attribute dependencies
    return Abstract.extend(relationAbstract).extend({

        defaults: {
            placeholder: '${ $.label }',
            labelVisible: false
        },

        isFieldInvalid: function () {
            return this.error() && this.error().length ? this : null;
        }
    });
});

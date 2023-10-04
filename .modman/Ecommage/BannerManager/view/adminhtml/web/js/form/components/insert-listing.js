/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/components/insert-listing',
    'underscore'
], function (Insert, _) {
    'use strict';

    return Insert.extend({
        /**
         * Invokes initialize method of parent class,
         * contains initialization logic
         */
        initialize: function () {
            this._super();
            _.bindAll(this, 'updateValue', 'updateExternalValueByEditableData');

            return this;
        },

        /**
         * On action call
         *
         * @param {Object} data - banner item and actions
         */
        onAction: function (data) {
            this[data.action + 'Action'].call(this, data.data);
        },

        /**
         * On mass action call
         *
         * @param {Object} data - banner item
         */
        onMassAction: function (data) {
            this[data.action + 'Massaction'].call(this, data.data);
        },

        /**
         * Delete banner item
         *
         * @param {Object} data - banner item
         */
        deleteAction: function (data) {
            this._delete([parseFloat(data['entity_id'])]);
        },

        /**
         * Mass action delete
         *
         * @param {Object} data - banner item
         */
        deleteMassaction: function (data) {
            var ids = _.map(data, function (val) {
                return parseFloat(val);
            });

            this._delete(ids);
        }
    });
});

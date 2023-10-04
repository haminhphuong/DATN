/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/components/button',
    'underscore'
], function (Button, _) {
    'use strict';

    return Button.extend({
        defaults: {
            entityId: null,
            bannerId: null,
            listens: {
                entity: 'changeVisibility'
            }
        },

        /**
         * Apply action on target component,
         * but previously create this component from template if it is not existed
         *
         * @param {Object} action - action configuration
         */
        applyAction: function (action) {
            if (action.params && action.params[0]) {
                action.params[0]['entity_id'] = this.entityId;
                action.params[0]['banner_id'] = this.bannerId;
            } else {
                action.params = [{
                    'entity_id': this.entityId,
                    'banner_id': this.bannerId
                }];
            }

            this._super();
        },

        /**
         * Change visibility of the default address shipping/billing blocks
         *
         * @param {Object} entity - customer address
         */
        changeVisibility: function (entity) {
            this.visible(!_.isEmpty(entity));
        }
    });
});

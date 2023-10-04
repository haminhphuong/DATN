/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/grid/filters/filters'
], function ($, Filters) {
    'use strict';

    return Filters.extend({
        defaults: {
            chipsConfig: {
                name: '${ $.name }_chips',
                provider: '${ $.chipsConfig.name }',
                component: 'Ecommage_BannerManager/js/grid/filters/chips'
            }
        }
    });
});

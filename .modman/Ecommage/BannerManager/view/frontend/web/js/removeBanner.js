define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('mage.removeBanner', {
        /**
         * @type {Object}
         */
        options: {},

        /**
         * @private
         */
        _create: function () {
            var options = this.options,
                date   = options.endDate,
                itemId   = options.itemId,
                elementId = options.elementId,
            endDate = new Date(date).getTime();
            var x = setInterval(function() {
                var now = new Date();
                now.setHours(now.getHours() - 7);
                var nowTimeStamp = new Date(now).getTime();
                var distance = endDate - nowTimeStamp;
                if(distance <= 0){
                    clearInterval(x);
                    $("#"+elementId+" .banner-item-"+itemId).remove()
                }
            }, 1000);
        }
    });
    return $.mage.removeBanner;
});

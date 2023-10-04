define([
    'jquery'
], function ($) {
    "use strict";
    return function (config, element) {
        $(element).click(function () {
            var form = $(this).parents('form'),
                baseUrl = form.attr('action'),
                addToCartUrl = config.addToCartUrl,
                buyNowCartUrl = config.buyNowCartUrl,
                buyNowUrl = baseUrl.replace(addToCartUrl, buyNowCartUrl);
            form.attr('action', buyNowUrl);
            form.trigger('submit');
            form.attr('action', baseUrl);
            return false;
        });
    }
});

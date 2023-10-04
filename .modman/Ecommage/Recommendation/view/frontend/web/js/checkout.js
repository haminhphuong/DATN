define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('mage.recommendationCheckout', {
        /**
         * @type {Object}
         */
        options: {},

        /**
         * @private
         */
        _create: function () {
            let scenarioIdComplementaryProducts = this.options.scenarioIdComplementaryProducts;
            let num = this.options.num;
            let url = this.options.url;
            let productId = this.options.productId;
            let typeComplementary = this.options.typeComplementary;
            qg(
                'getRecommendationByScenario',
                {
                    scenarioId: scenarioIdComplementaryProducts,
                    productId: productId,
                    num: num
                },
                function(err, RecommendationData) {
                    if(!err){
                        let ids = [];
                        let params = [];
                        $.each(RecommendationData,function(index,value){
                            ids.push(value['productId']);
                            params.push(value['url'].split("?")[1]);
                        })
                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {
                                'productIds':ids,
                                'type':typeComplementary,
                                'params':params
                            },
                            context: $('body')
                        }).success(function (response) {
                            $('.recommendation-complementary-products').html(response).trigger('contentUpdated');
                            $('body').click();
                        }.bind(this));
                    }
                }
            );
        }
    });
    return $.mage.recommendationCheckout;
});

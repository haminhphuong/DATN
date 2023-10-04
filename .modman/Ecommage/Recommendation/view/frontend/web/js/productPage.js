define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('mage.recommendationProductPage', {
        /**
         * @type {Object}
         */
        options: {},

        /**
         * @private
         */
        _create: function () {
            let productId = this.options.productId;
            let scenarioIdPeopleAlsoViewed = this.options.scenarioIdPeopleAlsoViewed;
            let scenarioIdSimilarProduct = this.options.scenarioIdSimilarProduct;
            let num = this.options.num;
            let url = this.options.url;
            let typePeople = this.options.typePeople;
            let typeSimilar = this.options.typeSimilar;
            qg(
                'getRecommendationByScenario',
                {
                    scenarioId: scenarioIdPeopleAlsoViewed,
                    productId: productId,
                    num: num
                },
                function(err, RecommendationData) {
                    if(!err) {
                        let ids = [];
                        let params = [];
                        $.each(RecommendationData, function (index, value) {
                            ids.push(value['productId']);
                            params.push(value['url'].split("?")[1]);
                        })
                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {
                                'productIds': ids,
                                'type': typePeople,
                                'params':params
                            },
                            context: $('body')
                        }).success(function (response) {
                            $('.recommendation-people-also-viewed').html(response).trigger('contentUpdated');
                            $('body').click();
                        }.bind(this));
                    }
                }
            );
            qg(
                'getRecommendationByScenario',
                {
                    scenarioId: scenarioIdSimilarProduct,
                    productId: productId,
                    num: num
                },
                function(err, RecommendationData) {
                    if(!err) {
                        let ids = [];
                        let params = [];
                        $.each(RecommendationData, function (index, value) {
                            ids.push(value['productId']);
                            params.push(value['url'].split("?")[1]);
                        })
                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {
                                'productIds': ids,
                                'type': typeSimilar,
                                'params':params
                            },
                            context: $('body')
                        }).success(function (response) {
                            $('.recommendation-similar-product').html(response).trigger('contentUpdated');
                            $('body').click();
                        }.bind(this));
                    }
                }
            );
        }
    });
    return $.mage.recommendationProductPage;
});

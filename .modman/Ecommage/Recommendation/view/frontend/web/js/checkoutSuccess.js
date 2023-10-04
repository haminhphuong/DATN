define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('mage.recommendationCheckoutSuccess', {
        /**
         * @type {Object}
         */
        options: {},

        /**
         * @private
         */
        _create: function () {
            let scenarioIdRecommendedForYou = this.options.scenarioIdRecommendedForYou;
            let num = this.options.num;
            let url = this.options.url;
            let typeRecommendation = this.options.typeRecommendation;
            qg(
                'getRecommendationByScenario',
                {
                    scenarioId: scenarioIdRecommendedForYou,
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
                                'type': typeRecommendation,
                                'params':params
                            },
                            context: $('body')
                        }).success(function (response) {
                            $('.recommendation-recommended-for-you').html(response).trigger('contentUpdated');
                            $('body').click();
                        }.bind(this));
                    }
                }
            );
        }
    });
    return $.mage.recommendationCheckoutSuccess;
});

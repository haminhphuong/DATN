define([
    'jquery',
    'swiper'
], function ($,Swiper) {
    'use strict';

    $.widget('mage.recommendationSlider', {
        /**
         * @type {Object}
         */
        options: {},

        /**
         * @private
         */
        _create: function () {
            let cacheKey = this.options.cacheKey;
            var swiper = new Swiper('#'+cacheKey, {
                slidesPerView: 2,
                spaceBetween: 30,
                effect: 'fadeOut',
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: true,
                },
                pagination: {
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next-'+cacheKey,
                    prevEl: '.swiper-button-prev-'+cacheKey,
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 10,
                    },
                    767: {
                        slidesPerView: 2,
                        spaceBetween: 10,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 10,
                    },
                    1279: {
                        slidesPerView: 4,
                        spaceBetween: 20,
                    },
                }
            });
        }
    });
    return $.mage.recommendationSlider;
});

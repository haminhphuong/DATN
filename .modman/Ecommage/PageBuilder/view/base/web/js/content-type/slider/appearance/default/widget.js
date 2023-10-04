/**
 * Copyright © Thomas Nguyen, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_PageBuilder/js/events',
    'slick'
], function ($, events) {
    'use strict';

    return function (config, sliderElement) {
        var $element = $(sliderElement);

        /**
         * Prevent each slick slider from being initialized more than once which could throw an error.
         */
        if ($element.hasClass('slick-initialized')) {
            $element.slick('unslick');
        }

        $element.slick({
            autoplay: $element.data('autoplay'),
            autoplaySpeed: $element.data('autoplay-speed') || 0,
            fade: $element.data('fade'),
            infinite: $element.data('infinite-loop'),
            arrows: $element.data('show-arrows'),
            slidesToShow: $element.data('slides-to-show'),
            slidesToScroll: $element.data('slides-to-scroll'),
            dots: $element.data('show-dots'),
            responsive: [
                {
                    breakpoint: 768,
                    settings:{
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });

        // Redraw slide after content type gets redrawn
        events.on('contentType:redrawAfter', function (args) {
            if ($element.closest(args.element).length) {
                $element.slick('setPosition');
            }
        });
        events.on('stage:viewportChangeAfter', $element.slick.bind($element, 'setPosition'));
    };
});

/**
 *   Back to top button functionality
 */

define([
    'jquery',
    'matchMedia'
], function ($, mediaCheck) {
    'use strict';

    $.widget('mage.backTop', {
        options: {
            mediaBreakpoint: '(min-width: 1px)',
            visibleClass: '-show',
            animateClass: '-animate',
            fixClass: '-fix',
            stopScrollPositionSelector: 'footer.page-footer',
            stopButtonDistance: 10
        },
        stopScrollElement: '',
        buttonFixDistance: '',

        /**
         * Widget initialization
         * @private
         * @returns {void}
         */
        _create: function () {
            var self = this,
                backToTop = this.element,
                wasDesktop = false;

            this.onEvents(backToTop);

            mediaCheck({
                media: this.options.mediaBreakpoint,
                entry: function () {
                    self.onScroll(backToTop);
                    wasDesktop = true;
                },
                exit: function () {
                    if (wasDesktop) {
                        self.offScroll(backToTop);
                    }
                }
            });

            self.buttonFixDistance = parseInt(backToTop.css('bottom'));
            self.stopScrollElement = $(self.options.stopScrollPositionSelector);
        },

        /**
         * Scroll to top behavior
         * @param {Object} element
         * @returns {void}
         */
        onEvents: function (element) {
            element.on('click', function () {
                $('html,body').animate({
                    scrollTop: '0'
                });
            });

            element.on('keydown', function (e) {
                switch (e.keyCode) {
                    case 13:
                    case 32:
                        $(e.target).click().blur();
                        break;
                    default:
                        break;
                }
            });
        },

        /**
         * Desktop visibility behavior
         * @param {Object} element
         * @returns {void}
         */
        onScroll: function (element) {
            var self = this,
                options = self.options,
                toogleClass = options.visibleClass;

            element.addClass(options.animateClass);

            $(window).on('scroll.backtotop', function () {
                self.checkButtonPosition();

                if ($(window).scrollTop() > 200) {
                    element.addClass(toogleClass);
                } else {
                    element.removeClass(toogleClass);
                }
            });
        },

        /**
         * Check and set fix position for button
         * @returns {void}
         */
        checkButtonPosition: function () {
            var fixClass = this.options.fixClass,
                stopScrollPosition = this.stopScrollElement.position().top - this.options.stopButtonDistance,
                stopButtonPosition = stopScrollPosition - this.element.outerHeight(),
                stopPosition = stopScrollPosition + this.buttonFixDistance;

            if ($(window).scrollTop() + $(window).height() >= stopPosition) {
                this.element.addClass(fixClass).css('top', stopButtonPosition);
            } else {
                this.element.removeClass(fixClass).css('top', 'inherit');
            }
        },

        /**
         * Reset behavior for mobile
         * @param {Object} element
         * @returns {void}
         */
        offScroll: function (element) {
            var options = this.options,
                animateClass = options.animateClass,
                visibleClass = options.visibleClass;

            element.removeClass(visibleClass + ' ' + animateClass);

            $(window).off('scroll.backtotop');
        }
    });

    return $.mage.backTop;
});

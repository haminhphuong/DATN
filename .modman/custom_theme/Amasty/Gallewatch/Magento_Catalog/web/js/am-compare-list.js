/**
 * Compare Page Widget
 *
 * @param {Number} productColumnWidth - Compare table item width
 * @param {Number} controllerContainerWidth - Scroll controller container width
 * @param {String} windowPrintSelector - Print page selector
 */
define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('am.compareList', {
        options: {
            productColumnWidth: null,
            controllerContainerWidth: 85, // can be hidden
            desktopBreakpoint: 768,
            selectors: {
                compareWrapper: '[data-amtheme-compare="container"]',
                compareTable: '[data-amtheme-compare="compare-table"]',
                compareProducts: '[data-amtheme-compare="product"]',
                comparisonHeadings: '[data-amtheme-compare="comparison-headings"]',
                attrLabel: '[data-amtheme-compare="attrubute-label"]',
                scrollBackContainer: '[data-amtheme-compare="scroll-back-container"]',
                scrollBack: '[data-amtheme-compare="scroll-back"]',
                scrollForwardContainer: '[data-amtheme-compare="scroll-forward-container"]',
                scrollForward: '[data-amtheme-compare="scroll-forward"]',
                stickyProducts: '[data-amtheme-compare="sticky-products"]',
                stickyProduct: '[data-amtheme-compare="sticky-product"]',
                windowPrintSelector: '[data-amtheme-js="print-page"]'
            }
        },

        /**
         * @private
         * @returns {void}
         */
        _create: function () {
            var elem = this.element,
                widgetElements;

            this._initElements();
            this._createFixedHeadings();

            widgetElements = this.options.elements;

            $(this.options.selectors.windowPrintSelector).on('click', function (e) {
                e.preventDefault();
                window.print();
            });

            this.options.productColumnWidth = $(widgetElements.compareProducts, elem).width();
            this._initScrollControllers();
            this._initStickyElements();
        },

        /**
         *
         * @private
         * @returns {void}
         */
        _initElements: function () {
            var widgetSelectors = this.options.selectors;

            this.options.elements = {
                windowGlobal: $(window),
                compareWrapper: $(widgetSelectors.compareWrapper),
                compareTable: $(widgetSelectors.compareTable),
                compareProducts: $(widgetSelectors.compareProducts),
                scrollBackContainer: $(widgetSelectors.scrollBackContainer),
                scrollBack: $(widgetSelectors.scrollBack),
                scrollForwardContainer: $(widgetSelectors.scrollForwardContainer),
                scrollForward: $(widgetSelectors.scrollForward),
                attrLabel: $(widgetSelectors.attrLabel),
                stickyProducts: $(widgetSelectors.stickyProducts),
                stickyProduct: $(widgetSelectors.stickyProduct)
            };
        },

        /**
         * Create fixed headings table
         * @private
         * @returns {void}
         */
        _createFixedHeadings: function () {
            var elem = this.element,
                headings = $('<table/>', {
                    class: 'amtheme-comparison-headings table',
                    'data-amtheme-compare': 'comparison-headings'
                });

            $('th', elem).each(function () {
                var th = $(this),
                    thCopy = th.clone(),
                    height = th.outerHeight();

                thCopy.outerHeight(height)
                    .appendTo(headings)
                    .wrap('<tr />');
            });

            headings.insertBefore(this.options.elements.compareWrapper);
        },

        /**
         * Set height for fixed headings table cells
         * @private
         * @returns {void}
         */
        _setFixedHeadingsCellsHeight: function () {
            var realHeadings = $('th', this.options.elements.compareTable),
                comparisonHeadings = $(this.options.selectors.comparisonHeadings);

            comparisonHeadings.find('th').each(function (index, heading) {
                $(heading).outerHeight($(realHeadings[index]).outerHeight());
            });
        },

        /**
         * Initialize scroll controllers
         * @private
         * @returns {void}
         */
        _initScrollControllers: function () {
            var widgetElements = this.options.elements;

            widgetElements.scrollBackContainer
                .css('left', $(this.options.selectors.attrLabel, this.element).outerWidth());
            this._toggleSideContainers();

            widgetElements.scrollBack.on('click', this._scrollTableView.bind(this, 'back'));
            widgetElements.scrollForward.on('click', this._scrollTableView.bind(this, 'forward'));
        },

        /**
         * Toggle scroll controllers containers
         * @private
         * @returns {void}
         */
        _toggleSideContainers: function () {
            var elem = this.element,
                widgetElements = this.options.elements,
                compareContainer = this.options.elements.compareWrapper,
                tableIsScrolled = compareContainer.scrollLeft() > 0,
                tableIsScrolledToTheEnd = compareContainer.scrollLeft() < elem.width() - compareContainer.width(),
                isMobileView = this.options.elements.windowGlobal.width() >= this.options.desktopBreakpoint;

            widgetElements.scrollBackContainer.toggle(tableIsScrolled && isMobileView);
            widgetElements.scrollForwardContainer.toggle(tableIsScrolledToTheEnd && isMobileView);
        },

        /**
         * Scroll Compare Table View on Controller Click
         * @param {String} direction
         * @private
         * @returns {void}
         */
        _scrollTableView: function (direction) {
            var compareContainer = this.options.elements.compareWrapper,
                stickyProducts = this.options.elements.stickyProducts,
                toggleSideContainers = this._toggleSideContainers.bind(this),
                scrollIncrement = direction === 'forward'
                    ? this.options.productColumnWidth
                    : this.options.productColumnWidth * -1;

            stickyProducts.animate({
                scrollLeft: stickyProducts.scrollLeft() + scrollIncrement
            }, 300, toggleSideContainers);

            compareContainer.animate({
                scrollLeft: compareContainer.scrollLeft() + scrollIncrement
            }, 300, toggleSideContainers);
        },

        /**
         * Initialize sticky elements
         * @private
         * @returns {void}
         */
        _initStickyElements: function () {
            var windowGlobal = this.options.elements.windowGlobal,
                compareContainer = this.options.elements.compareWrapper,
                toggleStickyElements = this._toggleStickyElements.bind(this),
                synchronousScrolling = this._synchronousScrolling.bind(this);

            this._toggleStickyElements();
            windowGlobal.off('resize.amCompareList').on('resize.amCompareList', toggleStickyElements);
            windowGlobal.off('scroll.amCompareList').on('scroll.amCompareList', toggleStickyElements);
            compareContainer.off('scroll.amCompareList').on('scroll.amCompareList', synchronousScrolling);
        },

        /**
         * Toggle all sticky elements
         * @private
         * @returns {void}
         */
        _toggleStickyElements: function () {
            this._setFixedHeadingsCellsHeight();
            this._toggleSideContainers();
            this._toggleStickyScrollControllers();
            this._toggleStickyProducts();
        },

        /**
         * Toggle sticky scroll controllers
         * @private
         * @returns {void}
         */
        _toggleStickyScrollControllers: function () {
            var widgetElements = this.options.elements,
                compareWrapper = widgetElements.compareWrapper,
                compareWrapperOffset = compareWrapper.offset(),
                isCompareScrolledTop = compareWrapperOffset.top < widgetElements.windowGlobal.scrollTop(),
                controllerBack = widgetElements.scrollBack,
                controllerForward = widgetElements.scrollForward;

            controllerBack.toggleClass('-sticky', isCompareScrolledTop);
            controllerForward.toggleClass('-sticky', isCompareScrolledTop);
        },

        /**
         * Toggle sticky product bar
         * @private
         * @returns {void}
         */
        _toggleStickyProducts: function () {
            var widgetElements = this.options.elements,
                windowScrollTop = widgetElements.windowGlobal.scrollTop(),
                stickyProducts = widgetElements.stickyProducts,
                compareWrapper = widgetElements.compareWrapper,
                compareWrapperOffset = compareWrapper.offset(),
                attrLabelWidth = widgetElements.attrLabel.outerWidth(),
                stickyOffsetLeft = compareWrapperOffset.left + attrLabelWidth,
                stickyProductsWidth = compareWrapper.outerWidth() - attrLabelWidth;

            this._setStickyProductsWidth();

            stickyProducts
                .css({
                    left: stickyOffsetLeft,
                    width: stickyProductsWidth
                })
                // .toggleClass(
                //     '-visible',
                //     compareWrapperOffset.top < windowScrollTop
                //     && compareWrapperOffset.top
                //         + compareWrapper.outerHeight()
                //         - stickyProducts.outerHeight() > windowScrollTop
                // );
        },

        /**
         * Set width for items inside sticky products bar
         * @private
         * @returns {void}
         */
        _setStickyProductsWidth: function () {
            var widgetElements = this.options.elements;

            $.each(widgetElements.compareProducts, function (index, product) {
                $(widgetElements.stickyProduct[index]).outerWidth($(product).outerWidth());
            });
        },

        /**
         * Sticky product bar and table synchronous scrolling
         * @private
         * @returns {void}
         */
        _synchronousScrolling: function () {
            this.options.elements.stickyProducts.scrollLeft(this.options.elements.compareWrapper.scrollLeft());
            this._toggleSideContainers();
        }
    });

    return $.am.compareList;
});

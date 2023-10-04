/**
 * Created by : RH
 */
/* global $, $H */
define([
    'jquery',
    'mage/adminhtml/grid'
], function ($) {
    'use strict';
    return function (config) {
        var selectedSlider = config.selectedSlider,
            selectedSlider = selectedSlider,
            gridJsObject = window[config.gridJsObjectName],
            tabIndex = 1000;
        /**
         * Show selected product when edit form in associated product grid
         */
        $("#rh_categories_slider").val(Object.toJSON(selectedSlider));
        /**
         * Register Category Product
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerSelectedSlider(grid, element, checked) {
            if (checked) {
                // var postId = element.defaultValue;
                selectedSlider.push(element.value)
            } else{
                selectedSlider = $.grep(selectedSlider, function(value) {
                    return value !== element.value;
                });
            }

            $("#rh_categories_slider").val(Object.toJSON(selectedSlider));
            grid.reloadParams = {'slider_ctg_ids[]': selectedSlider.keys()};
        }
        /**
         * Click on product row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function categoryPostRowClick(grid, event) {
            var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;
            if (trElement) {
                checkbox = Element.getElementsBySelector(trElement, 'input');
                if (checkbox[0]) {
                    checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                }
            }
        }

        gridJsObject.rowClickCallback = categoryPostRowClick;
        gridJsObject.checkboxCheckCallback = registerSelectedSlider;
    };
});

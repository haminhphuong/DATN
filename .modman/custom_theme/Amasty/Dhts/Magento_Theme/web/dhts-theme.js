/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'jquery/ui',
    'mage/translate',
    'swiper',
    'domReady!',
], function ($, Swiper) {
    'use strict';

    // Animate Form
    var $inputItem = $(".js-inputWrapper");
    $inputItem.length && $inputItem.each(function () {
        var $this = $(this),
            $input = $this.find(".formRow--input"),
            placeholderTxt = $input.attr("placeholder"),
            $placeholder;

        $input.after('<span class="placeholder">' + placeholderTxt + "</span>"),
            $input.attr("placeholder", ""),
            $placeholder = $this.find(".placeholder"),

            $input.val() ? $this.addClass("active") : $this.removeClass("active"),

            $input.on("focusout", function () {
                $input.val() ? $this.addClass("active") : $this.removeClass("active");
            }).on("focus", function () {
                $this.addClass("active");
            });
    });
    // See more content product list
    var moretext = $.mage.__("Show more");
    var lesstext = $.mage.__("Show less");
        $(".category-des .category-txt").each(function() {
            var $minHeight = 120;
            if ( $(this).height() < $minHeight) {
                $(this).next(".category-btn-seemore-wrap").hide();
            }
        });
    $(".category-btn-seemore .pagebuilder-button-link span").click(function(){
        if($(".category-txt").hasClass("show-more-height")){
            $(this).text(moretext)
        }
        else{
            $(this).text(lesstext)
        }
        $(".category-txt").toggleClass("show-more-height");
    });
    // see more content product info
    $(".showmore-info .pagebuilder-button-link span").click(function () {
        if($(".text-info").hasClass("show-more-height")) {
            $(this).text(moretext);
        } else {
            $(this).text(lesstext);
        }
        $(".text-info").toggleClass("show-more-height");
    });

    $(".showmore-info").click(function(){
        $(this).toggleClass("active")
    });
    $(".logo").click(function(){
        alert("Click logo");
    });

    $(".text-info").click(function () {
        $(this).toggleClass("show-more-height");
    });

});

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
        $(this).toggleClass("active");
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
    })

    // sticky menu
    $(window).scroll(function(){
        if($(window).width() > 1023){
            var sticky = $('.page-header'),
                height = sticky.height() - 20;
                scroll = $(window).scrollTop();
                if (scroll >= height){
                    sticky.addClass('fixed');
                    $('body').addClass('fixed-body');
                } else {
                    sticky.removeClass('fixed');
                     $('body').removeClass('fixed-body');
                }
        }

    });
    // dropdown footer
    $(".footer__items > p").click(function(){
        $(this).toggleClass("active")
        $(this).next().slideToggle(200);
    })
    // menu
    $(document).ready(function(){
        $(".section-item-title").first().addClass("active");
        $(".section-item-content").first().addClass("active");
    })
    $(".section-item-title").click(function () {
        $(this).addClass("active").siblings().removeClass("active");
        $(this).next(".section-item-content").addClass("active");
    });
    $('.groupmenu li.item.parent').each(function() {
        $(this).append( "<span class='icon-dr'></span>" );
    });
    $(".groupmenu .icon-dr").click(function(){
        $(this).closest('li.item.parent').toggleClass('open-dropdown').siblings().removeClass('open-dropdown');
    });

//    PDP description
    $(".product.attribute.description .value").each(function() {
        var $minHeight = 290;
        if ( $(this).height() > $minHeight) {
            $(this).parent().append( "<div class='show-button-content'><span class='show-content'>"+moretext+"</span></div>" );
        }
    });
    $(".product.attribute.description .show-button-content .show-content").click(function(){
        if($(".value").hasClass("show-more-height")){
            $(this).text(moretext)
        }
        else{
            $(this).text(lesstext)
        }
        $(this).toggleClass("active")
        $(".value").toggleClass("show-more-height");
    });

    //product detail 1 image
    var displayAmastyGalleryImage = $("#amasty-gallery-images").css("display");
    if(displayAmastyGalleryImage === "none"){
        $("#amasty-gallery-images").parent().css("height","0px");
    }
    //End product detail 1 image

    //Title my account
    let titleMyaccount = $(".account-nav-content .nav.items").find('.current').text();
    let titleAffilateAccount = $('.block-collapsible-nav-content .nav.items').find('.current').text();
    titleMyaccount === '' ? $('.account-nav-title strong').html("Account Menu") : $('.account-nav-title strong').html(titleMyaccount);
    titleAffilateAccount === '' ? $('.block-collapsible-nav-title strong').html("Affiliate Account") : $('.block-collapsible-nav-title strong').html(titleAffilateAccount);
    //End  Title my account

     $(".amlabel-wrapper .amasty-label-image").each(function(){
        var w_img = $(this).attr("data-img-width");
        var h_img = $(this).attr('data-img-height');
        $(this).css({"max-width": w_img/2, "max-height": h_img/2});
    });
     // triggle Forgot your password?
     $(".sparsh-mobile-number-login-option span").click(function () {
         $(this).prev('input').trigger('click');
    });
     $(document).mouseup(function (e) {
        var container = $("input.amsearch-input");
        $(document).mouseup(function (e) {
            var ww = $(window).width();
            var container = $("input.amsearch-input");
            var resultContainer = $('.amsearch-result-section');
            var target = $(e.target);
            if(!container.is(e.target) && container.has(e.target).length === 0 && !target.parents('.amsearch-result-section').length && !resultContainer.is(e.target)) {
                $('section.amsearch-result-section').hide();
                if(ww >= 768){
                    $('.amsearch-form-block').removeClass('-opened');
                }
            }else{
                $('section.amsearch-result-section').show();
            }
        });
    });

      $(".amsearch-form-block.-opened").click(function () {
         $(this).prev('input').trigger('click');
    });
});

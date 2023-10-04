define([
    "jquery",
    "mage/translate"
], function ($, $t) {

    $.widget('mage.amSearchLocator', {
        options: {},
        _create: function () {
            this.ajaxCallUrl = this.options.ajaxCallUrl;
            this.region = $(this.options.region);
            this.country = $(this.options.country);
            this.city = $(this.options.city);
            this.product_id = $(this.options.product_id);
            this.button = $(this.options.button);
            this.text_search = $(this.options.text_search);
            this.per_address = this.options.per_address;
            this.mainContainer = $(this.options.mainContainer);
            var self = this;
            var timeout = null
            $(this.region).change(function () {
                self.ajaxCall();
            });
            $(this.city).change(function () {
                self.ajaxCall();
            });
            // $(this.text_search).keyup(function () {
            //     clearTimeout(timeout)
            //     timeout = setTimeout(function() {
            //         self.dropdownDiv();
            //     }, 500)
            // });
            $(this.button).click(function () {
                self.ajaxCall();
            });
            self.showPerAddress();
        },

        showPerAddress: function () {
            var size_item = $('.amlocator-stores-wrapper .amlocator-store-desc').size(),
                perAddress = this.per_address;
            if (perAddress != undefined) {
                $('.amlocator-stores-wrapper .amlocator-store-desc').hide();
                if (size_item <= perAddress) {
                    $('.amlocator-stores-wrapper .load_more.btn_load_more').hide();
                }
                $('.amlocator-stores-wrapper .amlocator-store-desc:lt(' + perAddress + ')').show();
                $('.amlocator-stores-wrapper .load_more.btn_load_more').click(function () {
                    // $(this).closest('.amlocator-stores-wrapper').find('.amlocator-store-desc:not(:visible):lt(' + perAddress + ')').show();
                    $(this).closest('.amlocator-stores-wrapper').find('.amlocator-store-desc:not(:visible)').show();
                    if ($('.amlocator-stores-wrapper').find('.amlocator-store-desc:visible').length >= size_item) {
                        $(this).hide();
                    }
                })
            }
        },

        // dropdownDiv: function () {
        //     var self = this,
        //         params = this.collectParams();
        //
        //     $.ajax({
        //         url: self.ajaxCallUrl,
        //         type: 'POST',
        //         data: params,
        //         showLoader: false
        //     }).done($.proxy(function (response) {
        //         if (response.errors === false) {
        //             if (params.text_search != '') {
        //                 $('.dropdown-address').show();
        //                 $('.dropdown-address').html(response.html);
        //                 $('.dropdown-address .locator-title').hide();
        //                 $('.dropdown-address .amlocator-store-information .find_store').hide();
        //                 $('.dropdown-address .load_more.btn_load_more').hide();
        //             } else if (params.text_search == '') {
        //                 $('.dropdown-address').empty();
        //                 $('.dropdown-address').hide();
        //             }
        //         }
        //         return true;
        //     }));
        // },

        ajaxCall: function () {
            var self = this,
                params = this.collectParams();

            $.ajax({
                url: self.ajaxCallUrl,
                type: 'POST',
                data: params,
                showLoader: true
            }).done($.proxy(function (response) {
                if (response.errors === false) {
                    if (!$('.dropdown-address').is(':empty')) {
                        self.text_search.val('');
                        $('.dropdown-address').empty();
                    }
                    self.mainContainer.html(response.html);
                    $('.amlocator-all-store .amlocator-stores-wrapper').insertAfter('.amlocator-all-store .amlocator__content .dropdown-address');
                    self.showPerAddress();
                }
                return true;
            }));
        },

        collectParams: function () {
            var city = this.city.val();
            if (this.region.val() === '') {
                city = '';
            }
            return {
                'country': this.country.val(),
                'region': this.region.val(),
                'city': city,
                'text_search': this.text_search.val(),
                'product_id': this.product_id[0]
            };
        }

    });

    return $.mage.amSearchLocator;
});

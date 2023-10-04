/**
 * contact-phone
 *
 * @copyright Copyright © 2021 Ecommage. All rights reserved.
 * @author    phuonghm@ecommage.com
 */

define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'jquery-ui-modules/widget',
    'mage/translate',
], function ($, modal) {
    'use strict';

    $.widget('mage.contact_phone', {
        /**
         * Options common to all instances of this widget.
         * @type {Object}
         */
        options: {},

        /**
         * Bind event handlers for adding contact phone.
         * @private
         */
        _create: function () {
            let options = this.options,
                contactPhone = options.contactPhone;
            if (contactPhone) {
                $(document).on('click', contactPhone, this._contactPhone.bind(this));
            }
        },

        /**
         * @private
         * @return {Boolean}
         */
        _contactPhone: function () {
            let self = this;
            let div = document.createElement('div'),
                controlPhone = document.getElementById("control-phone");
            $(div).addClass("notification-error");
            let phone_regex = /((02|03|05|07|08|09)+([0-9]{8})\b)/g,
                telephone = $('#telephone').val();
            if (telephone.length === 0) {
                if (controlPhone.querySelector(".notification-error")) {
                    controlPhone.querySelector(".notification-error").remove();
                }
                $("#control-phone").prepend(div);
                $('.notification-error').html(self.options.notificationEmpty);
                return false;
            }
            if (phone_regex.test(telephone)) {
                let name = telephone;
                let email = telephone + "@gallewatch.com";
                let settings = _.extend({}, this.ajaxSettings, {
                    url: self.options.url,
                    type: 'POST',
                    data: {
                        name: name,
                        telephone: telephone,
                        product_id: self.options.id_product,
                        location_id: 1,
                        email: email
                    }
                });
                return $.ajax(settings)
                    .success(function () {
                        let options;

                        options = {
                            type: 'popup',
                            responsive: true,
                            title: self.options.title,
                            buttons: []
                        };
                        modal(options, $('#popup-notification'));

                        if (controlPhone.querySelector(".notification-error")) {
                            controlPhone.querySelector(".notification-error").remove();
                        }
                        $('#telephone').val('');
                        $('#notification-success').html(self.options.notificationSuccess);
                        $("#popup-notification").modal("openModal");
                        setTimeout(function () {
                            $("#popup-notification").modal("closeModal");
                        }, 3000);
                    })
                    .error(function () {
                        if (controlPhone.querySelector(".notification-error")) {
                            controlPhone.querySelector(".notification-error").remove();
                        }
                        $('.notification-error').html(self.options.notificationError);
                    });
            } else {
                if (controlPhone.querySelector(".notification-error")) {
                    controlPhone.querySelector(".notification-error").remove();
                }
                $("#control-phone").prepend(div);
                $('.notification-error').html(self.options.notificationInvalid);
            }
        }
    });
    return $.mage.contact_phone;
});

define([
    'jquery',
    'mage/loader',
    'mage/translate',
    'Amasty_SocialLogin/js/am-reload-content',
    'Magento_Customer/js/customer-data',
    'underscore',
    'Magento_Customer/js/model/customer'
], function ($, loader, $translate, amReloadContent, customerData, _, customer) {
    'use strict';

    $.widget('mage.amLoginPopup', {
        customerData: customerData,
        options: {
            selectors: {
                login: 'a[href*="customer/account/login"]',
                logOut: 'a[href*="customer/account/logout"]',
                createAccount: 'a[href*="customer/account/create"]',
                resetPassword: '.amsl-forgot-content form.forget',
                loginOverlay: '[data-am-js="am-login-overlay"]',
                tabWrapper: '[data-am-js="am-tabs-wrapper"]',
                tabForgotWrapper: '[data-am-js="am-tabs-wrapper"]',
                tabWrapperForgot: '[data-am-js="am-tabs-wrapper-forgot"]',
                contentWrapper: '[data-am-js="am-content-wrapper"]',
                popup: '[data-am-js="am-login-popup"]',
                form: '[data-am-js="am-login-popup"] .form',
                forgot_link: '[data-am-js="am-login-popup"] .action.remind',
                default_error: '',
                error_messages: '.amsl-error',
                visibleInput: 'input:not([type=hidden])'
            },
            textValues: {
                emailFieldLabel: $.mage.__('Email'),
                passwordFieldLabel: $.mage.__('Password')
            },
            classes: {
                flexCenter: '-flex-center'
            },
            redirect_duration: 2000,
            popup_duration: 300
        },

        _create: function () {
            this.init();
        },

        init: function () {
            this.initBindings();
            this.initClosePopupBindings();
            this.changeForms();
        },

        initBindings: function () {
            var self = this,
                protocol = document.location.protocol;

            $(self.options.selectors.login).prop('href', '#').on('click', function (event) {
                self.openPopup(0);
                self.initScrollToElement(event.target);
                event.preventDefault();

                return false;
            });

            $(document).on('click', self.options.selectors.login, function (event) {
                self.openPopup(0);
                event.preventDefault();
                self.initScrollToElement(event.target);

                return false;
            });

            /* observe create account links */
            $(self.options.selectors.createAccount).prop('href', '#').on('click', function (event) {
                self.openPopup(1);
                event.preventDefault();
                self.initScrollToElement(event.target);

                return false;
            });

            /* checkout page */
            $('body').addClass('amsl-popup-observed'); // hide magento popup on checkout page
            $(document).on('click', '[data-trigger="authentication"]', function (event) {
                self.openPopup(0);
                event.preventDefault();

                return false;
            });

            $(self.options.selectors.logOut)
                .prop('href', '#')
                .removeAttr('data-post', '')
                .unbind('click')
                .on('click', function (event) {
                    self.sendLogOut();
                    event.preventDefault();

                    return false;
                });

            $(self.options.selectors.form).each(function (index, element) {
                element = $(element);
                var action = element.attr('action'),
                    parser = document.createElement('a');

                parser.href = action;

                if (protocol !== parser.protocol) {
                    element.attr('action', action.replace(parser.protocol, protocol));
                }
            });
            var url = '', urlReset = '', flag = false;
            if(window.isEnable != 0){
                $('input[name="user_option"]').change(function() {
                    var id = $(this).attr('id');
                    if(id == 'mobile_number_user' || id == 'mobile_number_user_login'){
                        url = window.actionNew;
                        urlReset = window.urlResetPassword;
                        $('#login-form .field.password.required').hide();
                        flag = true;
                    }else{
                        $('#login-form .field.password.required').show();
                        flag = false;
                    }
                });
            }

            $(self.options.selectors.form).unbind('submit').on('submit', function (event) {
                var element = $(this);
                if(!flag){
                    url = element.attr('action').replace('customer/account', 'amsociallogin/account');
                }
                if(element.attr('id') == 'otp-login-form'){
                    url = element.attr('action');
                }

                self.options.selectors.default_error = $(element).parents('.amsl-content').find('[data-am-js="am-default-error"]');
                $(self.options.selectors.default_error).hide();

                if (element.valid()) {
                    element.find('button.action').prop('disabled', true);
                    self.sendFormWithAjax(element, url, flag);
                }

                event.preventDefault();

                return false;
            });

            $(self.options.selectors.resetPassword).unbind('submit').on('submit', function (event) {
                var element = $(event.currentTarget);
                if(!flag){
                    urlReset = self.options.reset_pass_url;
                }

                if (element.valid()) {
                    element.find('button.action').prop('disabled', true);
                    self.resetPasswordRequest(element, urlReset);
                }

                event.preventDefault();

                return false;
            });

            $(self.options.selectors.forgot_link).unbind('click').on('click', function (event) {
                self.toggleWrappers();
                event.preventDefault();

                return false;
            });
        },

        initScrollToElement: function (element) {
            var scrollToSelector = $(element).attr('data-amsl-scroll-to'),
                scrollTo = scrollToSelector ? $(scrollToSelector) : null;

            if (scrollTo) {
                $(document.body).one('scroll-after-ajax-update', function () {
                    $([document.documentElement, document.body]).animate({
                        scrollTop: scrollTo.offset().top
                    }, 500);
                });
            } else {
                $(document.body).off('scroll-after-ajax-update');
            }
        },

        initClosePopupBindings: function () {
            var self = this;

            $(self.options.selectors.loginOverlay).on('click', function (e) {
                var target = $(e.target);

                if (target.hasClass('amsl-popup-overlay') || target.hasClass('amsl-close')) {
                    self.closePopup();
                }
            });
        },

        resetPasswordRequest: function (form, urlReset) {
            var self = this;
            $.ajax({
                url: urlReset,
                type: 'post',
                data: $(form).serializeArray(),
                success: function (response) {
                    self.renderMessages(response, form);
                    form.find('button.action').removeProp('disabled');
                    form.find('.captcha-reload').click();
                }
            });
        },

        sendLogOut: function () {
            var self = this;

            this.showAnimation();

            $.ajax({
                url: self.options.logout_url,
                type: 'post',
                dataType: 'json',
                complete: function () {
                    self.hideAnimation();
                },
                success: function (response) {
                    if (response && response.message) {
                        self.showResultPopup(response.message);

                        if (!self._isCustomerAccountPage()) {
                            if (response.redirect === '1') {
                                response.redirect = '2';
                            }

                            self.options.redirect_data.url = window.BASE_URL;
                            amReloadContent.customRedirect(self.options.redirect_data);
                        } else {
                            setTimeout(function () {
                                window.location.href = window.BASE_URL;
                            }, self.options.redirect_duration);
                        }
                    } else {
                        window.location.href = 'customer/account/logout/';
                    }
                },
                error: function () {
                    window.location.href = 'customer/account/logout/';
                }
            });

            return false;
        },

        _isCustomerAccountPage: function () {
            return $('body').hasClass('account');
        },

        showResultPopup: function (message) {
            var parent = $(this.options.selectors.contentWrapper).addClass('amsl-login-success').html('');

            $('<div/>').html(message).appendTo(parent);
            parent.show();
            $(this.options.selectors.tabWrapper).hide();
            $(this.options.selectors.tabWrapperForgot).hide();
            $(this.options.selectors.loginOverlay).fadeIn(this.options.popup_duration);
        },
        showErrorPopup: function (message) {
            var parent = $(this.options.selectors.contentWrapper).addClass('amsl-login-success noti-error').html('');
            $('<div/>').html(message).appendTo(parent);
            parent.show();
            $(this.options.selectors.tabWrapper).hide();
            $(this.options.selectors.tabWrapperForgot).hide();
            $(this.options.selectors.loginOverlay).fadeIn(this.options.popup_duration);
        },

        sendFormWithAjax: function (form, url, flag) {
            var self = this;
            self.form = form;
            this.showAnimation();
            $.ajax({
                url: url,
                data: form.serialize(),
                type: 'post',
                dataType: !flag ? 'html' : 'JSON',
                complete: function () {
                    self.hideAnimation();
                },
                success: function (response) {
                    if(typeof response === 'string' && response.indexOf("isExitPhone") != -1 )
                    {
                        self.showErrorPopup($.mage.__('Mobile number already exists.'));
                        return false;
                    }
                    if(flag){
                        if(response.success){
                            self.showResultPopup(response.otplogin);
                        }else{
                            self.showErrorPopup($.mage.__('Mobile number does not exist.'));
                        }
                    }else{
                        var isSuccess = self.renderMessages(response, self.form);
                        if (isSuccess) {
                            customer.setIsLoggedIn(true);
                            $('body').trigger('ams-logged-in-successfully');
                            amReloadContent.customRedirect(self.options.redirect_data);
                        } else {
                            if(form.hasClass('form-create-account')){
                                self.showErrorPopup($.mage.__('Email already exists.'));
                            }else{
                                self.showErrorPopup($.mage.__('Wrong account or password.'));
                            }
                            self.captchaLoad(response, self.form);
                        }
                    }

                    self.form.find('button.action').removeProp('disabled');

                },
                error: function () {
                    self.showDefaultMessage(self.form);
                }
            });
        },

        captchaLoad: function (response, form) {
            var reloadButton = form.find('.captcha-reload');

            if (reloadButton.length) {
                $(reloadButton).click();
            } else {
                var captcha = $(response).find('.captcha.required');

                if (captcha.length) {
                    form.find('.actions-toolbar').before(captcha);
                    form.trigger('contentUpdated');
                }
            }
        },

        renderMessages: function (response, form) {
            var cookieMessages = _.unique($.cookieStorage.get('mage-messages'), 'text'),
                self = this;

            $.cookieStorage.set('mage-messages', '');

            if (cookieMessages.length) {
                var correct = true;

                $(cookieMessages).each(function (index, message) {
                    if (message.type === 'error') {
                        correct = false;
                    }
                });

                if (!correct) {
                    self.showDefaultMessage(form, cookieMessages);

                    return false;
                }
            }

            if (cookieMessages.length) {
                self.showResultPopup(cookieMessages[0].text);
            } else if (form.hasClass('form-login')) {
                if (response.indexOf('customer/account/logout') !== -1) {
                    self.showResultPopup($.mage.__('You have successfully logged in.'));
                } else {
                    self.showDefaultMessage(form);
                    return false;
                }
            } else if (form.hasClass('form-create-account')) {
                self.showResultPopup($.mage.__('Thank you for registering with us.'));
            } else {
                return false;
            }
            self.showDefaultMessage(form);
            return true;
        },

        openPopup: function (activeTabIndex) {
            this.showMore();
            this.refreshPopup();

            if ($('html').hasClass('nav-open')) {
                $('.navigation > .ui-menu').menu('toggle');
            }

            $(this.options.selectors.loginOverlay).fadeIn(this.options.popup_duration);
            $(this.options.selectors.popup).focus().tabs('activate', activeTabIndex);
        },

        refreshPopup: function () {
            $(this.options.selectors.contentWrapper).hide();
            $(this.options.selectors.tabWrapperForgot).hide();
            $(this.options.selectors.tabWrapper).show();
        },

        closePopup: function () {
            $(this.options.selectors.error_messages).hide();
            $(this.options.selectors.loginOverlay).fadeOut(this.options.popup_duration);
            this.resetFormFields();
        },

        resetFormFields: function () {
            $(this.options.selectors.form + ' ' + this.options.selectors.visibleInput).val('');
        },

        changeForms: function () {
            var parent = $(this.options.selectors.popup);

            /* Login Form */

            /* adding placeholders for fields */
            parent
                .find('.amsl-login-content [name="login[username]"]')
                .prop('placeholder', this.options.textValues.emailFieldLabel);
            parent
                .find('.amsl-login-content [name="login[password]"]')
                .prop('placeholder', this.options.textValues.passwordFieldLabel);

            /* moving 'forgot password' link */
            parent.find('.fieldset.login .actions-toolbar .secondary')
                .insertAfter('[data-am-js="am-login-popup"] .fieldset.login .field.password');

            /* Register Form */

            /* moving 'newsletter' checkbox */
            parent.find('.amsl-register-content .field.choice.newsletter')
                .insertBefore('[data-am-js="am-login-popup"] .amsl-register-content .field.password');
        },

        showDefaultMessage: function (form, messages) {
            var popup = $(this.options.selectors.popup);

            this.options.selectors.default_error = $(form).parents('.amsl-content').find('[data-am-js="am-default-error"]');

            if (!popup.has('.amsl-social-wrapper').length
                && (popup.hasClass('-social-right') || popup.hasClass('-social-left'))
            ) {
                $('.amsl-login-content').addClass('-empty');
            }

            var parent = $(this.options.selectors.default_error).html(''),
                div = null;

            if (messages) {
                $(messages).each(function (index, message) {
                    div = $('<div/>').html(message.text).appendTo(parent);
                });
            } else {
                $('<div/>').html($.mage.__('Sorry, an unspecified error occurred. Please try again.')).appendTo(parent);

                // when we don't know error - it is better to make reload - error can be connected to form_key
                setTimeout(function () {
                    window.location.reload(true);
                }, this.options.redirect_duration);
            }

            parent.slideDown(800);
            form.find('button.action').removeProp('disabled');
        },

        showAnimation: function () {
            $('body').trigger('processStart');
        },

        hideAnimation: function () {
            $('body').trigger('processStop');
        },

        toggleWrappers: function () {
            $(this.options.selectors.tabWrapper).toggle();
            $(this.options.selectors.tabWrapperForgot).toggle();
        },

        showMore: function () {
            var self = this,
                hideAfterCount = 3,
                buttonWrap = '[data-amslogin="button-wrap"]',
                showMoreButtonAttr = 'data-amslogin="showmore"',
                showMoreButtonClass = 'amsl-button-showmore',
                socialButtonSelector = '.amsl-button-wrapper',
                showMoreElement = '<p class="' + showMoreButtonClass + '"' + showMoreButtonAttr
                    + '><span class="amsl-label">' + $.mage.__('Show More')
                    + '<span class="amsl-arrow"></span></span></p>',
                buttons;

            $(buttonWrap).each(function () {
                if (!$(this).find('[' + showMoreButtonAttr + ']').length
                    && $(this).children().length > hideAfterCount
                    && $(this).parents(self.options.selectors.popup).length) {
                    $(this).addClass(self.options.classes.flexCenter)
                        .find(socialButtonSelector + ':nth-child(' + hideAfterCount + ')')
                        .after(showMoreElement);
                    $(this).find('.' + showMoreButtonClass + ' ~ ' + socialButtonSelector).hide();
                }
            });

            $('[' + showMoreButtonAttr + ']').off().on('click', function () {
                buttons = $(this).parent().find(socialButtonSelector);

                $(this).fadeOut(self.options.popup_duration).remove();
                buttons.fadeIn(self.options.popup_duration);
            });
        }
    });

    return $.mage.amLoginPopup;
});

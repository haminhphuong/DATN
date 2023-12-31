define([
    'jquery',
    'countryCode'
], function ($, countryCode) {
    'use strict';

    let mobileNumber = $('.mobile_number'),
        countryCodeInput = $('input[name="country_code"]');

    if($('.sparsh-mobile-number-login-option').length) {
        countryCode.changeLoginUser($('input[name="user_option"]'), 'login[username]');
    }
    countryCode.setCountryDropdown(mobileNumber, countryCodeInput);
    countryCode.validateMobileNumber(mobileNumber, countryCodeInput);
    $('.column.main .form.password.forget .action.back').click(function(e){
        window.location.href = window.loginUrl;
        e.preventdefault();
    })
});

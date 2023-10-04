define([
    'jquery',
    'jquery/validate',
    'intlTelInput',
    'intlTelInputUtils'
], function ($) {
    'use strict';

    function changeLoginUser(userInput, userName){
        $('.sparsh-user-name.sparsh-mobile-number').hide();
        userInput.click(function(){
            let inputId = $(this).attr('id');
            let inputValue = $(this).attr('value');
            if(inputId == "email_user" || inputId == "email_user_login"){
                inputValue = "sparsh-email";
                if(window.isEnable != 0){
                    $('.form.password.forget').attr('action',window.urlForgotPass);
                    $('#login-form').attr('action',window.actionLoginPost);
                    $('#login-form .field.password.required').show();
                }
            }
            if(inputId == "mobile_number_user" || inputId == "mobile_number_user_login"){
                inputValue = "sparsh-mobile-number";
                if(window.isEnable != 0) {
                    $('#login-form .field.password.required').hide();
                    $('#login-form').attr('action',window.actionNew);
                    $('.form.password.forget').attr('action',window.urlResetPassword);
                }
            }
            let targetValue = $("." + inputValue);
            let user =  $('.sparsh-user-name');
            user.not(targetValue).hide();
            user.find('input').removeAttr('name');
            $(targetValue).show();
            $(targetValue).find('input').attr('name', userName);
        });
    }

    function setCountryDropdown(telInput, countryCode) {
        let countryCodeValue = countryCode.val();
        telInput.intlTelInput({
            separateDialCode: true,
            autoPlaceholder: false,
            formatOnDisplay: false,
            preventInvalidNumbers: true,
            preferredCountries: [],
            initialCountry: $.trim(countryCodeValue) ? countryCodeValue : 'auto',
            geoIpLookup: function(success, failure) {
                // $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                //     var countryCode = (resp && resp.country) ? resp.country : '';
                //     success(countryCode);
                // });
                success('VN');
            },
            utilsScript: intlTelInputUtils
        });
    }

    function validateMobileNumber(telInput, countryCode) {
        $.validator.addMethod(
            'validate-mobile-number',
            function (value, element) {
                if ($.trim(telInput.val())) {
                    if (telInput.intlTelInput('isValidNumber')) {
                        countryCode.val(telInput.intlTelInput('getSelectedCountryData').iso2);
                        return true;
                    }
                    return false;
                }
            },
            $.mage.__('Please enter a valid mobile number.')
        );
    }

    return {
        changeLoginUser: changeLoginUser,
        setCountryDropdown: setCountryDropdown,
        validateMobileNumber: validateMobileNumber
    };
});

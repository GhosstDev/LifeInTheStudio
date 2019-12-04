$(document).ready(function(){
    function getUserData() {
        var url = new URLSearchParams(window.location.search);
        var token = url.get("token");
        var key = url.get('key');

        $.ajax({
            url: '/assets/php/auth.php?action=getUserDataFromToken',
            type: 'POST',
            data: {
                token: token,
                key: key
            },
            success: function(response) {
                if (response.startsWith("err")) {
                    alert("error");
                } else {
                    var data = JSON.parse(response);
                    $('#email').val(data.email);
                }
                
            }
        });
    }
    getUserData();
    startBackground();
});

(function ($) {
    "use strict";
    /*==================================================================
    [ Validate ]*/
    var input = $('.validate-input .input100');
    $('.validate-form').on('submit',function(){var check = true;for(var i=0; i<input.length; i++) {if(validate(input[i]) == false){showValidate(input[i]);check=false;}}return check;});
    $('.validate-form .input100').each(function(){$(this).focus(function(){hideValidate(this);});});

    function validate (input) {if($(input).attr('type') == 'email' || $(input).attr('name') == 'email') {if($(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {return false;}}else {if($(input).val().trim() == ''){return false;}}}
    function showValidate(input) {var thisAlert = $(input).parent();$(thisAlert).addClass('alert-validate');}
    function hideValidate(input) {var thisAlert = $(input).parent();$(thisAlert).removeClass('alert-validate');}
})(jQuery);

function checkEmail() {
    $.ajax({
        url: '/assets/php/auth.php?action=checkEmail',
        data: {username: $('#username').val()},
        type: 'POST',
        success: function(response) {
            if (response == "taken") {
                $('#emailfield').attr('title', 'Email is already taken')
                return false;
            } else {
                return true;
            }
        }
    });
}

function attemptSignup() {
    if ($('#password').val() == $('#repassword').val()) {
        var urlParams = new URLSearchParams(window.location.search);
        $.ajax({
            url: '/assets/php/auth.php?action=register',
            type: 'POST',
            data: {
                email: $('#email').val(),
                firstName: $('#firstName').val(),
                lastName: $('#lastName').val(),
                password: $('#password').val(),
                token: urlParams.get("token"),
                key: urlParams.get("key")
            },
            success: function(response) {
                if (response.startsWith("success")) {
                    window.location.href = '/';
                } else if (response.startsWith("err")) {
                    alert("error");
                }
            }
        });
    }
}
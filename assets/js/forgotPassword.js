$(document).ready({
    startBackground()
});

(function ($) {
    "use strict";


    /*==================================================================
    [ Validate ]*/
    var input = $('.validate-input .input100');

    $('.validate-form').on('submit',function(){
        var check = true;

        for(var i=0; i<input.length; i++) {
            if(validate(input[i]) == false){
                showValidate(input[i]);
                check=false;
            }
        }

        return check;
    });


    $('.validate-form .input100').each(function(){
        $(this).focus(function(){
           hideValidate(this);
        });
    });

    function validate (input) {
        if($(input).attr('type') == 'email' || $(input).attr('name') == 'email') {
            if($(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
                return false;
            }
        }
        else {
            if($(input).val().trim() == ''){
                return false;
            }
        }
    }

    function showValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).addClass('alert-validate');
    }

    function hideValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).removeClass('alert-validate');
    }
    
    

})(jQuery);

function sumbitReset() {
    $('#passwordResetForm').slideUp(1000, function() {
        $('#ajax_loader').slideIn(300, function() {
            var url = new URLSearchParams(window.location.search);
            var token = url.get("token");
            var key = url.get('key');
            $.ajax({
                url: "/assets/php/auth.php?action=resetPassword",
                type: "POST",
                data: {
                    token: token,
                    key: key,
                    password: $('#password').val()
                },
                success: function(response) {
                    if (response.startsWith("err_")) {
                        if (response.startsWith("err_invalid_token")) {
                            $('#return_message').addClass('error_text').text("Invalid reset token or token expired");
                        }
                    } else if (response.startsWith("success")) {
                        $('#return_message').addClass('success_text').html("Password was successfully reset! You may now log in with your new credentials.<br><br><a href='/login.html'>Login</a>");
                    } else {
                        $('#return_message').addClass('error_text').text("Unkown error occurred while submitting the password reset, please try again later or contact the administrator");
                    }

                    $('#ajax_loader').fadeOut(500, function() {$('#return_message').fadeIn(500);});
                    
                },
                error: function () {
                    $('#return_message').addClass('error_text').text("Unkown error occurred while submitting the password reset, please try again later or contact the administrator");

                    $('#ajax_loader').slideUp(500, function() {
                        $('#return_div').fadeIn(500);
                    });
                }
            });
        });
        
    });
}
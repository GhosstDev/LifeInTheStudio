$(document).ready(function(){
    $('#forgotPasswordModal').on('shown.bs.modal', function () {
        $('#forgotPasswordEmail').trigger('focus')
    });

    $('#ajax_loader').slideUp(1);
    // startBackground();
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

function login() {
    $.ajax({
        url: "/assets/php/auth.php?action=login",
        type: 'POST',
        data: {
            email: $('#email').val(),
            password: $('#password').val()
        },
        success: function(response) {
            if (response.startsWith("err")) {
                alert("error");
            } else if (response.startsWith("loggedin")) {
                window.location.href = '/';
            }
        }
    });
}

function forgotPasswordShow() {
    $("#forgotPasswordModal").modal();
}

function forgotPasswordRequest() {
    if ($('#forgotPasswordEmail').val()) {
        $('#forgotPasswordSubmitBtn').attr('disabled', 'true');
        $('#forgotPasswordForm').fadeOut(1000, function() {
            $('#ajax_loader').fadeIn(500, function() {
                $.ajax({
                    url: "/assets/php/auth.php?action=forgotPassword",
                    type: 'POST',
                    data: {
                        email: $('#forgotPasswordEmail').val()
                    },
                    success: function (response) {
                        if (response == "success") {
                            $(':root').css("--password-reset-reply-color", 'green');
                            $('#forgotPasswordReplyText').text("Success! The email has been sent. If there was an account associated with this email, then instructions on how to reset your password will be sent to your inbox. If you have not gotten an email from us in a few minutes, then check your spam or junk mail folders");
                        } else if (response.startsWith("err_")) {
                            $(':root').css("--password-reset-reply-color", 'red');
                            $('#forgotPasswordReplyText').text("There was an error sending you password reset instructions. Please wait and try again later or contact the webmaster");
                        }
                    },
                    error: function(p1,p2,p3,p4) {
                        console.log(p1,p2,p3,p4);
                    },
                    complete: function() {
                        $('#ajax_loader').fadeOut(500, function() {
                            $('#forgotPasswordReplyDiv').fadeIn(500);
                        });
                    }
                });
            });
        });
    }
}
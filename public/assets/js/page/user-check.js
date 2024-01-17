///////////////////GLOBALS///////////////////GLOBALS///////////////////GLOBALS///////////////////GLOBALS///////////////////
let response_check = "no response, captcha not fired";
let g_recaptcha_response = "";
let captcha_enable = "off";

// ajax call flag
let flag = "";

/////////////RESET PASSWORD EMAIL/////////////RESET PASSWORD EMAIL/////////////RESET PASSWORD EMAIL/////////////RESET PASSWORD EMAIL
function user_check(id_forward_to, from_forward_to) {

    // ajax call flag
    flag = true;

    //let reset_password_email = $("#reset-password-email").val();

    response_check = "no response, captcha not fired";

    // when google captcha is clicked, a hidden input is created on the page, we need the value of this to pass into check, this is setup var
    g_recaptcha_response = "";

    captcha_enable = "on";

    // js response from captcha to check for checkmark
    response_check = grecaptcha.getResponse();

    // js captcha check
    if (response_check.length == 0) {

        $("#please-complete-captcha").css({ "display": "flex" });
        //console.log("Please complete the captcha - JS");

        flag = false;
    }

    g_recaptcha_response = $('#g-recaptcha-response').val();

    //console.log( reset_password_email + ' - ' + g_recaptcha_response);

    if (flag) {
        $.ajax({
            url: "ajax/async-user-check.php?action=user-check",
            dataType: "json",
            type: "POST",
            data: {
                captcha_enable: captcha_enable,
                g_recaptcha_response: g_recaptcha_response
            },
            error: function(xhr) {

                // remove to see any email sending errors
                //console.log(xhr.responseText);
            },
            success: function(data) {

                //console.log(from_forward_to);

                // captcha failed
                if (data.captcha_state == "captcha_state_fail") {

                    $("#please-complete-captcha").css({ "display": "flex" });
                    //console.log("Please complete the Captcha. - AJAX");

                } if (from_forward_to == 'chapter') {
                    //console.log(data);
                    window.location.href = "/test-question?id=" + id_forward_to + "&from=" + from_forward_to;

                } else if (from_forward_to == 'definitions') {
                    //console.log(data);
                    window.location.href = "/test-question?id=" + id_forward_to + "&from=" + from_forward_to;

                } else {
                    //console.log(data);
                    window.location.href = "/progress";
                }
            }
        });
    }
}
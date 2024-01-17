//// NOTES FOR THE PAGES ARE IN THE ROOT PAGE //// GS IS A TOOL TO HELP KIDS LEARN

/////////////////////RED OUTLINE FUNCTIONS /////////////////////RED OUTLINE FUNCTIONS /////////////////////RED OUTLINE FUNCTIONS

// highlights error box in red for .5 sec
function login_error() {
    $('#login-error').addClass("highlight-border-red-2px");
    // call after .5 sec
    setTimeout(function() {
        $('#login-error').removeClass("highlight-border-red-2px");
    }, 500);
}

// highlights error box in red for .5 sec
function register_error() {
    $('#register-error').addClass("highlight-border-red-2px");
    // call after .5 sec
    setTimeout(function() {
        $('#register-error').removeClass("highlight-border-red-2px");
    }, 500);
}

// highlights error box in red for .5 sec
function reset_pass_error() {
    $('#reset-pass-error').addClass("highlight-border-red-2px");
    // call after .5 sec
    setTimeout(function() {
        $('#reset-pass-error').removeClass("highlight-border-red-2px");
    }, 500);
}

// highlights error box in red for .5 sec
function new_password_error() {
    $('#new-password-error').addClass("highlight-border-red-2px");
    // call after .5 sec
    setTimeout(function() {
        $('#new-password-error').removeClass("highlight-border-red-2px");
    }, 500);
}


///////////////////UNHIDE FLEX BOX FUNCTIONS ///////////////////UNHIDE FLEX BOX FUNCTIONS///////////////////UNHIDE FLEX BOX FUNCTIONS
function unhide_register_cont() {
    $("#login-cont").removeClass("show-flex").addClass("hide");
    $("#register-cont").addClass("show-flex").removeClass("hide");
    $("#register-button-cont").removeClass("show-flex").addClass("hide");

    // reset all captcha
    grecaptcha.reset();
}

function unhide_reset_password() {
    $("#login-cont").removeClass("show-flex").addClass("hide");
    $("#register-button-cont").removeClass("show-flex").addClass("hide");
    $("#reset-pass-cont").addClass("show-flex").removeClass("hide");
    $("#captcha-cont").removeClass("hide").addClass("show-flex");

    // reset all captcha
    grecaptcha.reset();
}


///////////////////GLOBALS///////////////////GLOBALS///////////////////GLOBALS///////////////////GLOBALS///////////////////
// reg checks setup
let reg_for_email = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
let reg_for_one_number = /\d/;
let reg_for_alpha_num_and_spaces = /[^A-Za-z0-9 ]/;

let response_check = "no response, captcha not fired";
let g_recaptcha_response = "";
let captcha_enable = "off";

// ajax call flag
let flag = "";




// global for this function only
let login_attempt = 0;
///////////////////SIGN IN///////////////////SIGN IN///////////////////SIGN IN///////////////////SIGN IN///////////////////SIGN IN
function sign_in() {

    // ajax call flag
    flag = true;

    // login attempt, 6 by default
    login_attempt++;

    let timezone = moment.tz.guess();

    // for ajax pass in
    captcha_enable = "off";

    let username_login = $("#username-login").val();
    let password_login = $("#password-login").val();

    response_check = "no response, captcha not fired";

    // when google captcha is clicked, a hidden input is created on the page, we need the value of this to pass into check, this is setup var
    g_recaptcha_response = "";

    // if login attempt is over 6, show this as 1st check, captcha
    if (login_attempt >= 6) {

        $("#captcha-cont").removeClass("hide").addClass("show-flex");
        $("#register-button-cont").removeClass("show-flex").addClass("hide");

        captcha_enable = "on";

        // js response from captcha to check for checkmark
        response_check = grecaptcha.getResponse();

        // js captcha check
        if (response_check.length == 0) {
            $("#login-error").css({ "display": "flex" });
            $("#login-error-span").text("Please complete the captcha");
            //console.log("Please complete the captcha - JS Captcha Blank");

            login_error();

            flag = false;
        }
        //passed js check
        else if (response_check.length != 0) {

            g_recaptcha_response = $('#g-recaptcha-response').val();
        }
    }


    // new if else statements, if username blank
    if (username_login == "") {

        $("#login-error").css({ "display": "flex" });
        $("#login-error-span").text("Please enter your username email address.");
        //console.log("Please enter your username email address. - JS Blank");

        login_error();

        flag = false;
    }

    // if username is not formatted as an email
    else if (reg_for_email.test(username_login) == false) {

        $("#login-error").css("display", "flex");
        $("#login-error-span").text("Please enter your username email address.");
        //console.log("Please enter your username email address. - Not Formatted JS");

        login_error();

        flag = false;
    } else if (password_login == "") {

        $("#login-error").css({ "display": "flex" });
        $("#login-error-span").text("Please enter your password.");
        //console.log("Please enter your password. - Blank Password JS");

        login_error();

        flag = false;

    }
    // if password is less than 8, does not include 1 number, or is greater than 64
    else if ((password_login.length <= 8) || !(reg_for_one_number.test(password_login)) || (password_login.length > 64)) {

        $("#login-error").css({ "display": "flex", "cursor": "pointer" });
        $("#login-error-span").html('<div>Incorrect Password, <span style="color: #fff470">Click Here to Reset.</span></div><div style="font-size: 0.9em; margin-top: 5px;"> Hint: All passwords are at least 8 characters and include 1 number.</div>');

        $("#login-error").on("click", unhide_reset_password);

        //console.log("Incorrect Password, Click Here to Reset. - Length or Format JS");

        login_error();
        flag = false;
    }

    //console.log( captcha_enable  + ' - ' + username_login + ' - ' + password_login + ' - ' + login_attempt + ' - ' + response_check);


    if (flag) {
        $.ajax({
            url: "ajax/async-signin.php?action=sign-in",
            dataType: "json",
            type: "POST",
            data: { username_login: username_login, password_login: password_login, captcha_enable: captcha_enable, timezone: timezone, g_recaptcha_response: g_recaptcha_response },
            error: function(xhr) {
                // console.log(xhr.responseText);
            },
            success: function(data) {

                // all errors in this //console.log
                //console.log(data);

                // captcha is off, user not verified or pass not verified
                if ((data.captcha_on_off == "captcha-off") && (data.checks_state == "checks_failed")) {

                    $("#login-error").css({ "display": "flex", "cursor": "pointer" });
                    $("#login-error-span").html('<div>Incorrect User Name or Password, <span style="color: #fff470">Click Here to Reset.</span></div><div style="font-size: 0.9em; margin-top: 5px;"> Hint: All passwords are at least 8 characters and include 1 number.</div>');

                    $("#login-error").on("click", unhide_reset_password);

                    login_error();

                    //console.log("JS Captcha Off, Username or Password bad - AJAX");

                }

                // captcha is off, user verified, pass verified, and already signed in with session
                else if ((data.captcha_on_off == "captcha-off") && (data.checks_state == "checks_passed")) {

                    if (reports_flag) {
                        if (reports_user == "")
                            window.location.href = "reports?date=" + reports_time;
                        else
                            window.location.href = "reports?date=" + reports_time + "&user=" + reports_user;
                    } else {


                        window.location.href = "progress";

                    }
                    //console.log("JS Captcha Off, Username or Password good, logged in - AJAX");
                }

                // captcha on, not verifed in backend
                else if ((data.captcha_on_off == "captcha-on") && (data.captcha_state == "captcha_state_fail")) {

                    //same error as the rest
                    $("#login-error-cont").html(
                        '<div id="login-error" style="display: flex; cursor: pointer; justify-content: center; font-family: \'Raleway\', sans-serif; padding: 10px 20px; font-size: 1.1rem; line-height: 1.5; background-color: #00acc1; color: #fff; border-radius: 10px;  margin-top: 5px;">' +
                        '<span id="login-error-span">Please Complete Captcha</span></div>'
                    );
                    login_error();

                    //console.log("JS Captcha Good, Backend Captcha bad - AJAX");

                }

                // captcha on, not verifed in backend
                else if ((data.captcha_on_off == "captcha-on") && (data.captcha_state == "captcha_state_success") && (data.checks_state == "checks_passed")) {

                    if (reports_flag) {
                        if (reports_user == "")
                            window.location.href = "reports?date=" + reports_time;
                        else
                            window.location.href = "reports?date=" + reports_time + "&user=" + reports_user;
                    } else {


                        window.location.href = "progress";

                    }

                    //console.log("JS Captcha Good, Backend Captcha good, User and Pass correct, logged in - AJAX");

                }

                // captcha on, captcha success, username or password not success (reset captcha, give them another 6 tries before doing captcha again)
                else if ((data.captcha_on_off == "captcha-on") && (data.captcha_state == "captcha_state_success") && (data.checks_state == "checks_failed")) {

                    // reset login attempt counter, to allow for another 6 tries before another captcha
                    login_attempt = 0;
                    $("#captcha-cont").removeClass("show-flex").addClass("hide");

                    $("#register-button-cont").removeClass("hide").addClass("show-flex");

                    // var passed into ajax reset
                    captcha_enable = "off";

                    // google captcha reset
                    grecaptcha.reset();

                    //same error as the rest
                    $("#login-error-cont").html(
                        '<div id="login-error" style="display: flex; cursor: pointer; justify-content: center; font-family: \'Raleway\', sans-serif; padding: 10px 20px; font-size: 1.1rem; line-height: 1.5; background-color: #00acc1; color: #fff; border-radius: 10px;  margin-top: 5px;">' +
                        '<span id="login-error-span">Incorrect Password, Reset?</span></div>'
                    );
                    login_error();

                    //console.log("JS Captcha Good, Backend Captcha good, User or Pass not correct - AJAX");

                } else if ((data.request_from_gamesmartz == "failed")) {
                    // console.log("request not from api")
                }

                ///////////////////END CHECKS

            }
        });
    } ///////////////////END AJAX///

    //// SEE NOTES ON LOGIN FOR WHY THIS IS NEEDED
    return false;
}


// global for this function only
let register_login_attempt = 0;
///////////////////REGISTER///////////////////REGISTER///////////////////REGISTER///////////////////REGISTER///////////////////REGISTER
function register() {

    // ajax call flag
    flag = true;

    register_login_attempt++;

    // already defined
    response_check = "no response, captcha not fired";

    // let betakey_input = $("#betakey").val();

    let username_register = $("#username-register").val();
    let password_register = $("#password-register").val();

    let nickname_register = $("#nickname-register").val();

    if (register_login_attempt >= 6) {

        $("#captcha-cont").removeClass("hide").addClass("show-flex");

        captcha_enable = "on";

        // js response from captcha to check for checkmark
        response_check = grecaptcha.getResponse();

        // js captcha check
        if (response_check.length == 0) {
            $("#register-error").css({ "display": "flex" });
            $("#register-error-span").text("Please complete the captcha");
            //console.log("Please complete the captcha. - JS 0");

            register_error();

            flag = false;
        }
        //checked
        else if (response_check.length != 0) {

            $("#captcha-cont").removeClass("show-flex").addClass("hide");

            register_login_attempt = 0;
            grecaptcha.reset();

            g_recaptcha_response = $('#g-recaptcha-response').val();
        }
    }


    // if ( (betakey != betakey_input) ) {
    //
    //     $("#register-error").css({ "display": "flex" } );
    //     $("#register-error-span").text("Please enter the correct beta key");
    //     //console.log("Please enter the correct beta key. - JS NO MATCH");
    //
    //     register_error();
    //
    //     flag = false;
    // }

    // username email is not blank or more than 64
    else if ((username_register == "") || (username_register > 64)) {

        $("#register-error").css({ "display": "flex" });
        $("#register-error-span").text("Please enter a valid email.");
        //console.log("Please enter a valid email. - JS BLANK");

        register_error();

        flag = false;
    }
    // username email formatting
    else if (reg_for_email.test(username_register) == false) {

        $("#register-error").css({ "display": "flex" });
        $("#register-error-span").text("Please enter a valid email.");
        //console.log("Please enter a valid email. - JS FORMAT");

        register_error();

        flag = false;
    }

    // if pass is less than 8 or greater than 64
    else if ((password_register.length <= 8) || (password_register.length > 64)) {
        $("#register-error").css({ "display": "flex" });
        $("#register-error-span").text("Please make password at least 8 characters and include 1 number.");
        //console.log("Please make password at least 8 characters and include 1 number - JS LENGTH");

        register_error();

        flag = false;
    }
    // password needs to include 1 number
    else if (reg_for_one_number.test(password_register) == false) {
        $("#register-error").css({ "display": "flex" });
        $("#register-error-span").text("Please make password at least 8 characters and include 1 number.");
        //console.log("Please make password at least 8 characters and include 1 number - JS NUMBER");

        register_error();

        flag = false;
    }

    // nickname
    else if (reg_for_alpha_num_and_spaces.test(nickname_register) == true) {
        $("#register-error").css({ "display": "flex" });
        $("#register-error-span").text("Nicknames can be letters and numbers only.");
        //console.log("Nicknames can be letters and numbers only - JS NICKNAME");

        register_error();

        flag = false;
    }


    //console.log( username_register + ' - ' + password_register + ' - ' + nickname_register );

    if (flag) {
        $.ajax({
            url: "ajax/async-signin.php?action=register",
            dataType: "json",
            type: "POST",
            data: {
                username_register: username_register,
                password_register: password_register,
                nickname_register: nickname_register,
                captcha_enable: captcha_enable,
                g_recaptcha_response: g_recaptcha_response
            },
            error: function(xhr) {
                //console.log(xhr.responseText);
            },
            success: function(data) {

                //console.log(data);

                if (data.email == "email_not_valid") {

                    $("#register-error").css({ "display": "flex" });
                    $("#register-error-span").text("Please enter a valid email.");
                    //console.log("Please enter a valid email. - AJAX FORMAT");

                    register_error();
                } else if (data.password == "password_not_valid") {

                    $("#register-error").css({ "display": "flex" });
                    $("#register-error-span").text("Please make password at least 8 characters and include 1 number.");
                    //console.log("Please make password at least 8 characters and include 1 number - AJAX LENGTH");

                    register_error();
                } else if (data.password_number == "password_without_number") {

                    $("#register-error").css({ "display": "flex" });
                    $("#register-error-span").text("Please make password at least 8 characters and include 1 number.");
                    //console.log("Please make password at least 8 characters and include 1 number - AJAX NO NUMBER");

                    register_error();
                } else if (data.user == "user_already_exists") {

                    $("#register-error-cont").html(
                        '<a href="login">' +
                        '<div id="register-error" style="display: flex; cursor: pointer; justify-content: center; font-family: \'Raleway\', sans-serif; padding: 10px 20px; font-size: 1.1rem; line-height: 1.5; background-color: #00acc1; color: #fff; border-radius: 10px;  margin-top: 5px;">' +
                        '<span id="register-error-span">User already exists, click here to login.</span></div>' +
                        '</a>'
                    );
                    //console.log("User Already Exists - AJAX");

                    register_error();
                } else if ((data.request_from_gamesmartz == "failed")) {
                    // console.log("request not from api")
                }
                // data comes back succcess from php db check, log in user
                if (data.result == "user_entered") {

                    //console.log("USER ENTERED - AJAX");
                    // user is logged in, forward to queue

                    window.location.href = "progress";

                }

            }
        });

    }

}



/////////////RESET PASSWORD EMAIL/////////////RESET PASSWORD EMAIL/////////////RESET PASSWORD EMAIL/////////////RESET PASSWORD EMAIL
function reset_password() {

    // ajax call flag
    flag = true;

    let reset_password_email = $("#reset-password-email").val();

    response_check = "no response, captcha not fired";

    // when google captcha is clicked, a hidden input is created on the page, we need the value of this to pass into check, this is setup var
    g_recaptcha_response = "";

    captcha_enable = "on";

    // js response from captcha to check for checkmark
    response_check = grecaptcha.getResponse();


    // js captcha check

    if (response_check.length == 0) {

        $("#reset-pass-error").css({ "display": "flex" });
        $("#reset-pass-error-span").text("Please complete the captcha.");
        //console.log("Please complete the captcha - JS");

        reset_pass_error();

        flag = false;
    } else if (reset_password_email == "") {

        $("#reset-pass-error").css({ "display": "flex" });
        $("#reset-pass-error-span").text("Please enter a valid email.");
        //console.log("Please enter a valid email. - JS BLANK");

        reset_pass_error();

        flag = false;
    } else if (reg_for_email.test(reset_password_email) == false) {

        $("#reset-pass-error").css({ "display": "flex" });
        $("#reset-pass-error-span").text("Please enter a valid email.");
        //console.log("Please enter a valid email. - JS FORMAT");

        reset_pass_error();

        flag = false;
    }

    g_recaptcha_response = $('#g-recaptcha-response').val();

    //console.log( reset_password_email + ' - ' + g_recaptcha_response);

    if (flag) {
        $.ajax({
            url: "ajax/async-signin.php?action=reset-pass",
            dataType: "json",
            type: "POST",
            data: {
                reset_password_email: reset_password_email,
                captcha_enable: captcha_enable,
                g_recaptcha_response: g_recaptcha_response
            },
            error: function(xhr) {

                // remove to see any email sending errors
                // console.log(xhr.responseText);
            },
            success: function(data) {

                //console.log(data);

                // captcha failed
                if (data.captcha_state == "captcha_state_fail") {

                    $("#reset-pass-error").css({ "display": "flex" });
                    $("#reset-pass-error-span").text("Please complete the Captcha.");
                    //console.log("Please complete the Captcha. - AJAX");
                    reset_pass_error();
                }

                // email not valid
                else if (data.email == "email_not_valid") {

                    $("#reset-pass-error").css({ "display": "flex" });
                    $("#reset-pass-error-span").text("Please enter a vaild email address.");
                    //console.log("Please enter a vaild email address. - AJAX");
                    reset_pass_error();
                } else if (data.mailer_function == "mailer-function-failed") {
                    $("#reset-pass-error").css({ "display": "flex" });
                    $("#reset-pass-error-span").text("Mail Reset Failed.");
                    reset_pass_error();
                } else if ((data.request_from_gamesmartz == "failed")) {
                    $("#reset-pass-error").css({ "display": "flex" });
                    $("#reset-pass-error-span").text("Mail Reset Failed from Api.");
                    reset_pass_error();
                }

                // show same no matter what happens
                else {

                    $("#reset-pass-error-cont").html(
                        '<div id="reset-pass-error" style="font-family: \'Raleway\', sans-serif; padding: 10px 20px; font-size: 1.4rem; line-height: 1.5; background-color: #00acc1; color: #fff; border-radius: 10px; margin-top: 40px;">' +
                        'If you had an email with us,' + '<br>' + 'we sent an email to this address!' +
                        '</div>'
                    );

                    // reset successful, hide everything for clarity
                    $("#reset-pass-instructions").removeClass("show-flex").addClass("hide");
                    $("#reset-pass-input-elements").removeClass("show-flex").addClass("hide");
                    $("#captcha-cont").removeClass("show-flex").addClass("hide");

                    //console.log("Check your email account for info on resetting your password. - AJAX");

                    reset_pass_error();
                }

            }
        });

    }
}



// Global, use for this function only
let change_pass_login_attempt = 0;
/////////////RESET PASSWORD AFTER EMAIL CREATE_PASSWORD.PHP/////////////RESET PASSWORD AFTER EMAIL CREATE_PASSWORD.PHP/////////////RESET PASSWORD AFTER EMAIL CREATE_PASSWORD.PHP
function change_password() {

    // ajax call flag
    flag = true;

    change_pass_login_attempt++;

    var new_password = $("#new_password").val();
    var repeat_password = $("#repeat_password").val();
    var user_id = $("#user_id").val();

    // most likely never used, but only used if user clicks change password 6 times, use captcha
    if (change_pass_login_attempt >= 6) {

        $("#captcha-cont").removeClass("hide").addClass("show-flex");

        captcha_enable = "on";

        // js response from captcha to check for checkmark
        response_check = grecaptcha.getResponse();

        // js captcha check
        if (response_check.length == 0) {
            $("#register-error").css({ "display": "flex" });
            $("#register-error-span").text("Please complete the captcha");
            //console.log("Please complete the captcha. - JS");

            register_error();

            flag = false;
        }
        //checked
        else if (response_check.length != 0) {

            $("#captcha-cont").removeClass("show-flex").addClass("hide");

            change_pass_login_attempt = 0;
            grecaptcha.reset();

            g_recaptcha_response = $('#g-recaptcha-response').val();
        }
    }

    // if new pass blank or incorrect length
    else if ((new_password == "") || (repeat_password == "")) {

        $("#new-password-error").css({ "display": "flex" });
        $("#new-password-error-span").text("Please enter an password.");
        //console.log(" Please enter an password. - JS Blank");

        new_password_error();

        flag = false;
    } else if (new_password != repeat_password) {

        $("#new-password-error").css({ "display": "flex" });
        $("#new-password-error-span").text("Please have passwords match.");
        //console.log(" Please have passwords match. - DONT MATCH JS");

        new_password_error();

        flag = false;
    }

    // if pass is less than 8 or greater than 64
    else if ((new_password.length <= 8) || (new_password.length > 64)) {
        $("#new-password-error").css({ "display": "flex" });
        $("#new-password-error-span").text("Please make password at least 8 characters and include 1 number.");
        //console.log(" Please make password at least 8 characters and include 1 number - JS LENGTH");

        new_password_error();

        flag = false;
    }

    // password needs to include 1 number
    else if (reg_for_one_number.test(new_password) == false) {
        $("#new-password-error").css({ "display": "flex" });
        $("#new-password-error-span").text("Please make password at least 8 characters and include 1 number.");
        //console.log("Please make password at least 8 characters and include 1 number - JS NUMBER");

        new_password_error();

        flag = false;
    }


    //console.log( new_password + ' - ' + new_password.length  );


    if (flag) {
        $.ajax({
            url: "ajax/async-signin.php?action=update-password",
            dataType: "json",
            type: "POST",
            data: { user_id: user_id, new_password: new_password, repeat_password: repeat_password, captcha_enable: captcha_enable, g_recaptcha_response: g_recaptcha_response },

            error: function(xhr) {
                //console.log(xhr.responseText);
            },
            success: function(data) {

                //console.log(data);

                if (data.password_length == "password_length_fail") {

                    $("#new-password-error").css({ "display": "flex" });
                    $("#new-password-error-span").text("Please make password at least 8 characters and include 1 number.");
                    //console.log("Please make password at least 8 characters and include 1 number - AJAX LENGTH");

                    new_password_error();
                } else if (data.password_number == "password_without_number") {

                    $("#new-password-error").css({ "display": "flex" });
                    $("#new-password-error-span").text("Please make password at least 8 characters and include 1 number.");
                    //console.log("Please make password at least 8 characters and include 1 number - AJAX NUMBER");

                    new_password_error();
                } else if (data.pass_match == "pass_dont_match") {

                    $("#new-password-error").css({ "display": "flex" });
                    $("#new-password-error-span").text("Please have Passwords Match.");
                    //console.log("Please have Passwords Match - AJAX MATCH");

                    new_password_error();
                } else if ((data.request_from_gamesmartz == "failed")) {
                    // console.log("request not from api")
                } else if (data.password_state == "password_updated") {

                    $("#new-password-error").css({ "display": "flex" });
                    $("#new-password-error-span").text("Password Updated!");
                    //console.log(data.password_state);

                    new_password_error();

                    change_pass_login_attempt = 6;

                    setTimeout(function() {
                        window.location.href = "login";
                    }, 1500);
                }


            }
        });
    }

}
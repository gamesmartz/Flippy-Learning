function onSignOut(){
    var session_token = $("#session_token").val();
    $.ajax({
        url: "ajax/async-signOut.php",
        dataType : "json",
        type : "POST",
        data : {session_token: session_token },
        success : function(data){
            if(data.result == "success"){
                // removed php extension
                window.location.href = "/";
                return;
            } else {

                console.log ("Sign Out Failed!");
                window.location.href = "/";
                return;
            }
        }
    });
}
$(document).ready(function(){
    $("#change-password-link").click(function(){
        $(".profile-modal-wrapper").bPopup();
        $("#change-password").css("display","block");
        $("#match-error").css("display","none");
        $("#update-contact").css("display","none");
        $("#change-subscription").css("display","none");
        $("#new-password").val("");
        $("#confirm-password").val("");
    });

    $("#update-contact-link").click(function(){
        $(".profile-modal-wrapper").bPopup();
        $("#update-contact").css("display","block");
        $("#contact-error").css("display","none");
        $("#change-password").css("display","none");
        $("#change-subscription").css("display","none");
        $("#user_name").val("");
        $("#new_email").val("");
    });
});

function update_password(){
    var flag = true;
    $("#match-error").css("display","none");
    var user_id = $("#user_id").val();
    var new_password = $("#new-password").val();
    var confirm_password = $("#confirm-password").val();

    var reg = /\d/;
    if(new_password != confirm_password){
        flag = false;
        $("#match-error-span").html("passwords do not match.");
        $("#match-error").css("display","block");
    }
    else if(new_password.length < 6){
        flag = false;
        $("#match-error-span").html("password too short, use at least 6 characters and 1 number.");
        $("#match-error").css("display","flex");
    }
    else if(reg.test(new_password) == false){
        flag = false;
        $("#match-error-span").html("password needs to include at least one number.");
        $("#match-error").css("display","block");
    }

    if(flag){
        $.ajax({
            url: "ajax/async-updateUser.php?action=password",
            dataType : "json",
            type : "POST",
            data : { user_id : user_id, new_password : new_password },
            success : function(data){
                if(data.result == "success"){
                    $("#match-error-span").html("Password Updated!");
                    $("#match-error").css("display","block");
                    return;
                } else if (data.result == "not_long_enough") {
                    $("#match-error-span").html("password too short, use at least 6 characters and 1 number.");
                    $("#match-error").css("display","flex");
                }
                else{
                   // console.log("fail");
                    return;
                }
            }
        });
    }
}

function update_contact(){
    $("#contact-error").css("display","none");
    var flag = true;
    var user_id = $("#user_id").val();

    var new_email = $("#new_email").val();

    var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

     if(new_email == ""){
        $("#contact-error").css("display","flex");
        $("#contact-error-span").text("Please enter a valid email address.");
        $("#new_email").focus();

         $('#contact-error').addClass("highlight-border-red-2px");
         // call after .5 sec
         setTimeout(function () {
             $('#contact-error').removeClass("highlight-border-red-2px");
         }, 500);

        flag = false;
    }
     else if (reg.test(new_email) == false){
        $("#contact-error").css("display","flex");
        $("#contact-error-span").text("Please enter a valid email address.");

         $('#contact-error').addClass("highlight-border-red-2px");
         // call after .5 sec
         setTimeout(function () {
             $('#contact-error').removeClass("highlight-border-red-2px");
         }, 500);

        $("#new_email").focus();
        flag = false;
    }
    if(flag){
        $.ajax({
            url: "ajax/async-updateUser.php?action=contact",
            dataType : "json",
            type : "POST",
            data : { user_id : user_id, new_email : new_email },
            success : function(data){
                if(data.result == "success"){
                    $("#contact-error").css("display","flex");
                    $("#contact-error-span").text("Email Updated!");

                    $('#contact-error').addClass("highlight-border-green-2px");
                    // call after .5 sec
                    setTimeout(function () {
                        $('#contact-error').removeClass("highlight-border-green-2px");
                    }, 500);


                } else if (data.result == "email-validation-failed") {
                    $("#contact-error").css("display","flex");
                    $("#contact-error-span").text("Please enter a valid email address.");

                    $('#contact-error').addClass("highlight-border-red-2px");
                    // call after .5 sec
                    setTimeout(function () {
                        $('#contact-error').removeClass("highlight-border-red-2px");
                    }, 500);
                }
            }
        });
    }
}

function update_subscription(){
    $("#subscription-error").css("display","none");
    var flag = true;
    var user_id = $("#user_id").val();
    var subscription = $("#subscription").val();

    if(subscription == ""){
        $("#subscription-error").css("display","block");
        $("#subscription-error").text("Please edit the subscription!");
        $("#subscription").focus();
        flag = false;
    }else if(subscription != 1 && subscription != 0){
        $("#subscription-error").css("display","block");
        $("#subscription-error").text("Please edit 1 or 0!");
        $("#subscription").focus();
        flag = false;
    }

    if(flag){
        $.ajax({
            url: "ajax/async-updateUser.php?action=subscription",
            dataType : "json",
            type : "POST",
            data : { user_id : user_id, subscription : subscription },
            success : function(data){
                if(data.result == "success"){
                    alert("Subscription Updated!");
                    $(".profile-modal-wrapper").bPopup().close();
                    $('.top-navlinks > li > ul').fadeOut(500);
                    $(".top-navlinks > li > ul > li > a").removeClass("orange-text");
                    return;
                }else{
                    alert("Subscription Update Failed!");
                    return;
                }
            }
        });
    }
}

function close_expire(){
    window.location.href = "index.php";
}

///////////////////GAMESMARTZ IS A TOOL TO HELP KIDS LEARN
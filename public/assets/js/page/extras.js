$(document).ready(function () {
    $("#interval-time").ddslick();
    $("#ante-post").ddslick();
    $("#timezone").ddslick();
    $("#sms-interval").ddslick();
});

/* change page min width event */
function changeMinWidth() {
    if ($('.extras-min-width-change-target').length > 0) {
        $(".page-min-width").removeClass("page-min-width").addClass("page-min-width-extras");
    }
}

$(document).ready(function () {
    /* call change min width when page loads */
    changeMinWidth();
});

/* save reports value to users event when current logged in user subscription is 1 and show modal Popup */
function submit_changes() {

    var flag = true;

    var type = "save_change";

    var reports_time = [];

    /*if ($("#interval-time .dd-selected-value").val() != "")
        reports_time[0] = $("#interval-time .dd-selected-value").val();
    else*/
    reports_time[0] = 9;

    /*if ($("#ante-post .dd-selected-value").val() != "")
        reports_time[1] = $("#ante-post .dd-selected-value").val();
    else*/
    reports_time[1] = "PM";

    // if ($("#timezone .dd-selected-value").val() != "")
    //     reports_time[2] = $("#timezone .dd-selected-value").val();
    // else
    reports_time[2] = "PST";

    var reports_option = [];

    //reports_option[0] = $("#email-subscription").is(":checked") ? 1 : 0;
    //reports_option[1] = $("#text-reports").is(":checked") ? 1 : 0;

    // there were to say yes or no to email and text reports, set them to off by default
    reports_option[0] = 0;
    reports_option[1] = 0;

    var reports_phone = $.trim($("#report-phone").val());

    // if the field is blank, set the option to not send out report
    if ( reports_phone == "" ) {
        reports_option[1] = 0;
    } else {
        // if phone is not blank, set text to on
        reports_option[1] = 1;
    }

    if (!(reports_phone == "")) {
        // 1st format the text, after, if the text still does not match, show error

        let text_everything_removed = reports_phone.replace(/\D/g,'');

        let text_with_dash = text_everything_removed.slice(0,3) + "-" + text_everything_removed.slice(3,6) + "-" + text_everything_removed.slice(6);

            if ( !(/^[0-9]{3}[\-]{1}[0-9]{3}[\-]{1}[0-9]{4}$/).test(text_with_dash)) {
                $("#phone-error").css("display", "flex");
                $("#report-phone").focus();
                $('#phone-error').addClass("highlight-border-red-2px");
                // call after .5 sec
                setTimeout(function () {
                    $('#phone-error').removeClass("highlight-border-red-2px");
                }, 500);

                flag = false;
            } else {
                reports_phone = text_with_dash;
                $("#report-phone").val(reports_phone);
            }

        }

    // this can be used to send to other country
    // var country_code = $.trim($("#country-code").val());
    //
    //     if (!(country_code == "")) {
    //         if ( !(/[\+]{1}[0-9]{3}/).test(country_code)) {
    //             $("#country-code-error").css("display", "block");
    //             $("#country_code").focus();
    //             return;
    //         } else {
    //             $("#country-code-error").css("display", "none");
    //         }
    //     }

    var reports_interval = $("#sms-interval .dd-selected-value").val();
        if (reports_interval == "") {
            reports_interval = 20;
        }

     if (flag) {
         $.ajax({
             url: "/ajax/async-updateUser/reports",
             dataType: "json",
             type: "POST",
             data: {
                 type: type,
                // reports_email: reports_email,
                // reports_time: reports_time,
                 reports_option: reports_option,
                 reports_phone: reports_phone,
                // country_code: country_code,
                 reports_interval: reports_interval
             },
             success: function (data) {
                 if (data.result == "success") {
                   //  console.log('success');
                     $('#changes-saved-modal').modal();
                 }
             }
         });
     }

}

/* close save modal Popup */
function close_changeModal() {
    $('#changes-saved-modal').modal('hide');
}
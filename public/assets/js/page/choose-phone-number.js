
function choose_phone_number() {

    $("#phone-empty-error").css("display", "none");  // no phone error off by default

    var flag = true;

    var submitted_phone_number = $.trim($("#phone_number").val());
    var country_code = $.trim($("#country_code").val());
    var phone_number_opt_out = $("#phone_number_opt_out").is(':checked');

    if (phone_number_opt_out) {
        window.location.href = "queue.php";
        return;
    }

    // regex checks for numbers and dashes only
    if ( !(/^[0-9\-]+$/).test(submitted_phone_number) ) {
                $("#phone-empty-error").css("display", "block");
                $("#phone_number").focus();
                flag = false;
                return;
            } else {
                $("#phone-empty-error").css("display", "none");
                // remove non chars from string
                submitted_phone_number = submitted_phone_number.replace(/\D/g,'');
            }


    // regex checks for numbers and + only
    if (!(country_code == "")) {
        if ( !(/^[0-9\+]+$/).test(country_code) ) {
                        $("#country-code-error").css("display", "block");
                        $("#country_code").focus();
                        flag = false;
                       return;
                }
                else {
                $("#country-code-error").css("display", "none");
                // remove non chars from string
                country_code = country_code.replace(/\D/g,'');
                }
        }

if (flag) {
        $.ajax({

            url: "ajax/async-submit-phone-number-on-registration.php?action=choose-phone-number",
            dataType: "json",
            type: "POST",
            data: {submitted_phone_number: submitted_phone_number, country_code: country_code},

            success: function (data) {
                            if (data.result == "success") {
                                window.location.href = "queue.php";
                                return;
                            } else {
                                alert("Phone Number Update Failed!");
                                return;
                            }
                        }
        });
    }
}
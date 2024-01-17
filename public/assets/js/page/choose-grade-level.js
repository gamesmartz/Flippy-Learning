$(document).ready(function () {
    /* call change min width when page loads */
    $('select#current_grade').ddslick();
    $('a#submit_choose_grade').click(function(e) {
        e.preventDefault();
        var current_grade = $("#current_grade .dd-selected-value").val();

        var errorHtml = '<div class="error-massage" id="captcha-error" style="">Please choose your grade</div>';
        if (current_grade == '') {
            $('div#current_grade').remove('div.error-message').prepend(errorHtml);
            return false;
        }
        /* if the students grade is not kindergarden, then subtract 1 from grade to allow them to select tests 1 level below them */
        if (current_grade != 1) {
            current_grade--;
        }

        $.ajax({
            url: "ajax/async-submit-grade-level.php", // destination
            dataType: "json", // format to recieve back
            type: "POST",
            data: {current_grade_level: current_grade}, // the data being sent to the server in json format
            // to call if request succeeds.. pass in the result (data)
            success: function (data) {
                if (data.result == "success") {
                    if (data.result == "success") {
                        window.location.href = "choose-tests.php";
                        return;
                    }
                } else {
                    alert('Choose Grade Failed!');
                }
            }
        });
        /*<div class="error-massage" id="captcha-error" style="">
            Please complete the captcha
        </div>*/
    })
});
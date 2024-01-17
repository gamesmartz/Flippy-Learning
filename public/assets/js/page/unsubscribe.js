function unsubscribe() {

    $('.forget-modal-wrapper').bPopup();

}

/* un-subscribe event */
function unsubscribe_continue() {

    var user_id = $("#user_id").val();
    var md5_email = $("#md5_email").val();

    $.ajax({
        url: "ajax/async-updateUser.php?action=unsubscribe",
        dataType: "json",
        type: "POST",
        data: {user_id: user_id, md5_email: md5_email},
        success: function (data) {
            if (data.result == "success") {
                $('.forget-modal-wrapper').bPopup().close();
            }
        }
    });

}
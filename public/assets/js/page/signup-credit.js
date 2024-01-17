$(document).ready(function () {
    /* define select box to jquery ddslick */
    $("#state").ddslick();
    $("#choose-month").ddslick();
    $("#choose-year").ddslick();

});

/* submit billing modal popup and set values to modal elements */
function submit_billing() {

    var flag = true;

    var first_name = $("#first_name").val();
    var last_name = $("#last_name").val();
    var address_1 = $("#address_1").val();
    var address_2 = $("#address_2").val();
    var city = $("#city").val();
    var zipcode = $("#zipcode").val();
    if (first_name == "" || last_name == "" || address_1 == "" || city == "" || zipcode == "") {
        flag = false;
    }

    var region = $("#state .dd-selected-value").val();
    if (region == "") {
        flag = false;
    } else {
        $("#region").val(region);
    }

    if (flag) {
        $(".billing-form").css("display", "none");
        $(".card-form").css("display", "block");
        $(".order-form").css("display", "none");
    } else {
        $(".require-modal-wrapper").bPopup();
    }

}

/* submit card modal popup and set values to modal elements */
function submit_card() {

    var flag = true;

    var card_number = $("#card_number").val();
    var cvv = $("#cvv").val();
    if (card_number == "" || cvv == "") {
        flag = false;
    }

    var month = $("#choose-month .dd-selected-value").val();
    var year = $("#choose-year .dd-selected-value").val();
    if (month == "" || year == "") {
        flag = false;
    } else {
        $("#month").val(month);
        $("#year").val(year);
    }

    if (flag) {
        $(".billing-form").css("display", "none");
        $(".card-form").css("display", "none");
        $(".order-form").css("display", "block");
    } else {
        $("#require_message").text("Please input the card info!");
        $(".require-modal-wrapper").bPopup();
    }

}

/* close require modal */
function close_requieModal() {
    $(".require-modal-wrapper").bPopup().close();
}
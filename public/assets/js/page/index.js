var reports_flag = false;
var reports_time = "";
var reports_id = "";

$(document).ready(function () {

// flips the card above the button
    $(".show-answer").click(function(){

        // changes color of button and text
        $(this).find(".show-answer-txt").toggleClass("show").toggleClass("hide");
        $(this).find(".hide-answer-txt").toggleClass("show").toggleClass("hide");

        // flip show image
        $(this).parent().siblings().find(".chapter-flip-card-inner").toggleClass("chapter-transform-rotate");
    });

});


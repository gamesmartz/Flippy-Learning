$(document).ready(function () {

    // flips the card above the button
    $(".show-answer").click(function(){

        // changes color of button and text
        $(this).find(".show-answer-txt").toggleClass("show").toggleClass("hide");
        $(this).find(".hide-answer-txt").toggleClass("show").toggleClass("hide");

        // flip show image
        $(this).parent().siblings().find(".chapter-flip-card-inner").toggleClass("chapter-transform-rotate");
    });


    // toggles the answer visibility for all the cards - does not flip them, just shows them
    $(".show-all-answers").click(function(){

        // hide button itself and show the other button
        $(this).removeClass("show").addClass("hide");
        $(this).siblings(".hide-all-answers").removeClass("hide").addClass("show");

        // hide buttons to change individual card
        $(".show-answer").removeClass("show").addClass("hide");


        // remove the class that has flipped any cards
        $(".chapter-flip-card-inner").removeClass("chapter-transform-rotate");
        // remove class that hides the front of this card
        $(".answer-card").removeClass("chapter-flip-card-back");
        // hides the card in the back, which is currently shown
        $(".no-answer-card").addClass("hide");

    });


    // toggles the no-answer visibility for all the cards - does not flip them, gets them ready to be flipped
    $(".hide-all-answers").click(function(){

        // hide button itself and show the other button
        $(this).removeClass("show").addClass("hide");
        // show show-all-answer button instead
        $(this).siblings(".show-all-answers").removeClass("hide").addClass("show");

        // put the classes back on, the way they were before show answer button was pressed
        $(".answer-card").addClass("chapter-flip-card-back");
        // hide non-answer cards and show answer cards
        $(".no-answer-card").removeClass("hide");


        // show buttons for fliping
        $(".show-answer").removeClass("hide").addClass("show");
        // put all individual buttons back to orange, if an issue
        $(".show-answer-txt").addClass("show").removeClass("hide");
        $(".hide-answer-txt").addClass("hide").removeClass("show");

    });

});
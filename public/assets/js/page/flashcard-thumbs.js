
// .scroll event is callback on every scroll, and in this function we store the value of the location to local storage // the normal functionality is, the .scroll function will only fire on scroll event.
// however, after we load the page, the ajax comes back and scrolls the page, this fires the event, so where the ajax starts at: 185px // is stored in storage, then after the ajax has rendedred, we set the page top to 185px
// thus the page is always at 185px, because the ajax is firing it after coming back // we need to stop the ajax from firing it, thus on page reload, set the innital flag to false, so on the very 1st time through it does not run
var dont_load_scroll_on_1st_run = false;

$(window).scroll(function() {
    if (dont_load_scroll_on_1st_run) {
        sessionStorage.scrollPosition = $(this).scrollTop();
        //console.log(sessionStorage.scrollTop);
    }
    // callback has ran through 1 time, so set to false
    dont_load_scroll_on_1st_run = true;
});


$(document).ready(function () {
///////////////////DEFAULT ACTION AND BINDINGS///////////////////DEFAULT ACTION AND BINDINGS///////////////////DEFAULT ACTION AND BINDINGS///////////////////DEFAULT ACTION AND BINDINGS


    // this check happens when there has been a pushed state from: history.pushState // the back and forward button fire this even which gets the variables from the URLs // so when there has been a state change, get vars from URL and render
    window.onpopstate = function(event) {

        // search the URL string for &vocabulary=Cell, return everything after, for example vocabulary = , passed in
        var grade = getQueryVariablefromURL('grade');
        var subject = getQueryVariablefromURL('subject');

        // if both exist, render the page, pass them in
        if ( grade && subject ) {

            $("#vocab-choose-grade .dd-selected-value").val(grade);
            if (grade == 'all') {
                $("#vocab-choose-grade .dd-selected").text('All Grades');
            } else {
                $("#vocab-choose-grade .dd-selected").text(grade + 'th Grade');
            }

            renderPage(subject, grade);
        }
    }

    // when page has loaded (PHP load), alway render a page
    if ( subject_GET && grade_GET ) {
        renderPage(subject_GET, grade_GET);
    }
    else {
        // else forward to index
        window.location = "/"
    }

///////////////////ON RESET CLICK ///////////////////ON RESET CLICK///////////////////ON RESET CLICK///////////////////ON RESET CLICK
    // on reset blank out input, and push /vocab search to URL, and render the default, with nothing in the URL
    $("#vocab_word_reset").on('click touchstart', function () {

        $("#vocab-choose-grade .dd-selected-value").val('all');
        $("#vocab-choose-grade .dd-selected").text('All Grades');

        // update browser URL
        history.pushState(null, null, 'flashcard-thumbs?grade=all&subject=science');

        renderPage('science', 'all' );

    });


///////////////////DDSCLICK RENDER AND ON SELECT ///////////////////DDSCLICK RENDER AND ON SELECT///////////////////DDSCLICK RENDER AND ON SELECT
    // category dropdown - add plugin to dropdown, allowing styling of section and option elements
    // http://designwithpc.com/Plugins/ddSlick

    $('#vocab-choose-grade').ddslick({

        onSelected: function(){
            var grade_id = $("#vocab-choose-grade .dd-selected-value").val();
            // console.log(grade_id);


            history.pushState(null, null, ('flashcard-thumbs?grade=' + spaces_to_html_spaces(grade_id) + '&subject=' + spaces_to_html_spaces(subject_GET)));


            renderPage(subject_GET, grade_id);

        }
    });

    // when clicking show flashcard button per flashcard
    $(".show-answer").click(function(){

        // changes color of button and text
        $(this).find(".show-answer-txt").toggleClass("show").toggleClass("hide");
        $(this).find(".hide-answer-txt").toggleClass("show").toggleClass("hide");

        // flip show image
        $(this).parent().siblings().find(".chapter-flip-card-inner").toggleClass("chapter-transform-rotate");
    });


    // show all answers toggle at top - depending on what button is shown
    $(".show-all-answers").click(function(){

        // show hide button itself
        $(this).removeClass("show").addClass("hide");
        $(this).siblings(".hide-all-answers").removeClass("hide").addClass("show");

        // hide buttons to change individual card
        $(".show-answer").removeClass("show").addClass("hide");

        // flips all cards
        $(".chapter-flip-card-inner").toggleClass("chapter-transform-rotate");
    });

    // hide all answers toggle at top - depending on what button is shown
    $(".hide-all-answers").click(function(){

        // show hide button itself
        $(this).removeClass("show").addClass("hide");
        $(this).siblings(".show-all-answers").removeClass("hide").addClass("show");

        // show buttons
        $(".show-answer").removeClass("hide").addClass("show");

        // put all individual buttons back to orange
        $(".show-answer-txt").addClass("show").removeClass("hide");
        $(".hide-answer-txt").addClass("hide").removeClass("show");

        // flips all cards
        $(".chapter-flip-card-inner").toggleClass("chapter-transform-rotate");
    });



}); // End Doc Ready


///////////////////RENDER PAGE ///////////////////RENDER PAGE ///////////////////RENDER PAGE ///////////////////RENDER PAGE ///////////////////RENDER PAGE
function renderPage (subject, grade_id) {

    var newcontent = "";

        $.ajax({
            url: "ajax/async-flashcard-thumbs.php",
            dataType: "json",
            type: "POST",
            data: {subject: subject, grade_id: grade_id},
            error: function (xhr) {
                //console.log(xhr.responseText);
            },
            success: function (data) {


                ///////////////////IF SUCCESS DB CALL ///////////////////IF SUCCESS DB CALL///////////////////IF SUCCESS DB CALL///////////////////IF SUCCESS DB CALL
                if (data.result == "success") {

                    for (var i = 0; i < data.vocab_results.length; i++) {

                       // convert image string to png and webp
                         var png_800_file_path = data.vocab_results[i].image_350;
                         var webp_800_file_path = (data.vocab_results[i].image_350).replace("png", "webp").replace("375", "375-webp");

                        // TEMP CODE USED TO CHECK TO MAKE SURE ALL PNG AND WEBP FILES ARE PRESENT LOCALLY, NOT PUSHED TO PRODUCTION
                        // var png_800_file_path = data.vocab_results[i].image_350;
                        // png_800_file_path = png_800_file_path.replace("375", "800-no-text");
                        // var webp_800_file_path = (data.vocab_results[i].image_350).replace("png", "webp")
                        // webp_800_file_path = webp_800_file_path.replace("375", "800-no-text-webp");

                        newcontent += '<div style="margin-bottom: 30px;">';

                        newcontent += '<div class="flashcard-thumbs-width">';

                                    newcontent += '<a href="definitions?definition=' + data.vocab_results[i].question_id + '&' + spacestoDashes(data.vocab_results[i].question_answer) + ' "> ';

                                        newcontent += '<picture>';

                                        newcontent += '<source data-srcset="' + webp_800_file_path + '"type="image/webp">';
                                        newcontent += '<source data-srcset="' + png_800_file_path + '"type="image/png">';

                                        newcontent += '<img class="lazyload img-thumbs-page" style="min-height: 206px;" data-src="' + png_800_file_path + '" src="upload/subjects/science/default/gs-375-default.png"  alt="' + firstWordToUpperCase(data.vocab_results[i].question_title) + '" title="' + allWordsToUpperCase(data.vocab_results[i].question_answer) + ' Flash Card">';

                                        newcontent += '</picture>';

                                  newcontent += '</a>';

                        newcontent += '</div>';
                        newcontent += '</div>';

                    }
                    $('#vocab_search_div').html(newcontent);

                    // update subject name at top
                    if (  !(grade_id == 'all') ) {
                        $("#top-subject-name").text(data.vocab_results[0].extra_subject_name);
                    } else {
                        $("#top-subject-name").text('Science');
                    }

                    // jquery, sets the vertical scroll to value from scrollTop var, stored in the browsers storage, this fires after the ajax html content is completed
                    if (sessionStorage.scrollTop != "undefined") {
                        $(window).scrollTop(sessionStorage.scrollPosition);
                    }


                ///////////////////IF SUCCESS DB CALL ///////////////////IF SUCCESS DB CALL///////////////////IF SUCCESS DB CALL///////////////////IF SUCCESS DB CALL
                } else if (data.result == "notfound") {

                    newcontent += '<div style="margin: 20px 0;">';
                    newcontent += '<span style="font-family: \'Raleway\', sans-serif; color:#fff; font-size: 1.1rem;">Grade Not Found in This Subject</span>';
                    newcontent += '<div style="margin: 5px 0;">';
                    newcontent += '<img style="max-width: 265px;" class="vocab-image highlight-border-image" src="upload/subjects/science/subject-names/magnifying-glass-vocab-not-found.png" alt="Magnifying Glass Not Found">';
                    newcontent += '</div>';
                    newcontent += '</div>';

                    $('#vocab_search_div').html(newcontent);


                } else {
                    //console.log('Ajax did not return a flag');
                }
            }
        });

}
$(document).ready(function() {


    ///////////////////DEFAULT ACTION AND BINDINGS///////////////////DEFAULT ACTION AND BINDINGS///////////////////DEFAULT ACTION AND BINDINGS///////////////////DEFAULT ACTION AND BINDINGS
    // same check that happens on every document.ready, this check happens when there has been a pushed state from: history.pushState
    // and then the back or forward button has been pressed
    // the back and forward button fire this even which gets the variables from the URLs, to make the page accurate
    window.onpopstate = function(event) {

        // search the URL string for &vocabulary=Cell, return everything after, for example vocabulary = , passed in

        ////// hard coded grade to be 'all', instead of a drop down selectable
        var grade = 'all';

        var vocabulary = getQueryVariablefromURL('vocabulary');

        // if both exist, render the page, pass them in
        if ((grade != false) && (vocabulary != false)) {
            default_renderPage(vocabulary, grade);
        } else {
            // else render the page, dont pass them in (default page)
            default_renderPage();
        }
    }

    // when page has loaded (PHP load), alway render a page
    // these variables are echoed copies of the PHP equiv

    ////// hard coded grade to be 'all', instead of a drop down selectable
    grade_GET = 'all';

    if (typeof vocabulary_GET !== 'undefined') {
        default_renderPage(vocabulary_GET, grade_GET);
    } else {
        // if vocabulary_GET not set render default page
        default_renderPage();
    }



    ///////////////////ON CLICK RENDER PAGE///////////////////ON CLICK RENDER PAGE///////////////////ON CLICK RENDER PAGE///////////////////ON CLICK RENDER PAGE///////////////////ON CLICK RENDER PAGE
    $('#vocab_word_search_btn').on('click touchstart', function() {
        search_button_pressed_or_return();
    });
    // on return button click
    $('#vocab_word').keypress(function(e) {
        if (e.which == 13) {
            search_button_pressed_or_return();
        }
    });

    // on reset blank out input, and push /vocab search to URL, and render the default, with nothing in the URL
    $("#vocab_word_reset").on('click touchstart', function() {
        $("#vocab_word").val('');

        // update browser URL even on default render page
        history.pushState(null, null, ('vocab-search'));

        default_renderPage();
    });

    function search_button_pressed_or_return() {
        var search_form_vocab_word = $("#vocab_word").val();
        search_form_vocab_word = remove_all_non_characters(search_form_vocab_word.toLowerCase());

        //blank submit check
        if (search_form_vocab_word == "") {
            $(".enter-vocab-word").bPopup();
        } else {
            // get grade_id from the value


            ////// hard coded grade to be 'all', instead of a drop down selectable
            // var grade_id = $("#vocab-choose-grade .dd-selected-value").val();

            var grade_id = 'all';


            // search button was pressed
            var button_pressed = 1;

            // update browser URL
            history.pushState(null, null, ('vocab-search?grade=' + spaces_to_html_spaces(grade_id) + '&vocabulary=' + spaces_to_html_spaces(search_form_vocab_word)));


            renderPage(search_form_vocab_word, grade_id, button_pressed);
        }
    }


    ///////////////////DDSCLICK GRAPHIC///////////////////DDSCLICK GRAPHIC///////////////////DDSCLICK GRAPHIC///////////////////DDSCLICK GRAPHIC///////////////////DDSCLICK GRAPHIC
    // category dropdown - add plugin to dropdown, allowing styling of section and option elements
    // http://designwithpc.com/Plugins/ddSlick
    /////// hard coded grade to be all
    // $("#vocab-choose-grade").ddslick();


    ///////////////////AUTO COMPLETE///////////////////AUTO COMPLETE///////////////////AUTO COMPLETE///////////////////AUTO COMPLETE///////////////////AUTO COMPLETE

    let ajax_subject = 'science';

    setup_autocomplete(ajax_subject);

    function setup_autocomplete(ajax_subject) {

        $.ajax({
            url: "ajax/async-autocomplete-data/bysubject",
            dataType: "json",
            type: "POST",
            data: { subject: ajax_subject },
            error: function(xhr) {
                console.log(xhr.responseText);
            },
            success: function(data) {
                if (data.result == "success") {

                    let canonical_objects_array = [];
                    let object_canonical_item = {};

                    data.canonical_results.forEach(function(current_array_item) {

                        //  console.log(current_array_item);

                        object_canonical_item = {
                            value: current_array_item
                        }
                        canonical_objects_array.push(object_canonical_item);

                    });

                    // console.log(canonical_objects_array);

                    // devbridge auto complete with options
                    $('#vocab_word').autocomplete({
                        lookup: canonical_objects_array,
                        // maxHeight: '100',
                        width: '220',
                        lookupLimit: '10',
                        triggerSelectOnValidInput: false,
                        lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                            // checks to see if 1st letter matches with suggestion and answer
                            return suggestion.value.toLowerCase().indexOf(queryLowerCase) === 0;
                        },
                        onSelect: function(suggestion) {
                            search_button_pressed_or_return();
                        },
                    });

                } else {
                    // console.log('ajax response failed');
                }
            }
        });
    }



});


//////////////////REMOVE FROM QUE//////////////////REMOVE FROM QUE//////////////////REMOVE FROM QUE//////////////////REMOVE FROM QUE//////////////////REMOVE FROM QUE//////////////////REMOVE FROM QUE
//// function to remove from que, which is done by updating the que json string in the DB and showing 'Add to que' when compelted
function remove_que(object, test_id) {

    var type = "que";
    $.ajax({
        url: "ajax/async-tests.php?action=delete",
        dataType: "json",
        type: "POST",
        data: { type: type, test_id: test_id },
        success: function(data) {
            if (data.result == "success") {

                $(object).parents(".add-remove-que").html('<a role="button" class="fw-bold text-decoration-none text-dark" onclick="add_que(this,' + test_id + ')">Add to Queue</a>');
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}

///////////////////ADD TO QUE///////////////////ADD TO QUE///////////////////ADD TO QUE///////////////////ADD TO QUE///////////////////ADD TO QUE///////////////////ADD TO QUE
//// function to remove from que, which is done by updating the que json string in the DB and showing 'In Queue' when compelted
function add_que(object, test_id) {

    var type = "que";
    $.ajax({
        url: "ajax/async-tests.php?action=add",
        dataType: "json",
        type: "POST",
        data: { type: type, test_id: test_id },
        success: function(data) {
            if (data.result == "success") {

                $(object).parents(".add-remove-que").html('<a role="button" class="fw-bold text-decoration-none text-dark" onclick="remove_que(this,' + test_id + ')">In Queue</a>');
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}

///////////////////DEFAULT RENDER PAGE///////////////////DEFAULT RENDER PAGE///////////////////DEFAULT RENDER PAGE///////////////////DEFAULT RENDER PAGE///////////////////DEFAULT RENDER PAGE
function default_renderPage(vocabulary_GET, grade_GET) {
    // if vocabulary_GET is defined, render page with GET content
    if (typeof vocabulary_GET !== 'undefined' && typeof grade_GET !== 'undefined') {

        renderPage(vocabulary_GET, grade_GET);
    } else {
        var newcontent = "";

        newcontent += '<div class="col-md-12 text-center">';

        newcontent +=
            '<picture>' +
            '<source data-srcset="upload/subjects/science/vocab-search/vocab-search.webp" type="image/webp">' +
            '<source data-srcset="upload/subjects/science/vocab-search/vocab-search.png" type="image/png">' +
            '<img class="lazyload rounded img-fluid" alt="5th Grade Science" style="box-shadow: 0px 0px 0px 2px #f3821f;" ' +
            'data-src="upload/subjects/science/vocab-search/vocab-search.png" ' +
            'src="upload/subjects/science/default/gs-375-default.png">' +
            ' </picture>';

        newcontent += '</div>';

        $('#vocab_search_div').html(newcontent);
    }
    // for default render, clear input field for this page always
    $("#vocab_word").val('');

}

///////////////////RENDER PAGE AFTER SEARCH///////////////////RENDER PAGE AFTER SEARCH///////////////////RENDER PAGE AFTER SEARCH///////////////////RENDER PAGE AFTER SEARCH///////////////////RENDER PAGE AFTER SEARCH
function renderPage(search_term, grade_id, button_pressed) {
    // if submitted with no term, return false
    if (!search_term) {
        return false;
    }
    $.ajax({
        url: "ajax/async-vocab-search.php",
        dataType: "json",
        type: "POST",
        data: { vocab_word: search_term, grade_id: grade_id },
        error: function(xhr) {
            // console.log(xhr.responseText);
        },
        success: function(data) {

            if (data.result == "success") {

                var newcontent = "";
                for (var i = 0; i < data.vocab_results.length; i++) {

                    newcontent += '<div class="col-md-6 py-4">';
                    newcontent += '<div class="container">';
                    newcontent += '<div class="row">';
                    newcontent += '<div class="col-6 text-start">';
                    newcontent += '<a class="btn btn-sm btn-secondary btn-app-secondary" href="grade?grade=' + data.vocab_results[i].subject_id + ' ">';
                    newcontent += data.vocab_results[i].grade_name;
                    newcontent += '</a>';
                    newcontent += '</div>';
                    newcontent += '<div class="col-6 text-end add-remove-que">';
                    if (data.vocab_results[i].in_que == 'NO-SESSION') {
                        // DONT ADD QUEUE BUTTON
                    } else if (data.vocab_results[i].in_que == 'In Que') {
                        newcontent += '<a role="button" class="fw-bold text-decoration-none text-dark" onclick="remove_que(this,' + data.vocab_results[i].test_id + ')">In Queue</a>';
                    } else {
                        newcontent += '<a role="button" class="fw-bold text-decoration-none text-dark" onclick="add_que(this,' + data.vocab_results[i].test_id + ')">Add to Queue</a>';
                    }
                    newcontent += '</div>';
                    newcontent += '</div>';
                    newcontent += '<div class="col-md-12 text-center pt-2">';
                    newcontent += '<a href="chapter?chapter=' + data.vocab_results[i].test_id + '#' + spacestoDashes((data.vocab_results[i].question_answer).toLowerCase()) + '"> ';
                    newcontent += '<img class="img-thumbnail border-primary" src="' + data.vocab_results[i].image_350 + '"alt="' + firstWordToUpperCase(data.vocab_results[i].question_title) + '" title="' + allWordsToUpperCase(data.vocab_results[i].question_answer) + ' Flash Card">';
                    newcontent += '</a>';
                    newcontent += '</div>';
                    newcontent += '</div>';
                    newcontent += '</div>';
                }
                $('#vocab_search_div').html(newcontent);


                // put in search term
                $("#vocab_word").val(search_term.toLowerCase());

                // if the search button was pressed, after done rendering, add a 2 second highlight to the border, to indicate the search was successful
                if (button_pressed == 1) {
                    highlight_border_green();
                }
            } else if (data.result == "notfound") {

                var newcontent = "";
                newcontent += '<div class="col-md-12 text-center">';

                newcontent +=
                    '<picture>' +
                    '<source data-srcset="upload/subjects/science/vocab-search/vocab-search-not-found.webp" type="image/webp">' +
                    '<source data-srcset="upload/subjects/science/vocab-search/vocab-search-not-found.png" type="image/png">' +
                    '<img class="rounded img-fluid lazyload" alt="5th Grade Science" style="box-shadow: 0px 0px 0px 2px #f3821f;" ' +
                    'data-src="upload/subjects/science/vocab-search/vocab-search-not-found.png" ' +
                    'src="upload/subjects/science/default/gs-375-default.png">' +
                    ' </picture>'


                //newcontent += '<span style="font-family: \'Raleway\', sans-serif; color:#fff; font-size: 1.1rem;">Vocab Not Found</span>';

                newcontent += '</div>';

                $('#vocab_search_div').html(newcontent);

                // update even on a bad search
                // update browser URL
                history.pushState(null, null, ('vocab-search?grade=' + spaces_to_html_spaces(grade_id) + '&vocabulary=' + spaces_to_html_spaces(search_term)));

                // if the search button was pressed, after done rendering, add a 2 second highlight to the border, to indicate the search was successful
                if (button_pressed == 1) {
                    highlight_border_red();
                }
            } else {
                console.log('Ajax did not return a flag');
            }
        }
    });
}


///////////////////HIGHLIGHT BORDERS///////////////////HIGHLIGHT BORDERS///////////////////HIGHLIGHT BORDERS///////////////////HIGHLIGHT BORDERS///////////////////HIGHLIGHT BORDERS
// adds a green class, then removes that class, to make it obvious a search was done, (if the search terms are the same)
function highlight_border_green() {

    $('.vocab-image').removeClass("highlight-border-image");
    $('.vocab-image').addClass("highlight-border-green");

    // call after .8 sec
    setTimeout(function() {
        $('.vocab-image').removeClass("highlight-border-green");
        $('.vocab-image').addClass("highlight-border-image");

    }, 800);
}

function highlight_border_red() {

    $('.vocab-image').removeClass("highlight-border-image");
    $('.vocab-image').addClass("highlight-border-red");

    // call after .8 sec
    setTimeout(function() {
        $('.vocab-image').removeClass("highlight-border-red");
        $('.vocab-image').addClass("highlight-border-image");

    }, 800);
}
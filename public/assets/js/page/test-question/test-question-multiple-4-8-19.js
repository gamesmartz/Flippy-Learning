$(document).ready(function() {

    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });


    // note - many of the doc.ready bindings can not happen until the ajax comes back, so they are bound after that logic instead of here

    // things to do if 'return' is hit
    $(document).keyup(function(event) {
        // if return is hit
        if (event.keyCode == 13) {
            // if master modal is open
            if ($("#mastered-modal").css("display") == "block") {
                mastered_modal_close();
                // if require modal is open
            } else if ($("#require-modal").css("display") == "block") {
                close_requieModal();
                // else manually submit the question (even if its blank it will just ask to enter info)
            } else {
                question_submit();
                // after submit, set the focus on answer again for easy input
            }
        }
    });

    // on document ready, refresh the page 1 time (this allows for new javacript files to be loaded (this one) for the overlay
    if ($("#question_form").hasClass("test-in-game-flag")) {
        if (window.localStorage) {
            if (!localStorage.getItem('firstLoad')) {
                localStorage['firstLoad'] = true;
                window.location.reload();
            } else
                localStorage.removeItem('firstLoad');
        }
    }
});


// holds html play object
var player;
// holds array of audio url
var playlist = [];
$(document).ready(function() {

    // make html audio object
    player = document.getElementById("audio");
    //// after audio has ended, go to next question. this is the main driver of that event
    // we are no longer using the audio length to move onto the next question (we are not reading the correct answer)
    // also, on computers that have no audio, the question does not move on
    // instead we just play the 'smart' audio and move on immediately, so this logic is not used
    player.addEventListener('ended', function(e) {
        // if (!submitFlag) {
        //     submitFlag = true;
        //
        //     $(".hide-text").removeClass("answer-correct");
        //     $(".hide-text").removeClass("answer-incorrect");
        //     $(".hide-text").removeClass("dont-know");
        //     $(".fill-answer-value").css("display", "none");
        //     $(".question_submit").css("background-color", "#2f9892");
        //
        //     if (question_start_point != -1) {
        //         var opt = getOptionsFromForm();
        //         opt['current_page'] = question_start_point;
        //         $("#Pagination").pagination(members.length, opt);
        //     }
        to_next_question();
        //}
    });

    // on doc ready, check playFlag value if this value is true, hide question form. if false, enable pagination
    if (playFlag) {
        $(".hide-text").css("display", "none");
        $("#question_form").css("width", "auto");
        $("#question_form").css("padding", "0px 30px 15px");
    } else {
        // Create pagination element with options from form
        var optInit = getOptionsFromForm();
        optInit['current_page'] = question_start_point;
        $("#Pagination").pagination(members.length, optInit);
    }
});

// global array to store if file exists data, if the audio file exist is checked on every new element, and the results put in this array
// this is needed here because, audio can not be fired off an ajax event, so we need to do the check here, so when button press happens later, we can fire the audio and it will work
// based on these results
var files_exist = [];

///////////////////////////// MAIN HTML RENDER, CALL BACK FROM PAGINATION SETUP ///////////////////////////// MAIN HTML RENDER, CALL BACK FROM PAGINATION SETUP
// this renders the HTML on the page, calls every time anything new happens, and uses pagnation to display 1 element from 'members' array at a time
function pageselectCallback(page_index) {
    // Item per page must 1.
    var items_per_page = 1;
    // max elements based on members.length
    var max_elem = Math.min((page_index + 1) * items_per_page, members.length);

    // holds all new html content
    var newcontent = '';

    // for every question render html string
    for (var i = page_index * items_per_page; i < max_elem; i++) {
        // put question_id into hidden input
        newcontent += '<input type="hidden" id="question_id" value="' + members[i][0] + '">';

        // put correct answer value into hidden input
        newcontent += '<input type="hidden" id="answer_value" value="' + members[i][6].toLowerCase() + ' "class="textbox">';

        // if, word answer exists, put it into a hidden field for audio
        if (members[i][21] != "") {
            newcontent += '<input type="hidden" id="full-answer-notes" value="' + members[i][21] + '">';
        }

        // point row
        //  newcontent += '<div class="point-row" id="question_point" style="height: 22px;">' + numeral(total_points).format('0,0') + '</div>';

        newcontent += '<div style="display: flex; flex-direction:column; "><div class="text-dark" style="padding: 0 0 3px; font-family: \'Luckiest Guy\', cursive; font-size: 1.3em; font-weight: 400; color: #000; line-height: 1.2em; ">mastery</div></div>';

        newcontent += '<div style="display: flex; justify-content: space-around; margin-bottom: 10px; margin-top: 1px;">';
        newcontent += '<div class="progress_left under_part">';
        newcontent += '<div class="border_container">';
        newcontent += '<div class="tenth_separator"></div>';
        newcontent += '<div class="tenth_separator"></div>';
        newcontent += '<div class="tenth_separator"></div>';
        newcontent += '<div class="tenth_separator"></div>';
        newcontent += '<div class="tenth_separator"></div>';
        newcontent += '<div class="tenth_separator"></div>';
        newcontent += '<div class="tenth_separator"></div>';
        newcontent += '<div class="tenth_separator"></div>';
        newcontent += '<div class="tenth_separator"></div>';
        newcontent += '<div class="tenth_separator"></div>';
        newcontent += '</div>';
        newcontent += '<div id="progress-bar-left" class="progress-bar progress-bar-success" style="width: 0%;"></div>';
        newcontent += '</div>';
        newcontent += '<div class="progress_right under_part">';
        newcontent += '<div class="border_container">';
        newcontent += '<div class="third_separator"></div>';
        newcontent += '<div class="third_separator"></div>';
        newcontent += '<div class="third_separator"></div>';
        newcontent += '</div>';
        newcontent += '<div id="progress-bar-right" class="progress-bar progress-bar-success" style="width: 0%;"></div>';
        newcontent += '</div>';
        newcontent += '</div>';

        // if, load pre question audio on render
        if (members[i][3] != "") {
            if (toggled) {
                var list_num = playlist.push((window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + members[i][3]);
                if (list_num == 1) {
                    play();
                }
            }
            // if, show audio button instead
            if (members[i][5] == 1) {
                newcontent += '<div class="question-row" id="question_play">';
                newcontent += '<input type="hidden" id="question_audio" value="' + members[i][3] + '">';
                newcontent += '<a onclick="play_words(0)" class="icon-audio" onmouseover="row_mouseover(this)" onmouseout="row_mouseout(this)"></a>';
                newcontent += '</div>';
            }
        }
        // if, show image instead
        if (members[i][2] != "") {
            newcontent += '<div class="question-img" id="question_image">';
            if (members[i][4] == 1) {
                newcontent += '<img class="question-attach img-fluid" style="border-radius: 5px;" src="' + (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + members[i][2] + '" draggable="false" alt="">';
            } else {
                newcontent += '<img class="question-attach img-fluid" style="border-radius: 5px;" src="' + (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + 'assets/images/gs-answer-logo.png" draggable="false" alt="">';
            }
            newcontent += '</div>';
        }

        // container for the input and buttons
        newcontent += '<div id="inputs_container" style="">';

        newcontent += '<div id="correct-answer-hidden" style="display: flex; justify-content: center; margin-top: 8px;" class="d-none">';
        newcontent += '<div id="incorrect-answer-bubble" class="chapter-bubble btn btn-primary btn-app-primary" style="padding: 6px 15px; background-color: #f3821f !important; text-transform: capitalize; font-size: 1.2em;">' + members[i][6] + '</div>';
        newcontent += '</div>';
        newcontent += '<div class="mt10">';

        // since the #answer field is manually populated in the c#, and the cursor is added and removed every second, we need to put what is typed into #answer into #filter-answer, and strip it of the cursor and everything else.
        // then we run autocomplete on this input, and append the results to the div holding the input box
        newcontent += '<input type="hidden" id="filter-answer" value="" class="" style="">';
        newcontent += '<div id="txt-input-container" class="text-center" style="display: flex; justify-content: center; margin-top: 8px;">';

        // main input field. On the overlay, this is manually entered into, with a artifcial cursor, and it updates every second. autocomplete="new-password" needed to stop the default autofill dropdown
        // in game input needs differnt margins and spacing, and cant change it after the fact, or the input jumps
        if ($("#question_form").hasClass("test-in-game-flag")) {
            newcontent += '<input type="text" id="answer" autocomplete="off" value="" class="input-field-container text-start form-control" style="width: 100%; max-width:375px; border-radius: 5px; height: 38px;" onmouseover="row_mouseover(this)" onmouseout="row_mouseout(this)">';
        }
        // not in game input needs differnt margins and spacing, and cant change it after the fact, or the input jumps
        else {
            newcontent += '<input type="text" id="answer" autocomplete="off" value="" class="input-field-container text-start form-control" style="width: 100%; max-width:375px; border-radius: 5px; padding-left: 6px; height: 38px;" onmouseover="row_mouseover(this)" onmouseout="row_mouseout(this)">';

        }

        //newcontent += '</div>';

        newcontent += '</div>';
        newcontent += '</div>';

        // if not in the game show 'give answer' button
        if (!$("#question_form").hasClass("test-in-game-flag")) {
            newcontent += '<div style="display: flex; justify-content: center; margin-top: 40px;">';
            newcontent += '<div class="btn-answer-submit not-in-game-button-hover btn btn-primary btn-app-primary" style="font-size: 1.1em;"  onclick="question_submit()" onmouseover="row_mouseover(this)" onmouseout="row_mouseout(this)">Answer</div>';
            newcontent += '</div>';
        }

        // if question is in the game render button
        if ($("#question_form").hasClass("test-in-game-flag")) {

            newcontent += '<div class="" style="display: flex; justify-content: center; margin-top: 5px; height: 50px">';
            newcontent += '<button class="question_submit btn btn-answer-submit chapter-bubble" style="background-color: #00acc1; font-size: 1.1em; text-align: center; cursor: pointer; border-color: #00acc1; " onclick="question_submit()" onmouseover="row_mouseover(this)" onmouseout="row_mouseout(this)">';
            newcontent += '<span id="submit-text">answer</span><span class="icon-next"></span></button>';
        }

        // newcontent += '<a class="toggle-sound ' + (toggled ? 'toggled' : '') + '" onclick="toggle_sound()" onmouseover="row_mouseover(this)" onmouseout="row_mouseout(this)" style="display: none;"></a>';

        // TEMPORARILY TURNED OFF sizing buttons until debug. After resizing, if you hit the question mark, it would shrink when showing the correct answer
        // if (window.location.href.indexOf('test-question-in-game') != -1) {
        //     newcontent += '<div id="zoom-buttons" style="display: flex; flex-direction: column; justify-content: space-between; height: 50px; width: 25px; position: absolute;  right: 0;">';
        //     newcontent += '<div id="zoom-plus" class="zoom-plus" style="" onmouseover="row_mouseover(this)" onmouseout="row_mouseout(this)" onclick="zoom_question(&#39;+&#39;)">+</div>';
        //     newcontent += '<div id="zoom-minus" class="zoom-minus" style="" onmouseover="row_mouseover(this)" onmouseout="row_mouseout(this)" onclick="zoom_question(&#39;-&#39;)">-</a>';
        //     newcontent += '</div>';
        // }

        newcontent += '</div>';
        // end contain with height
        newcontent += '</div>';

    } /// main newcontent += end

    /// Replace old content with new content
    $('#question_form').html(newcontent);



    ///////////////////AUTO COMPLETE SETUP///////////////////AUTO COMPLETE SETUP///////////////////AUTO COMPLETE SETUP///////////////////AUTO COMPLETE SETUP
    // change this per subject
    let ajax_id = test_id;

    setup_autocomplete(ajax_id);

    function setup_autocomplete(ajax_id) {

        $.ajax({
            url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-autocomplete-data/byid",
            dataType: "json",
            type: "POST",
            data: { id: ajax_id },
            error: function(xhr) {
                //  console.log(xhr.responseText);
            },
            success: function(data) {
                if (data.result == "success") {

                    let canonical_objects_array = [];
                    let object_canonical_item = {};

                    data.canonical_results.forEach(function(current_array_item) {
                        // console.log(current_array_item);

                        object_canonical_item = {
                            value: current_array_item
                        }
                        canonical_objects_array.push(object_canonical_item);

                    });
                    // console.log(canonical_objects_array);

                    //////////////////IF IN GAME FLAG SETUP//////////////////IF IN GAME FLAG SETUP//////////////////IF IN GAME FLAG SETUP//////////////////IF IN GAME FLAG SETUP

                    // if #question_form has the class of test-in-game-flag (from test-question-in-game.php)
                    if ($("#question_form").hasClass("test-in-game-flag")) {

                        let innitial_overlay_height;

                        // get innital overlay height after 1 second to account for the fact this is done in an async ajax call
                        setTimeout(function() {
                            innitial_overlay_height = $(window).height();
                            // for debugging in overlay
                            //$('#submit-text').text(innitial_overlay_height);
                        }, 1000);


                        $('#answer').autocomplete({
                            lookup: canonical_objects_array,
                            lookupLimit: '2',
                            onSearchStart: function() {

                                // on the start of every search, get the current overlay height, jquery happens to get the overlay height correctly
                                let curent_overlay_height = $(window).height();

                                // if current is larger than innitial height, increase font size of drop down, by adding special class
                                if (curent_overlay_height > innitial_overlay_height) {
                                    //console.log(curent_overlay_height);
                                    $('.autocomplete-suggestions').addClass('autocomplete-suggestions-after-zoom');
                                }

                            },
                            lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                                // checks to see if 1st letter matches with suggestion and answer
                                if ((queryLowerCase).charAt(0) == $("#answer_value").val().charAt(0)) {
                                    return suggestion.value.toLowerCase().indexOf(queryLowerCase) === 0;
                                }
                            },
                            onSelect: function(suggestion) {
                                $("#answer").val(suggestion.value);
                            },
                        });

                    }
                    //////////////////// ELSE NOT IN OVERLAY //////////////////// ELSE NOT IN OVERLAY //////////////////// ELSE NOT IN OVERLAY //////////////////// ELSE NOT IN OVERLAY
                    else {
                        $('#answer').autocomplete({
                            lookup: canonical_objects_array,
                            width: '200',
                            lookupLimit: '5',
                            lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                                //keep rendering if, the 1st letter matches and the 3rd letter is not incorrect
                                if (((queryLowerCase).charAt(0) == $("#answer_value").val().charAt(0)) && ((queryLowerCase).charAt(1) == $("#answer_value").val().charAt(1))) {
                                    return suggestion.value.toLowerCase().indexOf(queryLowerCase) === 0;
                                }

                            },
                            onSelect: function(suggestion) {
                                $("#answer").val(suggestion.value);
                                // dont auto submit answer after select (wait for button press, so its similar to overlay)
                                // question_submit();
                            },
                        });

                        // game is not in the overlay so add styles to center the input and add height to the point area
                        $("#question_point").css("height", "30px");
                        // add non-game dark border and larger top margin
                        $("#question_image").addClass("non-game-border");
                        $("#question_image").addClass("non-game-border-margin");
                        // increase padding and font size on non-game correct answer
                        $("#incorrect-answer-bubble").addClass('non-game-correct-answer-formatting');
                        $("#question_point").addClass("hide");
                        $("#left-time").addClass("hide");
                        $("#inputs_container").css("margin-top", "10px");
                    }

                } else {
                    // console.log('ajax response for keyterms');
                }
            }
        });
    }



    // event handler added after ajax add, on focusin or focusout add/remove highlight
    $("#answer").focusin(function() {
        $("#answer").addClass('highlight-border-orange');
    });
    $("#answer").focusout(function() {
        $("#answer").removeClass('highlight-border-orange');
    });


    // on every new question load, check to see if the full audio exists and the complement file exists
    // this is critical because the question will not move on until this audio is called, so we need to set flags here before the audio is called
    var full_audio = $("#full-answer-notes").val();
    // shorthand ajax call to check to see if url exists, if yes, call done, if not call fail
    $.get('../' + full_audio)
        .done(function() {
            files_exist[0] = "full_audio_found";
        }).fail(function() {
            files_exist[0] = "full_audio_not_found";
        });

    // not using random complement
    // var k = Math.floor(Math.random() * compliments.length);
    // if (k > compliments.length - 1) {
    //     k = compliments.length - 1;
    // }

    var complement = compliments[0][1];
    // shorthand ajax call to check to see if url exists, if yes, call done, if not call fail
    $.get('../' + complement)
        .done(function() {
            files_exist[1] = "complement_audio_found";
        }).fail(function() {
            files_exist[1] = "complement_audio_not_found";
        });
    //console.log(files_exist);


    get_set_progress_left_width();
    get_set_progress_right_width(page_index);

    return;
} //// end of html string creation per page



// fields for pagiantion options
function getOptionsFromForm() {
    var opt = { callback: pageselectCallback };
    // Set pagination config
    opt['items_per_page'] = 1;
    opt['next_text'] = "Next";
    opt['num_display_entries'] = 5;
    opt['num_edge_entries'] = 2;
    opt['prev_text'] = "Prev";
    // Avoid html injections in this demo
    var htmlspecialchars = { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" }
    $.each(htmlspecialchars, function(k, v) {
        opt.prev_text = opt.prev_text.replace(k, v);
        opt.next_text = opt.next_text.replace(k, v);
    })
    return opt;
}

////////////// PLAY WORDS ON CORRECT OR INCORRECT //////////////////////////////// PLAY WORDS ON CORRECT OR INCORRECT //////////////////////////////// PLAY WORDS ON CORRECT OR INCORRECT
// if the audio does not exist, or file does not exist go to the next question after an interval
function play_words(flag) {
    // if flag 0, play full sentance
    if (flag == 0) {
        var file_path = $("#full-answer-notes").val();
        // global array storing if the files exist

        // COMMENTED OUT, THEN CHANGED MIND, NOT PLAY AUDIO ON INCORRECT ANSWER
        if (files_exist) {
            if (files_exist[0] == "full_audio_found") {
                // the file does exists, so puts the audio file in the global array playlist, this is played when audio is played with play()
                playlist = [(window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + file_path];
                play();
            } else if (files_exist[0] == "full_audio_not_found") {
                // file did not exist, go to next question after 3 sec
                setTimeout(function() {
                    to_next_question();
                }, 3000);
            }
        }
    } // if flag 1, play compliment
    else if (flag == 1) {
        // the complement location passed in
        file_path = compliments[0][1];
        // use ajax to check to see if file exists with full path, if it does, play it, if not go onto next question after 3 seconds
        if (files_exist) {
            if (files_exist[1] == "complement_audio_found") {
                // the file does exists, so puts the audio file in the global array playlist, this is played when audio is played with play()
                playlist = [(window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + file_path];
                play();
                setTimeout(function() {
                    to_next_question();
                }, 1000);
            } else if (files_exist[1] == "complement_audio_not_found") {
                // file did not exist, go to next question after 3 sec
                setTimeout(function() {
                    to_next_question();
                }, 1000);
            }
        }
    }
}

// use html player object, to play the playlist
function play() {
    player.src = playlist[0];
    player.volume = 0.7;
    player.load();
    player.play();
}

// used for targeting in  mouseovered_click()
function row_mouseover(object) {
    $(object).addClass("mouseovered");
}
// when mouse over elements, pass in 'this', add class to 'this', to keep track what the mouse is over, this is used for targeting of mouseovered_click()
function row_mouseout(object) {
    $(object).removeClass("mouseovered");
}


////////////////////// MAIN DRIVER OF C# FORM ////////////////////// MAIN DRIVER OF C# FORM ////////////////////// MAIN DRIVER OF C# FORM
// the c# form runs mouseovered_click when it detects a click event.
// if the element has the correct class on it, then the result of that click is returned to the c# form
// for example, return zoom_question(), returns "zoom:::" + shrink[zoom]; this is returned to the c# form to know what zoom to execute
// the question_sumbmit() returns: return_value += ":::" + (flag ? "true" : "false") + ":::" + action; to c# form to know if the question was correct,
// and to also know if the flag was 'help' or not, etc.

function mouseovered_click() {
    if ($(".mouseovered").hasClass("zoom-plus")) {
        return zoom_question('+');
    } else if ($(".mouseovered").hasClass("zoom-minus")) {
        return zoom_question('-');
    } else if ($(".mouseovered").hasClass("btn-help-submit")) {
        return question_submit('help');
    } else if ($(".mouseovered").hasClass("btn-answer-submit")) {
        return question_submit();
    } else {
        $(".mouseovered").click();
    }
    if ($(".mouseovered").attr("class") == "btn btn-green mouseovered" || $(".mouseovered").attr("class") == "b-close mouseovered") {
        $(".mouseovered").removeClass("mouseovered");
    }
}

////////////////////// RUN ON EVERY C# KEYPRESS ////////////////////// RUN ON EVERY C# KEYPRESS////////////////////// RUN ON EVERY C# KEYPRESS
//// this takes the keyboard input from the c# overlay form and passes it into the #answer input
//// this is mission critical
function input_answer(answer) {
    //  if (caret != null) {
    $("#answer").val(answer);
    // since devbridge autocomplete triggers on keyup, that does not happen here, so we manually have to call the autocomplete update here
    $('#answer').autocomplete('onValueChange');
    //  }
}

// save if audio toggle is on or off to DB
function toggle_sound() {
    if (toggled) {
        toggled = false;
        var audio = 0;
        $(".toggle-sound").removeClass("toggled");
    } else {
        toggled = true;
        var audio = 1;
        $(".toggle-sound").addClass("toggled");
    }
    // pass in if the audio is 0 or 1, to ajax, to write value to config, db
    $.ajax({
        url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-updateUser.php?action=audio",
        dataType: "json",
        type: "POST",
        data: { audio: audio },
        success: function(data) {
            if (data.result == "success") {} else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}

// this is called from mouseovered_click(), which is called on every click from the c# form
// this returns return "zoom:::" + shrink[zoom]; to the c# form, so it knows what zoom to preform
function zoom_question(option) {
    // flag false not zoomed yet
    var flag = false;
    // if button pressed and current zoom is greater than  0
    if (option == "+") {
        if (zoom > 0) {
            // subtract 1 zoom level (remember zoom 0 is largest) and turn the flag to true (has already zoomed)
            zoom--;
            flag = true;
        }
    } else {
        // add 1 zoom level (remember zoom 3 is smallest) and turn the flag to true (has already zoomed)
        if (zoom < 3) {
            zoom++;
            flag = true;
        }
    }
    // making sure zoom has taken place
    if (flag) {

        // we are not storing the current value of zoom to config db, for next use, we are always starting at 0 (largest setting)
        // $.ajax({
        //     url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-updateUser.php?action=zoom",
        //     dataType: "json",
        //     type: "POST",
        //     data: {zoom: zoom},
        //     success: function (data) {
        //         if (data.result == "success") {
        //
        //         } else if (data.result == "expire") {
        //             $('.expire-modal-wrapper').bPopup();
        //         }
        //     }
        // });

        // return in json format the current zoom level
        return "zoom:::" + shrink[zoom];
    }
}

// gets and sets left progress bar width (experience bar) based on current test array
function get_set_progress_left_width() {

    // curent question goodness: members[current_question][0];

    /// take lenth and * by mastery number (20 * 3) = 60
    let mastery_total = members.length * mastery_number;

    /// loop through array questions array, find the length
    let total_mastered_correct_atm = 0;
    for (let i = 0; i < members.length; i++) {
        if (members[i][23] != null) {
            total_mastered_correct_atm += parseInt(members[i][23]);
        }
    }
    /// take total mastery points / total possible ie ( 10 / 60)
    let mastery_percentage_atm = (total_mastered_correct_atm / mastery_total);
    mastery_percentage_atm = ((mastery_percentage_atm).toFixed(4)) * 100;

    /// change width of css progress bar to ( 10 / 60)
    $("#progress-bar-left").width(mastery_percentage_atm + '%');

    //console.log(mastery_percentage_atm);
}

// gets and sets right progress bar width (per question, to mastery bar) based on current test array
function get_set_progress_right_width(current_question) {
    /// find its current status 0-M
    // members[current_question][0];

    /// loop through array questions array, find the length
    let mastery_per_question_percentage_atm = 0;
    if (members[current_question][23] != null) {
        mastery_per_question_percentage_atm = (parseInt(members[current_question][23]) / parseInt(mastery_number));
    }

    /// change width of mastery bar with current status / mastery number
    mastery_per_question_percentage_atm = ((mastery_per_question_percentage_atm).toFixed(4)) * 100;
    $("#progress-bar-right").width(mastery_per_question_percentage_atm + "%");

    // console.log(mastery_per_question_percentage_atm);
}


function reset_progress() {
    // reset test progress to 0
    var type = "reset_progress";
    $.ajax({
        url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests/add",
        dataType: "json",
        type: "POST",
        data: { type: type, test_id: test_id },
        success: function(data) {
            if (data.result == "success") {
                console.log('success');
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}


function get_submitFlag() {
    if (submitFlag) {
        return "true";
    } else {
        return "false";
    }
}

var submitFlag = true;

//// called on every answer submit, starts all the logic, will eventually save the result, play audio, go to next question
function question_submit(action) {

    // on every submit, its time to hide the autocomplete drop down
    // also check to see if autocomplete element exists
    if ( $('.autocomplete-suggestions').length) {
        $('.autocomplete-suggestions').hide();
    }

    // if action is undefined (not passed in) set it to blank string
    if (typeof(action) === 'undefined') action = '';

    // if submitFlag global is true (has been submitted)
    if (submitFlag) {

        submitFlag = false;
        var flag = true;

        var current_question = $("#Pagination .current").text();
        current_question = current_question.replace("Prev", "");
        current_question = current_question.replace("Next", "");
        current_question = parseInt(current_question) - 1;

        // get the answer minus the caret, this is done here to check for the correct answer or not before moving on
        // not using the caret, so removed
        // var answer = $("#answer").val();
        // if (!caretFlag && typeof answer != "undefined") {
        //     // check to see if caret exists at the end
        //     if (answer.substr(answer.length - 1, 1) == "|") {
        //         // if so remove it and save answer
        //         answer = answer.substr(0, answer.length - 1);
        //     }
        // }
        // answer without caret
        // answer = answer.trim();

        // getting answer from input, trim both sides of white space
        var answer = $("#answer").val().trim();
        // get the correct answer value from hidden input, trim both sides of white space
        var answer_value = $("#answer_value").val().trim();

        // if action passed in was blank and answer input was blank, show modal asking for answer
        // we used to pop up a modal on a blank answer (when it was multiple choice) now we simply count it as the incorrect answer 'dont know'
        if (action == '' && answer == "") {
            //$("#require_message").text("Please input your answer.");
            //$(".require-modal-wrapper").bPopup();
            answer = "dont-know";
            //flag = false;
            submitFlag = true;
        }

        // a double check to make sure the question was submitted with data, set above to false if data did not exist
        if (flag) {
            // this flag is used for different purpose, used to tell if the answer was true or false below
            flag = false;
            var return_value = "submit";

            // when question was submitted, if the action passed in was not a blank string but 'help'
            if (action == 'help') {
                answer = "dont-know";
            }
            //// if the answer sumitted matches the answer_value, the answer is correct
            else if (answer.toLowerCase() == answer_value.toLowerCase()) {
                // answer is true, set to true
                flag = true;
            }

            // if global mastery_type is memorization (all the test are)
            if (mastery_type == "memorization") {

                // the post test starts at -1, and is set to non -1 after the test was mastered, if this is the case, after the test was mastered, start the post test
                // we are not using the post test (a test after the test is mastered) so commenting out this section

                // if (post_test != -1 && post_test < members.length) {
                //
                //     var test_mode = "post_test";
                //
                //     // post test number
                //     post_test++;
                //     if (post_test >= members.length) {
                //         question_start_point = 0;
                //         // initialize question_history
                //         question_history = question_history.map(history_map);
                //         members = members.map(history_map);
                //     } else {
                //         question_start_point = post_test;
                //     }
                //
                //     var type = test_mode;
                //     $.ajax({
                //         url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests/add",
                //         dataType: "json",
                //         type: "POST",
                //         data: {type: type, test_id: test_id, post_test: post_test},
                //         success: function (data) {
                //             if (data.result == "success") {
                //             } else if (data.result == "expire") {
                //                 $('.expire-modal-wrapper').bPopup();
                //             }
                //         }
                //     });
                //
                //     if (flag) {
                //
                //         total_points++;
                //         $(".point-row").text('+ 1');
                //         setTimeout(function () {
                //             $(".point-row").text(numeral(total_points).format('0,0'));
                //         }, 1000);
                //
                //         var time = new Date();
                //         correct_answered++;
                //         test_result.push({
                //             x: Date.UTC(time.getFullYear(), time.getMonth(), time.getDate(), time.getHours(), time.getMinutes(), time.getSeconds()),
                //             y: correct_answered,
                //             marker: {fillColor: 'rgb(8, 243, 53)'}
                //         });
                //
                //         var type = "complete";
                //         var completion_rate = 0;
                //         var question_id = members[current_question][0];
                //         $.ajax({
                //             url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests/add",
                //             dataType: "json",
                //             type: "POST",
                //             data: {
                //                 type: type,
                //                 test_id: test_id,
                //                 question_id: question_id,
                //                 completion_rate: completion_rate,
                //                 answer: answer,
                //                 test_mode: test_mode
                //             },
                //             success: function (data) {
                //                 if (data.result == "success") {
                //                 } else if (data.result == "expire") {
                //                     $('.expire-modal-wrapper').bPopup();
                //                 }
                //             }
                //         });
                //
                //         if (typeof stopAlarm === 'function') {
                //             stopAlarm();
                //             if (timerObj != null) {
                //                 if (left_time + award_time > max_time) { // max 15:00
                //                     left_time = max_time;
                //                 } else {
                //                     left_time += award_time;
                //                 }
                //             }
                //         }
                //
                //     } else {
                //
                //         var time = new Date();
                //         test_result.push({
                //             x: Date.UTC(time.getFullYear(), time.getMonth(), time.getDate(), time.getHours(), time.getMinutes(), time.getSeconds()),
                //             y: correct_answered,
                //             marker: {fillColor: 'rgb(245, 64, 50)'}
                //         });
                //
                //         var type = "not-complete";
                //         var completion_rate = 0;
                //         var question_id = members[current_question][0];
                //         $.ajax({
                //             url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests/add",
                //             dataType: "json",
                //             type: "POST",
                //             data: {
                //                 type: type,
                //                 test_id: test_id,
                //                 question_id: question_id,
                //                 answer: answer,
                //                 test_mode: test_mode,
                //                 completion_rate: completion_rate
                //             },
                //             success: function (data) {
                //                 if (data.result == "success") {
                //                 } else if (data.result == "expire") {
                //                     $('.expire-modal-wrapper').bPopup();
                //                 }
                //             }
                //         });
                //
                //         $(".hide-text").addClass("answer-incorrect");
                //         if (action == 'help') {
                //             $(".hide-text").addClass("dont-know");
                //         } else {
                //             total_points--;
                //             $(".point-row").text(numeral(total_points).format('0,0'));
                //             $(".point-row").css("color", "#F57B20");
                //             setTimeout(function () {
                //                 $(".point-row").css("color", "#9CDC48");
                //             }, 2000);
                //         }
                //         $(".question_submit").css("background-color", "#6faa24");
                //
                //     }
                //
                //     $("#question_image").html('<img class="question-attach" src="' + (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + members[current_question][2] + '" draggable="false" alt="">');
                //     $(".fill-answer-value").css("display", "inline-block");
                //     $(".help-link").css("display", "none");
                //     $(".question_submit").css("display", "none");
                //     $(".question_submit").parent(".mt15").css("padding-top", "38px");
                //
                //     total_answered++;
                //
                //     stop_report();
                //     chart_option = 0;
                //
                //     play_words(0);
                //     if (flag && toggled && compliments[k][1] != "") {
                //         var list_num = playlist.push(compliments[k][1]);
                //         if (list_num == 1) {
                //             play();
                //         }
                //     }
                //
                //     if (playlist.length == 0) {
                //         setTimeout(to_next_question,3000);
                //     }
                //
                // }

                //// else its not a post test, but a normal mastery test

                // else {


                //// start normal test
                var test_mode = "";

                // check to see if pre_test is complete
                // pre test is simply a record of the 1st time a question was answered, so this is recorded, it is simply a mode number in the DB

                if (pre_test < members.length && members[current_question][9] == null) {
                    test_mode = "pre_test";
                    // sets pre-test
                    pre_test++;

                    var type = test_mode;
                    $.ajax({
                        url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests/add",
                        dataType: "json",
                        type: "POST",
                        data: { type: type, test_id: test_id, pre_test: pre_test },
                        success: function(data) {
                            if (data.result == "success") {} else if (data.result == "expire") {
                                $('.expire-modal-wrapper').bPopup();
                            }
                        }
                    });

                } else {
                    // else pre test mode is done, and move into normal test mode
                    // a test mode is simply a record of how many times the user has taken a test
                    // the first time they take the test it is mode 0, second time mode 1, third, mode 2, etc
                    // this allows us to see how many times the user is taking the same test (and award, or dont award points accordingly)
                    test_mode = "test_" + mode_number;
                }

                // correct answer flag from above
                //// correct answer logic start
                if (flag) {

                    // answer was true, add 1 to total points
                    total_points++;

                    if (members[current_question][23] == null) {
                        members[current_question][22] = wait_time[1];
                        members[current_question][23] = 1;
                    } else {
                        // if less than mastery number continue loop
                        for (var i = 0; i < mastery_number; i++) {
                            if (members[current_question][23] == i) {
                                members[current_question][23] = i + 1;
                                // if this is greater than mastery number, set it to M
                                if (members[current_question][23] >= mastery_number) {
                                    members[current_question][22] = 'M';
                                    completion_num++;
                                } else {
                                    members[current_question][22] = wait_time[i + 1];
                                }
                                break;
                            } else if (members[current_question][23] >= mastery_number) {
                                members[current_question][22] = 'M';
                                completion_num++;
                                break;
                            }
                        }
                    }

                    // update time
                    var time = new Date();

                    // add to correct answered
                    correct_answered++;

                    test_result.push({
                        x: Date.UTC(time.getFullYear(), time.getMonth(), time.getDate(), time.getHours(), time.getMinutes(), time.getSeconds()),
                        y: correct_answered,
                        marker: { fillColor: 'rgb(8, 243, 53)' }
                    });

                    var completion_rate = Math.floor(100 * completion_num / members.length);

                    var type = "complete";

                    // the current question number
                    var question_id = members[current_question][0];

                    var new_mastered = 0;
                    if (mode_number == 0 && members[current_question][22] == 'M')
                        new_mastered = 1;

                    $.ajax({
                        url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests/add",
                        dataType: "json",
                        type: "POST",
                        data: {
                            type: type,
                            test_id: test_id,
                            question_id: question_id,
                            completion_rate: completion_rate,
                            answer: answer,
                            test_mode: test_mode,
                            new_mastered: new_mastered
                        },
                        success: function(data) {
                            if (data.result == "success") {} else if (data.result == "expire") {
                                $('.expire-modal-wrapper').bPopup();
                            }
                        }
                    });

                    /// remove timer
                    // since answer was correct, stop alarm and add to non overlay timer
                    // if (typeof stopAlarm === 'function') {
                    //     stopAlarm();
                    //     if (timerObj != null) {
                    //         // add to non-overlay timer
                    //         if (left_time + award_time > max_time) {
                    //             left_time = max_time;
                    //         } else {
                    //             left_time += award_time;
                    //         }
                    //     }
                    // }

                    // play complement on correct answer
                    // this also moves to the next question
                    play_words(1);
                    // put +1 test in .point-row
                    //$(".point-row").text('+ 1');

                    $(".progress_right").addClass("highlight-border-green-1px");

                    setTimeout(function() {
                        $(".progress_right").removeClass("highlight-border-green-1px");
                    }, 1000);

                } //// end correct answer

                //// incorrect answer logic start
                else {

                    members[current_question][22] = wait_time[0];
                    members[current_question][23] = 0;

                    var time = new Date();
                    test_result.push({
                        x: Date.UTC(time.getFullYear(), time.getMonth(), time.getDate(), time.getHours(), time.getMinutes(), time.getSeconds()),
                        y: correct_answered,
                        marker: { fillColor: 'rgb(245, 64, 50)' }
                    });

                    var type = "not-complete";
                    var completion_rate = Math.floor(100 * completion_num / members.length);
                    // current question number
                    var question_id = members[current_question][0];
                    $.ajax({
                        url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests/add",
                        dataType: "json",
                        type: "POST",
                        data: {
                            type: type,
                            test_id: test_id,
                            question_id: question_id,
                            answer: answer,
                            test_mode: test_mode,
                            completion_rate: completion_rate
                        },
                        success: function(data) {
                            if (data.result == "success") {} else if (data.result == "expire") {
                                $('.expire-modal-wrapper').bPopup();
                            }
                        }
                    });

                    // research:
                    if (action == 'help') {
                        $(".hide-text").addClass("dont-know");
                    } else {
                        // dont subract points on incorrect answer anymore
                        // total_points--;

                        // show total points in .point-row
                        //  $(".point-row").text(numeral(total_points).format('0,0'));
                        // make points orange
                        //   $(".point-row").css("color", "#F57B20");
                        // make points orange again after 2 seconds
                        //   setTimeout(function () {
                        //       $(".point-row").css("color", "#F57B20");
                        //   }, 2000);
                    }

                    // show image instead of question
                    // $("#question_image").html('<img class="question-attach" src="' + (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + members[current_question][2] + '" draggable="false" alt="">');

                    // unhide correct answer
                    $("#correct-answer-hidden").removeClass('d-none');
                    $("#txt-input-container").addClass('hide');
                    // removes the dark not-in-game border so the orange can show on incorrect answer
                    $("#question_image").removeClass("non-game-border");
                    $(".question-img").addClass("highlight-border-orange-thick");
                    $(".btn-answer-submit").addClass('hide');
                    $("#zoom-buttons").addClass('hide');
                    $("#answer").addClass('hide');

                    // play_words() with flag 0 (entire sentance) and move onto next question
                    play_words(0);

                } //// end incorrect answer logic

                //// come down other question logic (happens if answer is correct or not correct)
                var lowest = 0;
                var lowest_i = -1;
                var not_asked = '';
                for (var i = 0; i < members.length; i++) {

                    if (members[i][22] != 'M' && members[i][22] != null) {
                        if (i != current_question) {
                            members[i][22]--;
                        }
                        if (members[i][22] <= 0) {
                            if (lowest_i == -1) {
                                lowest = members[i][22];
                                lowest_i = i;
                            } else {
                                if (members[i][22] < lowest) {
                                    lowest = members[i][22];
                                    lowest_i = i;
                                } else if (members[i][22] == lowest) {
                                    if (members[lowest_i][23] > members[i][23]) {
                                        lowest_i = i;
                                    }
                                }
                            }
                        }
                    } else if (members[i][22] == null) {
                        if (not_asked == '') {
                            not_asked = i;
                        }
                    }
                }
                // find next question
                if (lowest_i == -1) {
                    if (not_asked == '') {
                        var lowest_correct = 0;
                        var correct_i = -1;
                        for (var i = 0; i < members.length; i++) {
                            if (members[i][23] < mastery_number) {
                                if (correct_i == -1) {
                                    lowest_correct = members[i][23];
                                    correct_i = i;
                                } else {
                                    if (members[i][23] < lowest_correct) {
                                        lowest_correct = members[i][23];
                                        correct_i = i;
                                    } else if (members[i][23] == lowest_correct) {
                                        if (members[i][22] < members[correct_i][22]) {
                                            correct_i = i;
                                        }
                                    }
                                }
                            }
                        }
                        // check Mastered or not
                        if (correct_i == -1) {
                            completion_num = 0;

                            /// Regular Test Mastered
                            mode_number++;

                            // add 1 to user level
                            user_level++;
                            var type = "add_1_to_level";
                            $.ajax({
                                url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests/add",
                                dataType: "json",
                                type: "POST",
                                data: { type: type, user_level: user_level },
                                success: function(data) {
                                    if (data.result == "success") {} else if (data.result == "expire") {
                                        $('.expire-modal-wrapper').bPopup();
                                    }
                                }
                            });


                            // post test not being used
                            // (post_test == -1)

                            if (post_test != -1) {
                                // go to post_test
                                post_test = 0;
                                question_start_point = 0;
                                var type = "post_test";
                                $.ajax({
                                    url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests/add",
                                    dataType: "json",
                                    type: "POST",
                                    data: { type: type, test_id: test_id, post_test: post_test },
                                    success: function(data) {
                                        if (data.result == "success") {} else if (data.result == "expire") {
                                            $('.expire-modal-wrapper').bPopup();
                                        }
                                    }
                                });
                                var type = "mode_update";
                                $.ajax({
                                    url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests/add",
                                    dataType: "json",
                                    type: "POST",
                                    data: { type: type, test_id: test_id, mode_number: mode_number },
                                    success: function(data) {
                                        if (data.result == "success") {} else if (data.result == "expire") {
                                            $('.expire-modal-wrapper').bPopup();
                                        }
                                    }
                                });
                            } else {
                                // disable pagination flag
                                question_start_point = -1;


                                // test was mastered
                                var type = "mastered";
                                $.ajax({
                                    url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests/add",
                                    dataType: "json",
                                    type: "POST",
                                    data: { type: type, test_id: test_id, mode_number: mode_number },
                                    success: function(data) {
                                        if (data.result == "success") {
                                            //mastered_report();
                                            //chart_option = 0;
                                            if (data.next_test == "") {
                                                // Show Queue Finished
                                                let mastered_que_empty = '';
                                                mastered_que_empty += '<div style="display: flex; flex-direction: column; justify-content: center; margin-top: 15px;">';
                                                mastered_que_empty += '<div><img src="' + (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + 'assets/images/test-mastered.png"></div>';
                                                mastered_que_empty += '<div style="margin-top: 10px; font-weight: 500; font-size: 1.3rem; font-family: \'Raleway\', sans-serif; color: #fff; margin-top: 20px;">';
                                                mastered_que_empty += '<div>Queue Finished!<div>';
                                                mastered_que_empty += '<div>Add More Tests to Your Queue to Continue<div>';
                                                mastered_que_empty += '</div>';
                                                // mastered_que_empty += '<div style="margin-top: 55px">';
                                                // mastered_que_empty += '<a href="choose-tests"><div class="btn btn-success" onclick="" <span>Add More Tests</span><span class="icon-next"></span></div></a>';
                                                // mastered_que_empty += '</div>';
                                                mastered_que_empty += '</div>';
                                                $("#question_form").html(mastered_que_empty);

                                                // reset the test progress to 0
                                                reset_progress();

                                            } else {
                                                let mastered_continue = '';
                                                mastered_continue += '<div style="display: flex; flex-direction: column; justify-content: center; margin-top: 15px;">';
                                                mastered_continue += '<div><img src="' + (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + 'assets/images/test-mastered.png"></div>';
                                                mastered_continue += '<div style="margin-top: 10px; font-weight: 500; font-size: 1.3rem; font-family: \'Raleway\', sans-serif; color: #fff; margin-top: 20px;">';
                                                mastered_continue += '<div>Test Mastered!<div>';
                                                mastered_continue += '<div>Level Up!<div>';
                                                mastered_continue += '<div>Continue to the next test!<div>';
                                                mastered_continue += '</div>';
                                                mastered_continue += '<div style="margin-top: 35px">';
                                                mastered_continue += '<button class="btn btn-success question_submit" onclick="next_que(' + data.next_test + ')" onmouseover="row_mouseover(this)" onmouseout="row_mouseout(this)"><span id="submit-text">next test</span><span class="icon-next"></span></button>';
                                                mastered_continue += '</div>';
                                                mastered_continue += '</div>';
                                                $("#question_form").html(mastered_continue);

                                                // reset the test progress to 0
                                                reset_progress();
                                            }

                                        } else if (data.result == "expire") {
                                            $('.expire-modal-wrapper').bPopup();
                                        }
                                    }
                                });

                            }
                        } else {
                            question_start_point = correct_i;
                        }
                    } else {
                        question_start_point = not_asked;
                    }
                } else {
                    question_start_point = lowest_i;
                }
                //// stop come down next question and record the results logic

                // if right or wrong, does not matter, add to total_answered
                total_answered++;
                stop_report();
                chart_option = 0;


                // after every question, get and set new experience bar width
                get_set_progress_left_width();
                get_set_progress_right_width(current_question);

            }
        }

        // update current state
        var type = "current_state";
        var insert_start_point = question_start_point;
        if (question_start_point == -1) {
            insert_start_point = 0;
        }

        for (var i = 0; i < members.length; i++) {
            question_history[i][0] = members[i][22];
            question_history[i][1] = members[i][23];
        }

        $.ajax({
            url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests/add",
            dataType: "json",
            type: "POST",
            data: {
                type: type,
                test_id: test_id,
                insert_start_point: insert_start_point,
                question_history: question_history
            },
            success: function(data) {
                if (data.result == "success") {} else if (data.result == "expire") {
                    $('.expire-modal-wrapper').bPopup();
                }
            }
        });

        return_value += ":::" + (flag ? "true" : "false") + ":::" + action;

        return return_value;

    }

} //// end question submit logic


/* close require modal Popup */
function close_requieModal() {
    $(".require-modal-wrapper").bPopup().close();
}

// used to be used to re-take
function retake_test(test_id, link) {
    var type = "retake_test";
    $.ajax({
        url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests/add",
        dataType: "json",
        type: "POST",
        data: { type: type, test_id: test_id },
        success: function(data) {
            if (data.result == "success") {
                // initialize question_history
                question_history = question_history.map(history_map);
                members = members.map(history_map);
                // go back to first question
                question_start_point = 0;
                var opt = getOptionsFromForm();
                opt['current_page'] = question_start_point;
                $("#Pagination").pagination(members.length, opt);
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}

/* next que event */
function next_que(test_id) {
    window.location.href = window.location.href.split('?')[0] + "?id=" + test_id + "&from=que";
}

/* stop studying */
function stop_report() {

    var type = "image/jpeg";
    var width = 600;

    print_question_correct();

    var question_correct_svg = question_correct_chart.getSVG();
    /*
    $.ajax({
        url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-studying/stop",
        dataType: "json",
        type: "POST",
        data: {
            type: type,
            test_id: test_id,
            correct_answered: correct_answered,
            total_answered: total_answered,
            report_date: report_date,
            width: width,
            question_correct_svg: question_correct_svg
        },
        success: function(data) {}
    });
    */

}

// modal close... find "button" and fire a click event
function mastered_modal_close() {
    $("#mastered-modal").find("button").click();
}


// used to do mastered report
function mastered_report() {

    var type = "image/jpeg";
    var width = 600;

    $("#pre_test").html(pre_test);
    $("#post_test").html(post_test);

    print_pre_post();

    var question_correct_svg = question_correct_chart.getSVG();
    var pre_post_svg = pre_post_chart.getSVG();

    $.ajax({
        url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-studying.php?action=mastered",
        dataType: "json",
        type: "POST",
        data: {
            type: type,
            test_id: test_id,
            correct_answered: correct_answered,
            total_answered: total_answered,
            report_date: report_date,
            width: width,
            question_correct_svg: question_correct_svg,
            pre_post_svg: pre_post_svg
        },
        success: function(data) {}
    });

}

/* draw correct question highchart */
function print_question_correct() {
    $('#question-correct-container').highcharts({
        chart: {
            type: 'spline',
            zoomType: 'x'
        },
        colors: ['#9ab2d6'],
        title: {
            text: 'Questions Correct Over Time'
        },
        subtitle: {
            text: document.ontouchstart === undefined ?
                'Click and drag in the plot area to zoom in' : 'Pinch the chart to zoom in'
        },
        xAxis: {
            type: 'datetime',
            labels: {
                format: '{value:%I:%M %p}'
            },
            title: {
                text: 'Time'
            }
        },
        yAxis: {
            title: {
                text: 'Questions Correct'
            },
            min: 0
        },

        //        legend: {
        //            enabled: false
        //        },
        //        plotOptions: {
        //            area: {
        //                fillColor: {
        //                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
        //                    stops: [
        //                        [0, Highcharts.getOptions().colors[0]],
        //                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
        //                    ]
        //                },
        //                marker: {
        //                    radius: 2
        //                },
        //                lineWidth: 1,
        //                states: {
        //                    hover: {
        //                        lineWidth: 1
        //                    }
        //                },
        //                threshold: null
        //            }
        //        },

        series: [{
            //type: 'area',
            name: 'Questions',
            // Define the data points. All series have a dummy year
            // of 1970/71 in order to be compared on the same x axis. Note
            // that in JavaScript, months start at 0 for January, 1 for February etc.
            data: test_result
        }]
    });
}

/* draw pre test and post test highchart */
function print_pre_post() {

    print_question_correct();

    $('#pre-post-container').highcharts({
        data: {
            table: document.getElementById('datatable')
        },
        chart: {
            type: 'column'
        },
        title: {
            text: 'Pre-Test vs Post Test'
        },
        yAxis: {
            allowDecimals: false,
            title: {
                text: 'Questions Correct'
            }
        },
        tooltip: {
            formatter: function() {
                return '<b>' + this.series.name + '</b><br/>' +
                    this.point.y + ' ' + this.point.name.toLowerCase();
            }
        }
    });
}

function to_next_question() {
    submitFlag = true;
    $(".hide-text").removeClass("answer-correct");
    $(".hide-text").removeClass("answer-incorrect");
    $(".hide-text").removeClass("dont-know");
    $(".fill-answer-value").css("display", "none");
    $(".question_submit").css("background-color", "#2f9892");

    if (question_start_point != -1) {
        var opt = getOptionsFromForm();
        opt['current_page'] = question_start_point;
        $("#Pagination").pagination(members.length, opt);
    }
}

function highlight_border_green() {

    console.log('highlight_border_green called');

    // $('#answer').removeClass("highlight-border-image");
    $('#answer').addClass("highlight-border-green");

    // call after .8 sec
    //setTimeout(function () {
    //    $('#answer').removeClass("highlight-border-green");
    //   $('#answer').addClass("highlight-border-image");

    // }, 800);
}
$(document).ready(function () {
    /* focus answer element in 500ms */
    setTimeout(function () {
        $("#answer").focus();
    }, 500);

    /* keyup event when hit <enter> keyboard */
    $(document).keyup(function (event) {
        if (event.keyCode == 13) {
            if ($("#mastered-modal").css("display") == "block") {
                mastered_modal_close();
            } else if ($("#require-modal").css("display") == "block") {
                close_requieModal();
            } else {
                question_submit();
                setTimeout(function () {
                    $("#answer").focus();
                }, 500);
            }
        }
    });

});

/* trigger to modal close event */
function mastered_modal_close() {
    $("#mastered-modal").find("button").click();
}

function pageselectCallback(page_index, jq) {
    // Item per page must 1.
    var items_per_page = 1;

    var max_elem = Math.min((page_index + 1) * items_per_page, members.length);
    var newcontent = '';

    // Iterate through a selection of the content and build an HTML string
    for (var i = page_index * items_per_page; i < max_elem; i++) {
        newcontent += '<input type="hidden" id="question_id" value="' + members[i][0] + '">';
        newcontent += '<div class="point-row" id="question_point">' + numeral(total_points).format('0,0') + '</div>';
        if (members[i][3] != "") {
            if (toggled) {
                var list_num = playlist.push((window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + members[i][3]);
                if (list_num == 1) {
                    play();
                }
            }
            if (members[i][5] == 1) {
                newcontent += '<div class="question-row" id="question_play">';
                newcontent += '<input type="hidden" id="question_audio" value="' + members[i][3] + '">';
                newcontent += '<a onclick="play_words(1)" class="icon-audio" onmouseover="row_mouseover(this)" onmouseout="row_mouseout(this)"></a>';
                newcontent += '</div>';
            }
        }
        if (members[i][2] != "") {
            newcontent += '<div class="question-img" id="question_image">';
            if (members[i][4] == 1) {
                newcontent += '<img class="question-attach" src="' + (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + members[i][2] + '" draggable="false" alt="">';
            } else {
                newcontent += '<img class="question-attach" src="' + (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + 'assets/images/gs-answer-logo.png" draggable="false" alt="">';
            }
            newcontent += '</div>';
        }
        newcontent += '<div class="lead-small question-row" id="question_title" style="' + form_options + '">' + members[i][1] + '</div>';
        newcontent += '<div class="fill-answer-value mb-2" ';
        if (post_test == -1 || post_test >= members.length) {
            if (mastery_type == "memorization") {
                if (members[i][9] == 0) {
                    newcontent += 'style="display: inline-block;"';
                }
            }
        }
        newcontent += '><strong>' + members[i][6] + '</strong></div>';
        newcontent += '<div class="mt10">';
        newcontent += '<input type="hidden" id="answer_value" value="' + members[i][6] + '" class="textbox">';
        newcontent += '<div class="text-center">';
        newcontent += '<input type="text" id="answer" value="" class="textbox textbox-format form-control" style="display: inline-block" onkeyup="format_answer(this,event)" onmouseover="row_mouseover(this)" onmouseout="row_mouseout(this)">';
        newcontent += '</div>';
        newcontent += '</div>';
        if (members[i][7] != "") {
            newcontent += '<div class="mt15">';
            //newcontent +='<span class="note-text">why is this answer correct?</span>';
            newcontent += '<input type="hidden" id="correct-answer-notes" value="' + members[i][7] + '">';
            //newcontent +='<a onclick="play_words(0)" class="icon-playbig" onmouseover="row_mouseover(this)" onmouseout="row_mouseout(this)"></a>';
            newcontent += '</div>';
        }
        newcontent += '<div class="mb-2 pos-rel text-center" style="margin-top: 12px;">';
        newcontent += '<div class="help-link btn-help-submit ml-1 btn btn-success" style="position: absolute; left: 0; padding-left: 13px; padding-right: 13px;" onclick="question_submit(&#39;help&#39;)" onmouseover="row_mouseover(this)" onmouseout="row_mouseout(this)">?</div>';
        newcontent += '<button class="question_submit btn-answer-submit btn btn-success px-3" onclick="question_submit()" onmouseover="row_mouseover(this)" onmouseout="row_mouseout(this)">';
        newcontent += '<span id="submit-text">submit</span><span class="icon-next"></span></button>';
        newcontent += '<a class="toggle-sound ' + (toggled ? 'toggled' : '') + '" onclick="toggle_sound()" onmouseover="row_mouseover(this)" onmouseout="row_mouseout(this)" style="display: none;"></a>';
        if (window.location.href.indexOf('test-question-in-game') != -1) {
            newcontent += '<div class="question_submit">';
            newcontent += '<a id="zoom-plus" class="zoom-plus" onmouseover="row_mouseover(this)" onmouseout="row_mouseout(this)" onclick="zoom_question(&#39;+&#39;)">+</a>';
            newcontent += '<a id="zoom-minus" class="zoom-minus" onmouseover="row_mouseover(this)" onmouseout="row_mouseout(this)" onclick="zoom_question(&#39;-&#39;)">-</a>';
            newcontent += '</div>';
        }
        newcontent += '</div>';
    }

    // Replace old content with new content
    $('#question_form').html(newcontent);

    // Prevent click eventpropagation
    return false;
}

// The form contains fields for many pagiantion optiosn so you can
// quickly see the resuluts of the different options.            
function getOptionsFromForm() {
    var opt = {callback: pageselectCallback};
    // Set pagination config
    opt['items_per_page'] = 1;
    opt['next_text'] = "Next";
    opt['num_display_entries'] = 5;
    opt['num_edge_entries'] = 2;
    opt['prev_text'] = "Prev";
    // Avoid html injections in this demo
    var htmlspecialchars = {"&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;"}
    $.each(htmlspecialchars, function (k, v) {
        opt.prev_text = opt.prev_text.replace(k, v);
        opt.next_text = opt.next_text.replace(k, v);
    })
    return opt;
}


$(document).ready(function () {

    player = document.getElementById("audio");

    /* playing end event */
    player.addEventListener('ended', function (e) {
        playlist.splice(0, 1);
        if (playlist.length != 0) {
            play();
        } else {
            if (!submitFlag) {
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
        }
    });

    /* check playFlag value if this value is true, hide question form. if false, enable pagination */
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

var player;
var playlist = [];

/* check if play is possible  */
function play_words(flag) {
    var text = "";
    if (flag == 0) {
        if (toggled && $('#correct-answer-notes').length > 0) {
            text = $("#correct-answer-notes").val();
        }
        if (text != "") {
            playlist = [(window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + text];
            play();
        }
    } else if (flag == 1) {
        text = $("#question_audio").val();
        if (text != "") {
            var list_num = playlist.push((window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + text);
            if (list_num == 1) {
                play();
            }
        }
    }

}

/* player play */
function play() {
    player.src = playlist[0];
    player.load();
    player.play();
}

/* mouse move event */
function row_mouseover(object) {
    $(object).addClass("mouseovered");
}

/* mouse move out event */
function row_mouseout(object) {
    $(object).removeClass("mouseovered");
}

var caret = null;

/* I don't think this function is used */
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
    if ($(".mouseovered").attr("id") == "answer") {
        if (caret == null) {
            caret = setInterval(function () {
                caretTimer();
            }, 500);
        }
    } else {
        if (caret != null) {
            clearInterval(caret);
            caret = null;
        }
        if (!caretFlag) {
            caretFlag = true;
            var answer = $("#answer").val();
            if (typeof answer != "undefined") {
                if (answer.substr(answer.length - 1, 1) == "|") {
                    $("#answer").val(answer.substr(0, answer.length - 1));
                }
            }
        }
    }
}

var caretFlag = true;

/* I don't think this function is used */
function caretTimer() {
    if (caretFlag) {
        caretFlag = false;
        var answer = $("#answer").val();
        $("#answer").val(answer + "|");
    } else {
        caretFlag = true;
        var answer = $("#answer").val();
        if (typeof answer != "undefined") {
            if (answer.substr(answer.length - 1, 1) == "|") {
                $("#answer").val(answer.substr(0, answer.length - 1));
            }
        }
    }
}


/* I don't think this function is used */
function input_answer(answer) {
    if (caret != null) {
        $("#answer").val(answer);
    }
}

/* save audio to user event */
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
    $.ajax({
        url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-updateUser.php?action=audio",
        dataType: "json",
        type: "POST",
        data: {audio: audio},
        success: function (data) {
            if (data.result == "success") {
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}

/* save zoom to user event (zoom +/- by option)*/
function zoom_question(option) {
    var flag = false;
    if (option == "+") {
        if (zoom > 0) {
            zoom--;
            flag = true;
        }
    } else {
        if (zoom < 3) {
            zoom++;
            flag = true;
        }
    }
    if (flag) {
        $.ajax({
            url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-updateUser.php?action=zoom",
            dataType: "json",
            type: "POST",
            data: {zoom: zoom},
            success: function (data) {
                if (data.result == "success") {

                } else if (data.result == "expire") {
                    $('.expire-modal-wrapper').bPopup();
                }
            }
        });
        return "zoom:::" + shrink[zoom];
    }
}

/* I don't think this function is used */
function get_submitFlag() {
    if (submitFlag) {
        return "true";
    } else {
        return "false";
    }
}

var submitFlag = true;

/* save test/question/answer insert or update test */
function question_submit(action) {

    if (typeof(action) === 'undefined') action = '';

    if (submitFlag) {

        submitFlag = false;

        var flag = true;

        var current_question = $("#Pagination .current").text();
        current_question = current_question.replace("Prev", "");
        current_question = current_question.replace("Next", "");
        current_question = parseInt(current_question) - 1;

        var answer = $("#answer").val();
        if (!caretFlag && typeof answer != "undefined") {
            if (answer.substr(answer.length - 1, 1) == "|") {
                answer = answer.substr(0, answer.length - 1);
            }
        }
        answer = answer.trim();

        var answer_value = $("#answer_value").val().trim();

        if (action == '' && answer == "") {
            $("#require_message").text("Please input your answer.");
            $(".require-modal-wrapper").bPopup();
            flag = false;
            submitFlag = true;
        }

        if (flag) {
            flag = false;
            var return_value = "submit";

            if (action == 'help') {
                answer = "dont-know";
            } else if (answer.toLowerCase() == answer_value.toLowerCase()) {
                flag = true;
            }

            var k = Math.floor(Math.random() * compliments.length);
            if (k > compliments.length - 1) {
                k = compliments.length - 1;
            }

            if (mastery_type == "memorization") {

                if (post_test != -1 && post_test < members.length) {

                    var test_mode = "post_test";

                    // post test number
                    post_test++;
                    if (post_test >= members.length) {
                        question_start_point = 0;
                        // initialize question_history
                        question_history = question_history.map(history_map);
                        members = members.map(history_map);
                    } else {
                        question_start_point = post_test;
                    }

                    var type = test_mode;
                    $.ajax({
                        url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests.php?action=add",
                        dataType: "json",
                        type: "POST",
                        data: {type: type, test_id: test_id, post_test: post_test},
                        success: function (data) {
                            if (data.result == "success") {
                            } else if (data.result == "expire") {
                                $('.expire-modal-wrapper').bPopup();
                            }
                        }
                    });

                    if (flag) {

                        total_points++;
                        $(".point-row").text('+ 1');
                        setTimeout(function () {
                            $(".point-row").text(numeral(total_points).format('0,0'));
                        }, 1000);

                        var time = new Date();
                        correct_answered++;
                        test_result.push({
                            x: Date.UTC(time.getFullYear(), time.getMonth(), time.getDate(), time.getHours(), time.getMinutes(), time.getSeconds()),
                            y: correct_answered,
                            marker: {fillColor: 'rgb(8, 243, 53)'}
                        });

                        var type = "complete";
                        var completion_rate = 0;
                        var question_id = members[current_question][0];
                        $.ajax({
                            url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests.php?action=add",
                            dataType: "json",
                            type: "POST",
                            data: {
                                type: type,
                                test_id: test_id,
                                question_id: question_id,
                                completion_rate: completion_rate,
                                answer: answer,
                                test_mode: test_mode
                            },
                            success: function (data) {
                                if (data.result == "success") {
                                } else if (data.result == "expire") {
                                    $('.expire-modal-wrapper').bPopup();
                                }
                            }
                        });

                        //$("#question_title").html('<img src="'+compliments[k][0]+'">');
                        //$("#question_title").addClass("display-inline-blk");
                        $("#question_title").addClass("answer-correct");

                        if (typeof stopAlarm === 'function') {
                            stopAlarm();
                            if (timerObj != null) {
                                if (left_time + award_time > max_time) { // max 15:00
                                    left_time = max_time;
                                } else {
                                    left_time += award_time;
                                }
                            }
                        }

                    } else {

                        var time = new Date();
                        test_result.push({
                            x: Date.UTC(time.getFullYear(), time.getMonth(), time.getDate(), time.getHours(), time.getMinutes(), time.getSeconds()),
                            y: correct_answered,
                            marker: {fillColor: 'rgb(245, 64, 50)'}
                        });

                        var type = "not-complete";
                        var completion_rate = 0;
                        var question_id = members[current_question][0];
                        $.ajax({
                            url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests.php?action=add",
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
                            success: function (data) {
                                if (data.result == "success") {
                                } else if (data.result == "expire") {
                                    $('.expire-modal-wrapper').bPopup();
                                }
                            }
                        });

                        $(".hide-text").addClass("answer-incorrect");
                        if (action == 'help') {
                            $(".hide-text").addClass("dont-know");
                        } else {
                            total_points--;
                            $(".point-row").text(numeral(total_points).format('0,0'));
                            $(".point-row").css("color", "#F57B20");
                            setTimeout(function () {
                                $(".point-row").css("color", "#9CDC48");
                            }, 2000);
                        }
                        $(".question_submit").css("background-color", "#6faa24");

                    }

                    $("#question_title").css("color", "#9CDC48");
                    $("#question_image").html('<img class="question-attach" src="' + (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + members[current_question][2] + '" draggable="false" alt="">');
                    $(".fill-answer-value").css("display", "inline-block");
                    $(".help-link").css("display", "none");
                    $(".question_submit").css("display", "none");
                    $(".question_submit").parent(".mt15").css("padding-top", "38px");

                    total_answered++;

                    stop_report();
                    chart_option = 0;

                    play_words(0);
                    if (flag && toggled && compliments[k][1] != "") {
                        var list_num = playlist.push(compliments[k][1]);
                        if (list_num == 1) {
                            play();
                        }
                    }

                    if (playlist.length == 0) {
                        setTimeout(to_next_question,3000);
                    }

                } else {
                    // normal mastery test
                    var test_mode = "";
                    if (pre_test < members.length && members[current_question][9] == null) {
                        test_mode = "pre_test";
                        // pre_test number
                        pre_test++;

                        var type = test_mode;
                        $.ajax({
                            url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests.php?action=add",
                            dataType: "json",
                            type: "POST",
                            data: {type: type, test_id: test_id, pre_test: pre_test},
                            success: function (data) {
                                if (data.result == "success") {
                                } else if (data.result == "expire") {
                                    $('.expire-modal-wrapper').bPopup();
                                }
                            }
                        });

                    } else {
                        test_mode = "test_" + mode_number;
                    }

                    if (flag) {

                        total_points++;
                        $(".point-row").text('+ 1');
                        setTimeout(function () {
                            $(".point-row").text(numeral(total_points).format('0,0'));
                        }, 1000);

                        if (members[current_question][9] == null) {
                            members[current_question][8] = wait_time[1];
                            members[current_question][9] = 1;
                        } else {
                            for (var i = 0; i < mastery_number; i++) {
                                if (members[current_question][9] == i) {
                                    members[current_question][9] = i + 1;
                                    if (members[current_question][9] >= mastery_number) {
                                        members[current_question][8] = 'M';
                                        completion_num++;
                                    } else {
                                        members[current_question][8] = wait_time[i + 1];
                                    }
                                    break;
                                } else if (members[current_question][9] >= mastery_number) {
                                    members[current_question][8] = 'M';
                                    completion_num++;
                                    break;
                                }
                            }
                        }

                        var time = new Date();
                        correct_answered++;
                        test_result.push({
                            x: Date.UTC(time.getFullYear(), time.getMonth(), time.getDate(), time.getHours(), time.getMinutes(), time.getSeconds()),
                            y: correct_answered,
                            marker: {fillColor: 'rgb(8, 243, 53)'}
                        });

                        var completion_rate = Math.floor(100 * completion_num / members.length);
                        var type = "complete";
                        var question_id = members[current_question][0];
                        var new_mastered = 0;
                        if (mode_number == 0 && members[current_question][8] == 'M')
                            new_mastered = 1;
                        $.ajax({
                            url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests.php?action=add",
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
                            success: function (data) {
                                if (data.result == "success") {
                                } else if (data.result == "expire") {
                                    $('.expire-modal-wrapper').bPopup();
                                }
                            }
                        });

                        //$("#question_title").html('<img src="'+compliments[k][0]+'">');
                        //$("#question_title").addClass("display-inline-blk");
                        $("#question_title").addClass("answer-correct");

                        if (typeof stopAlarm === 'function') {
                            stopAlarm();
                            if (timerObj != null) {
                                if (left_time + award_time > max_time) {
                                    left_time = max_time;
                                } else {
                                    left_time += award_time;
                                }
                            }
                        }

                    } else {

                        members[current_question][8] = wait_time[0];
                        members[current_question][9] = 0;

                        var time = new Date();
                        test_result.push({
                            x: Date.UTC(time.getFullYear(), time.getMonth(), time.getDate(), time.getHours(), time.getMinutes(), time.getSeconds()),
                            y: correct_answered,
                            marker: {fillColor: 'rgb(245, 64, 50)'}
                        });

                        var type = "not-complete";
                        var completion_rate = Math.floor(100 * completion_num / members.length);
                        var question_id = members[current_question][0];
                        $.ajax({
                            url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests.php?action=add",
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
                            success: function (data) {
                                if (data.result == "success") {
                                } else if (data.result == "expire") {
                                    $('.expire-modal-wrapper').bPopup();
                                }
                            }
                        });

                        $(".hide-text").addClass("answer-incorrect");
                        if (action == 'help') {
                            $(".hide-text").addClass("dont-know");
                        } else {
                            total_points--;
                            $(".point-row").text(numeral(total_points).format('0,0'));
                            $(".point-row").css("color", "#F57B20");
                            setTimeout(function () {
                                $(".point-row").css("color", "#9CDC48");
                            }, 2000);
                        }
                        $(".question_submit").css("background-color", "#6faa24");

                    }

                    $("#question_title").css("color", "#9CDC48");
                    $("#question_image").html('<img class="question-attach" src="' + (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + members[current_question][2] + '" draggable="false" alt="">');
                    $(".fill-answer-value").css("display", "inline-block");
                    $(".help-link").css("display", "none");
                    $(".question_submit").css("display", "none");
                    $(".question_submit").parent(".mt15").css("padding-top", "38px");

                    // come down other question
                    var lowest = 0;
                    var lowest_i = -1;
                    var not_asked = '';
                    for (var i = 0; i < members.length; i++) {

                        if (members[i][8] != 'M' && members[i][8] != null) {
                            if (i != current_question) {
                                members[i][8]--;
                            }
                            if (members[i][8] <= 0) {
                                if (lowest_i == -1) {
                                    lowest = members[i][8];
                                    lowest_i = i;
                                } else {
                                    if (members[i][8] < lowest) {
                                        lowest = members[i][8];
                                        lowest_i = i;
                                    } else if (members[i][8] == lowest) {
                                        if (members[lowest_i][9] > members[i][9]) {
                                            lowest_i = i;
                                        }
                                    }
                                }
                            }
                        } else if (members[i][8] == null) {
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
                                if (members[i][9] < mastery_number) {
                                    if (correct_i == -1) {
                                        lowest_correct = members[i][9];
                                        correct_i = i;
                                    } else {
                                        if (members[i][9] < lowest_correct) {
                                            lowest_correct = members[i][9];
                                            correct_i = i;
                                        } else if (members[i][9] == lowest_correct) {
                                            if (members[i][8] < members[correct_i][8]) {
                                                correct_i = i;
                                            }
                                        }
                                    }
                                }
                            }
                            // check Mastered or not
                            if (correct_i == -1) {
                                // Regular Test Mastered
                                completion_num = 0;
                                mode_number++;

                                if (post_test == -1) {
                                    // go to post_test
                                    post_test = 0;
                                    question_start_point = 0;
                                    var type = "post_test";
                                    $.ajax({
                                        url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests.php?action=add",
                                        dataType: "json",
                                        type: "POST",
                                        data: {type: type, test_id: test_id, post_test: post_test},
                                        success: function (data) {
                                            if (data.result == "success") {
                                            } else if (data.result == "expire") {
                                                $('.expire-modal-wrapper').bPopup();
                                            }
                                        }
                                    });
                                    var type = "mode_update";
                                    $.ajax({
                                        url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests.php?action=add",
                                        dataType: "json",
                                        type: "POST",
                                        data: {type: type, test_id: test_id, mode_number: mode_number},
                                        success: function (data) {
                                            if (data.result == "success") {
                                            } else if (data.result == "expire") {
                                                $('.expire-modal-wrapper').bPopup();
                                            }
                                        }
                                    });
                                } else {
                                    // disable pagination
                                    question_start_point = -1;
                                    var type = "mastered";
                                    $.ajax({
                                        url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests.php?action=add",
                                        dataType: "json",
                                        type: "POST",
                                        data: {type: type, test_id: test_id, mode_number: mode_number},
                                        success: function (data) {
                                            if (data.result == "success") {
                                                //mastered_report();
                                                //chart_option = 0;
                                                if (data.next_test == "") {
                                                    var margin_top = $("#question_form").height() - 247;
                                                    if (margin_top < 0) {
                                                        margin_top = 0;
                                                    }
                                                    $("#question_form").html('<div class="lead-small question-row test-mastered display-inline-blk" id="question_title"></div><div class="mt10"><img src="' + (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + 'assets/images/que-finished-graphic.png" class="mb20"></div><div style="margin-top: ' + margin_top + 'px;"></div>');
                                                } else {
                                                    var margin_top = $("#question_form").height() - 405;
                                                    if (margin_top < 0) {
                                                        margin_top = 0;
                                                    }
                                                    $("#question_form").html('<div class="lead-small question-row test-mastered display-inline-blk" id="question_title"></div><div class="mt10"><img src="' + (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + 'assets/images/mastered-lightbulb.png" class="mb20"></div><div style="margin-top: ' + margin_top + 'px;"><button class="btn-primary question_submit" onclick="next_que(' + data.next_test + ')" onmouseover="row_mouseover(this)" onmouseout="row_mouseout(this)"><span id="submit-text">next test</span><span class="icon-next"></span></button></div>');
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

                    total_answered++;

                    stop_report();
                    chart_option = 0;

                    play_words(0);
                    if (flag && toggled && compliments[k][1] != "") {
                        var list_num = playlist.push(compliments[k][1]);
                        if (list_num == 1) {
                            play();
                        }
                    }

                    if (playlist.length == 0) {
                        setTimeout(to_next_question,3000);
                    }

                }

            } else if (mastery_type == "problem solving") {

                if (post_test != -1 && post_test < members.length) {

                    var test_mode = "post_test";

                    // post test number
                    post_test++;
                    if (post_test >= members.length) {
                        question_start_point = 0;
                        // initialize question_history
                        question_history = question_history.map(history_map);
                        members = members.map(history_map);
                    } else {
                        question_start_point = post_test;
                    }

                    var type = test_mode;
                    $.ajax({
                        url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests.php?action=add",
                        dataType: "json",
                        type: "POST",
                        data: {type: type, test_id: test_id, post_test: post_test},
                        success: function (data) {
                            if (data.result == "success") {
                            } else if (data.result == "expire") {
                                $('.expire-modal-wrapper').bPopup();
                            }
                        }
                    });

                    if (flag) {

                        total_points++;
                        $(".point-row").text('+ 1');
                        setTimeout(function () {
                            $(".point-row").text(numeral(total_points).format('0,0'));
                        }, 1000);

                        var time = new Date();
                        correct_answered++;
                        test_result.push({
                            x: Date.UTC(time.getFullYear(), time.getMonth(), time.getDate(), time.getHours(), time.getMinutes(), time.getSeconds()),
                            y: correct_answered,
                            marker: {fillColor: 'rgb(8, 243, 53)'}
                        });

                        var type = "complete";
                        var completion_rate = 0;
                        var question_id = members[current_question][0];
                        $.ajax({
                            url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests.php?action=add",
                            dataType: "json",
                            type: "POST",
                            data: {
                                type: type,
                                test_id: test_id,
                                question_id: question_id,
                                completion_rate: completion_rate,
                                answer: answer,
                                test_mode: test_mode
                            },
                            success: function (data) {
                                if (data.result == "success") {
                                } else if (data.result == "expire") {
                                    $('.expire-modal-wrapper').bPopup();
                                }
                            }
                        });

                        //$("#question_title").html('<img src="'+compliments[k][0]+'">');
                        //$("#question_title").addClass("display-inline-blk");
                        $("#question_title").addClass("answer-correct");

                        if (typeof stopAlarm === 'function') {
                            stopAlarm();
                            if (timerObj != null) {
                                if (left_time + award_time > max_time) {
                                    left_time = max_time;
                                } else {
                                    left_time += award_time;
                                }
                            }
                        }

                    } else {

                        var time = new Date();
                        test_result.push({
                            x: Date.UTC(time.getFullYear(), time.getMonth(), time.getDate(), time.getHours(), time.getMinutes(), time.getSeconds()),
                            y: correct_answered,
                            marker: {fillColor: 'rgb(245, 64, 50)'}
                        });

                        var type = "not-complete";
                        var completion_rate = 0;
                        var question_id = members[current_question][0];
                        $.ajax({
                            url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests.php?action=add",
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
                            success: function (data) {
                                if (data.result == "success") {
                                } else if (data.result == "expire") {
                                    $('.expire-modal-wrapper').bPopup();
                                }
                            }
                        });

                        $(".hide-text").addClass("answer-incorrect");
                        if (action == 'help') {
                            $(".hide-text").addClass("dont-know");
                        } else {
                            total_points--;
                            $(".point-row").text(numeral(total_points).format('0,0'));
                            $(".point-row").css("color", "#F57B20");
                            setTimeout(function () {
                                $(".point-row").css("color", "#9CDC48");
                            }, 2000);
                        }
                        $(".question_submit").css("background-color", "#6faa24");

                    }

                    $("#question_title").css("color", "#9CDC48");
                    $("#question_image").html('<img class="question-attach" src="' + (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + members[current_question][2] + '" draggable="false" alt="">');
                    $(".fill-answer-value").css("display", "inline-block");
                    $(".help-link").css("display", "none");
                    $(".question_submit").css("display", "none");
                    $(".question_submit").parent(".mt15").css("padding-top", "38px");

                    total_answered++;

                    stop_report();
                    chart_option = 0;

                    play_words(0);
                    if (flag && toggled && compliments[k][1] != "") {
                        var list_num = playlist.push(compliments[k][1]);
                        if (list_num == 1) {
                            play();
                        }
                    }

                    if (playlist.length == 0) {
                        setTimeout(to_next_question,3000);
                    }

                } else {

                    // normal mastery test
                    var test_mode = "";
                    if (pre_test < members.length && members[current_question][9] == null) {
                        test_mode = "pre_test";
                        // pre_test number
                        pre_test++;

                        var type = test_mode;
                        $.ajax({
                            url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests.php?action=add",
                            dataType: "json",
                            type: "POST",
                            data: {type: type, test_id: test_id, pre_test: pre_test},
                            success: function (data) {
                                if (data.result == "success") {
                                } else if (data.result == "expire") {
                                    $('.expire-modal-wrapper').bPopup();
                                }
                            }
                        });

                    } else {
                        test_mode = "test_" + mode_number;
                    }

                    if (flag) {

                        total_points++;
                        $(".point-row").text('+ 1');
                        setTimeout(function () {
                            $(".point-row").text(numeral(total_points).format('0,0'));
                        }, 1000);

                        members[current_question][8] = wait_time;
                        members[current_question][9] = 1;

                        completion_num++;
                        var time = new Date();
                        correct_answered++;
                        test_result.push({
                            x: Date.UTC(time.getFullYear(), time.getMonth(), time.getDate(), time.getHours(), time.getMinutes(), time.getSeconds()),
                            y: correct_answered,
                            marker: {fillColor: 'rgb(8, 243, 53)'}
                        });

                        var completion_rate = Math.floor(100 * completion_num / mastery_number);
                        var type = "complete";
                        var question_id = members[current_question][0];
                        $.ajax({
                            url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests.php?action=add",
                            dataType: "json",
                            type: "POST",
                            data: {
                                type: type,
                                test_id: test_id,
                                question_id: question_id,
                                completion_rate: completion_rate,
                                answer: answer,
                                test_mode: test_mode
                            },
                            success: function (data) {
                                if (data.result == "success") {
                                } else if (data.result == "expire") {
                                    $('.expire-modal-wrapper').bPopup();
                                }
                            }
                        });

                        //$("#question_title").html('<img src="'+compliments[k][0]+'">');
                        //$("#question_title").addClass("display-inline-blk");
                        $("#question_title").addClass("answer-correct");

                        if (typeof stopAlarm === 'function') {
                            stopAlarm();
                            if (timerObj != null) {
                                if (left_time + award_time > max_time) {
                                    left_time = max_time;
                                } else {
                                    left_time += award_time;
                                }
                            }
                        }

                    } else {

                        members[current_question][8] = wait_time;
                        members[current_question][9] = 0;

                        completion_num = 0;

                        var time = new Date();
                        test_result.push({
                            x: Date.UTC(time.getFullYear(), time.getMonth(), time.getDate(), time.getHours(), time.getMinutes(), time.getSeconds()),
                            y: correct_answered,
                            marker: {fillColor: 'rgb(245, 64, 50)'}
                        });

                        var type = "not-complete";
                        var completion_rate = Math.floor(100 * completion_num / mastery_number);
                        var question_id = members[current_question][0];
                        $.ajax({
                            url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests.php?action=add",
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
                            success: function (data) {
                                if (data.result == "success") {
                                } else if (data.result == "expire") {
                                    $('.expire-modal-wrapper').bPopup();
                                }
                            }
                        });

                        $(".hide-text").addClass("answer-incorrect");
                        if (action == 'help') {
                            $(".hide-text").addClass("dont-know");
                        } else {
                            total_points--;
                            $(".point-row").text(numeral(total_points).format('0,0'));
                            $(".point-row").css("color", "#F57B20");
                            setTimeout(function () {
                                $(".point-row").css("color", "#9CDC48");
                            }, 2000);
                        }
                        $(".question_submit").css("background-color", "#6faa24");

                    }

                    $("#question_title").css("color", "#9CDC48");
                    $("#question_image").html('<img class="question-attach" src="' + (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + members[current_question][2] + '" draggable="false" alt="">');
                    $(".fill-answer-value").css("display", "inline-block");
                    $(".help-link").css("display", "none");
                    $(".question_submit").css("display", "none");
                    $(".question_submit").parent(".mt15").css("padding-top", "38px");

                    if (completion_num >= mastery_number && pre_test >= members.length) {
                        // Regular Test Mastered
                        completion_num = 0;
                        mode_number++;

                        if (post_test == -1) {
                            // go to post_test
                            post_test = 0;
                            question_start_point = 0;
                            var type = "post_test";
                            $.ajax({
                                url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests.php?action=add",
                                dataType: "json",
                                type: "POST",
                                data: {type: type, test_id: test_id, post_test: post_test},
                                success: function (data) {
                                    if (data.result == "success") {
                                    } else if (data.result == "expire") {
                                        $('.expire-modal-wrapper').bPopup();
                                    }
                                }
                            });
                            var type = "mode_update";
                            $.ajax({
                                url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests.php?action=add",
                                dataType: "json",
                                type: "POST",
                                data: {type: type, test_id: test_id, mode_number: mode_number},
                                success: function (data) {
                                    if (data.result == "success") {
                                    } else if (data.result == "expire") {
                                        $('.expire-modal-wrapper').bPopup();
                                    }
                                }
                            });
                        } else {
                            // disable pagination
                            question_start_point = -1;
                            var type = "mastered";
                            $.ajax({
                                url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests.php?action=add",
                                dataType: "json",
                                type: "POST",
                                data: {type: type, test_id: test_id, mode_number: mode_number},
                                success: function (data) {
                                    if (data.result == "success") {
                                        //mastered_report();
                                        //chart_option = 0;
                                        if (data.next_test == "") {
                                            var margin_top = $("#question_form").height() - 247;
                                            if (margin_top < 0) {
                                                margin_top = 0;
                                            }
                                            $("#question_form").html('<div class="lead-small question-row test-mastered display-inline-blk" id="question_title"></div><div class="mt10"><img src="' + (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + 'assets/images/que-finished-graphic.png" class="mb20"></div><div style="margin-top: ' + margin_top + 'px;"></div>');
                                        } else {
                                            var margin_top = $("#question_form").height() - 405;
                                            if (margin_top < 0) {
                                                margin_top = 0;
                                            }
                                            $("#question_form").html('<div class="lead-small question-row test-mastered display-inline-blk" id="question_title"></div><div class="mt10"><img src="' + (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + 'assets/images/mastered-lightbulb.png" class="mb20"></div><div style="margin-top: ' + margin_top + 'px;"><button class="btn-primary question_submit" onclick="next_que(' + data.next_test + ')" onmouseover="row_mouseover(this)" onmouseout="row_mouseout(this)"><span id="submit-text">next test</span><span class="icon-next"></span></button></div>');
                                        }

                                    } else if (data.result == "expire") {
                                        $('.expire-modal-wrapper').bPopup();
                                    }
                                }
                            });

                        }

                    } else {

                        if (completion_num >= mastery_number) {
                            completion_num = 0;
                        }

                        // generate random number
                        var random_i = Math.floor(Math.random() * members.length);
                        var select_i = -1;
                        // come down other question
                        for (var i = 0; i < members.length; i++) {
                            if (i != current_question) {
                                if (members[i][8] != null) {
                                    members[i][8]--;
                                    if (members[i][8] == 0) {
                                        members[i][8] = null;
                                        if (random_i == 0) {
                                            select_i = i;
                                        }
                                        random_i--;
                                    }
                                } else {
                                    if (random_i == 0) {
                                        select_i = i;
                                    }
                                    random_i--;
                                }
                            }
                        }
                        // find next question
                        if (select_i == -1) {

                            for (var i = members.length - 1; i >= 0; i--) {
                                if (members[i][8] == null) {
                                    if (random_i == 0) {
                                        select_i = i;
                                        break;
                                    }
                                    random_i--;
                                }
                            }

                            question_start_point = select_i;
                        } else {
                            question_start_point = select_i;
                        }

                    }

                    $(".msg-complete").text(Math.round(100 * completion_num / mastery_number).toString() + "%");

                    total_answered++;

                    stop_report();
                    chart_option = 0;

                    play_words(0);
                    if (flag && toggled && compliments[k][1] != "") {
                        var list_num = playlist.push(compliments[k][1]);
                        if (list_num == 1) {
                            play();
                        }
                    }

                    if (playlist.length == 0) {
                        setTimeout(to_next_question,3000);
                    }

                }

            }

            // update current state
            var type = "current_state";
            var insert_start_point = question_start_point;
            if (question_start_point == -1) {
                insert_start_point = 0;
            }

            for (var i = 0; i < members.length; i++) {
                question_history[i][0] = members[i][8];
                question_history[i][1] = members[i][9];
            }

            $.ajax({
                url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests.php?action=add",
                dataType: "json",
                type: "POST",
                data: {
                    type: type,
                    test_id: test_id,
                    insert_start_point: insert_start_point,
                    question_history: question_history
                },
                success: function (data) {
                    if (data.result == "success") {
                    } else if (data.result == "expire") {
                        $('.expire-modal-wrapper').bPopup();
                    }
                }
            });

            return_value += ":::" + (flag ? "true" : "false") + ":::" + action;

            return return_value;

        }

    }

}

/* close require modal Popup */
function close_requieModal() {
    $(".require-modal-wrapper").bPopup().close();
}

/* retake test but I don't think this function used. */
function retake_test(test_id, link) {
    var type = "retake_test";
    $.ajax({
        url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-tests.php?action=add",
        dataType: "json",
        type: "POST",
        data: {type: type, test_id: test_id},
        success: function (data) {
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

    $.ajax({
        url: (window.location.href.indexOf('test-question-in-game') != -1 ? '../' : '/') + "ajax/async-studying.php?action=stop",
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
        success: function (data) {
        }
    });

}

/* I don't think this function is used */
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
        success: function (data) {
        }
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
                'Click and drag in the plot area to zoom in' :
                'Pinch the chart to zoom in'
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
            formatter: function () {
                return '<b>' + this.series.name + '</b><br/>' +
                    this.point.y + ' ' + this.point.name.toLowerCase();
            }
        }
    });
}

function to_next_question()
{
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
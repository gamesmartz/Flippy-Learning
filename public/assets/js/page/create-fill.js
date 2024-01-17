function pageselectCallback(page_index, jq) {
    // Item per page must 1.
    var items_per_page = 1;

    var max_elem = Math.min((page_index + 1) * items_per_page, members.length);
    var newcontent = '';

    // Iterate through a selection of the content and build an HTML string
    for (var i = page_index * items_per_page; i < max_elem; i++) {
        newcontent += '<div style="min-width: 450px;">';
        newcontent += '<input type="hidden" id="question_id" value="' + members[i][0] + '">';
        newcontent += '<p class="lead question-hint mb-2">Question ' + (i + 1) + '</p>';
        newcontent += '<textarea name="question" id="question_title" placeholder="Question" class="textbox form-control" style="display: block; margin: 0px 15px 0px 0px; width: 290px; height: 100px;" onkeyup="format_answer(this,event)">' + members[i][1].replace("<br>", "\n") + '</textarea>';
        if (user_role == 2) {
            newcontent += '<div style="display: none;">';
        } else {
            newcontent += '<div class="">';
        }
        newcontent += '<div class="mb-2">';
        newcontent += '<button class="btn btn-success mt-3 mb-2" onclick="open_upload(this)" data-file="#attach_question" type="button">optional jpg</button>';
        newcontent += '</div>';
        newcontent += '<form class="input-file" id="attach_question_form" method="post" enctype="multipart/form-data" action="ajax/async-uploadImage.php" style="margin-bottom:0px;">';
        newcontent += '<input type="file" name="imageUpload" id="attach_question" class="input-file-graphics" onchange="upload_attach(0)">';
        newcontent += '<input type="hidden" name="uploadType" value="question">';
        newcontent += '<input type="hidden" name="attach_question_url" id="attach_question_url" value="' + members[i][2] + '">';
        newcontent += '<div class="clearboth"></div>';
        newcontent += '</form>';
        if (members[i][2] == "") {
            newcontent += '<div class="pull-left ml15" id="attach_question_img"></div>';
        } else {
            newcontent += '<div class="pull-left ml15 text-justify" id="attach_question_img">';
            newcontent += '<img src="' + members[i][2] + '" class="attach_images">';
            newcontent += '<div class="attach_trash white-trash btn btn-success" onclick="trash_attachment()"></div>';
            newcontent += '<div class="inline-block mt-2 d-flex align-items-center ">';
            newcontent += '<input type="checkbox" id="checked-show-image" name="checkboxes" ' + (members[i][4] == 1 ? 'checked' : '') + '>';
            newcontent += '<label for="checked-show-image"><span></span></label><span class="ml-2">Show Before Answer</span></div>';
            newcontent += '</div>';
        }

        newcontent += '<div class="clearfix">';

        if (members[i][3] == "") {
            newcontent += '<div class="inline-block ml15" id="question_audio_play"></div>';
        } else {
            newcontent += '<div class="inline-block ml15" id="question_audio_play">';
            newcontent += '<audio controls="controls"><source src="' + members[i][3] + '" type="audio/mp3" /></audio>';
            newcontent += '<div class="attach_trash white-trash btn btn-success" onclick="trash_audio(0)"></div>';
            newcontent += '<div class="inline-block ml15"><span class="label-show-button">Show<br>Button</span>';
            newcontent += '<input type="checkbox" id="checked-show-button" name="checkboxes" ' + (members[i][5] == 1 ? 'checked' : '') + '>';
            newcontent += '<label for="checked-show-button"><span></span></label></div>';
            newcontent += '</div>';
        }
        newcontent += '</div>';
        //newcontent +='<span class="test-icon-iota icon-iota ml15 tooltip_1" onmouseover="open_tooltip(this)" onmouseout="close_tooltip(this)" title="<strong>Optional Graphic</strong><br>A supporting graphic which sits below your question. Example:"></span>';
        newcontent += '</div>';

        newcontent += '<button class="btn btn-success btn-uploadAudio my-2" onclick="open_upload(this)" data-file="#question_audio" type="button"></button>';
        newcontent += '<form class="input-file" id="question_audio_form" method="post" enctype="multipart/form-data" action="ajax/async-uploadAudio.php" style="margin-bottom:0px;">';
        newcontent += '<input type="file" name="audioUpload" id="question_audio" class="input-file-audio" onchange="upload_audio(0)">';
        newcontent += '<input type="hidden" name="question_audio_src" id="question_audio_src" value="' + members[i][3] + '">';
        newcontent += '</form>';

        newcontent += '<hr>';

        newcontent += '<p class="lead question-hint mb-2">Answer</p>';
        newcontent += '<input type="text" name="answer" id="answer" placeholder="Answer" class="textbox form-control span200" value="' + members[i][6] + '" onkeyup="format_answer(this,event)">';
        newcontent += '<div class="sp25"></div>';
        if (user_role == 2) {
            newcontent += '<div class="clear" style="display: none;">';
        } else {
            newcontent += '<div class="clear">';
        }
        newcontent += '<div class="my-3 d-flex align-items-center">';
        newcontent += '<button class="btn btn-success btn-uploadAudio" onclick="open_upload(this)" data-file="#attach_audio" type="button"></button><span class="ml-2"> Correct Answer Audio</span>';
        newcontent += '<form class="input-file" id="attach_audio_form" method="post" enctype="multipart/form-data" action="ajax/async-uploadAudio.php" style="margin-bottom:0px;">';
        newcontent += '<input type="file" name="audioUpload" id="attach_audio" class="input-file-audio" onchange="upload_audio(1)">';
        newcontent += '<input type="hidden" name="attach_audio_src" id="attach_audio_src" value="' + members[i][7] + '">';
        newcontent += '</form>';
        newcontent += '</div>';
        if (members[i][7] == "") {
            newcontent += '<div class="inline-block ml15" id="attach_audio_play"></div>';
        } else {
            newcontent += '<div class="inline-block ml15" id="attach_audio_play"><audio controls="controls"><source src="' + members[i][7] + '" type="audio/mp3" /></audio><div class="attach_trash white-trash btn btn-success" onclick="trash_audio(1)"></div></div>';
        }
        newcontent += '</div>';
        newcontent += '<hr>';
        newcontent += '</div>';


        newcontent += '<script type="text/javascript">$(document).ready(function (){$(".tooltip_1").tooltipsy();});</script>';
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
    opt['num_display_entries'] = 3;
    opt['num_edge_entries'] = 1;
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

    // Create pagination element with options from form
    var optInit = getOptionsFromForm();
    $("#Pagination").pagination(members.length, optInit);

    // category dropdown
    $("#choose-grade-1").ddslick();
    $("#choose-subject-1").ddslick();

    $("#choose-award-time").ddslick();
    $("#choose-mastery-number").ddslick();
    $("#choose-font-size").ddslick();
    $("#choose-question-width").ddslick();

});

/* upload file image event */
function upload_attach(num) {

    if (typeof FileReader !== "undefined") {
        $('.input-file-graphics').each(function (index, element) {
            try {
                var object = document.getElementsByClassName('input-file-graphics').item(index);
                var file = object.files[0];
                //var file = document.getElementsByClassName('input-file-graphics').item(index).files[0];
                // check file size
                var size = parseInt(file.size / 1024);
                var type = file.type;

                if (typeof size !== 'NaN') {
                    if (size > 300 || (type !== 'image/jpeg' && type !== 'image/png')) {
                        //$(object).parents("form").reset();
                        if (num == index) {
                            $('.graphics-file-size-error').bPopup();
                            return;
                        }
                    }
                }
            }
            catch (e) {

            }
        });
    }

    if (num == 0) {
        $("#attach_question_form").ajaxForm({
            success: function (data) {
                if (data != "failed") {
                    $("#attach_question_url").val(data);
                    var content = '<img src="' + data + '" class="attach_images"><div class="attach_trash white-trash btn btn-success" onclick="trash_attachment()"></div>';
                    content += '<div class="inline-block"><span class="label-show-button">Show Before<br>Answer</span>';
                    content += '<input type="checkbox" id="checked-show-image" name="checkboxes" checked >';
                    content += '<label for="checked-show-image"><span></span></label></div>';
                    $("#attach_question_img").html(content);
                }
            }
        }).submit();
    }

}

/* update audio file event. */
function upload_audio(index) {

    if (index == 0) {
        if (typeof FileReader !== "undefined") {
            var object = document.getElementById('question_audio');
            var file = object.files[0];

            var type = file.type;

            if (type !== 'audio/mp3' && type !== 'audio/mpeg') {
                return;
            }
        }

        $("#question_audio_form").ajaxForm({
            success: function (data) {
                if (data != "failed") {
                    $("#question_audio_src").val(data);
                    var content = '<audio controls="controls"><source src="' + data + '" type="audio/mp3" /></audio><div class="attach_trash white-trash btn btn-success" onclick="trash_audio(0)"></div>';
                    content += '<div class="inline-block ml15"><span class="label-show-button">Show<br>Button</span>';
                    content += '<input type="checkbox" id="checked-show-button" name="checkboxes">'
                    content += '<label for="checked-show-button"><span></span></label></div>';
                    $("#question_audio_play").html(content);
                }
            }
        }).submit();
    } else if (index == 1) {
        if (typeof FileReader !== "undefined") {
            var object = document.getElementById('attach_audio');
            var file = object.files[0];

            var type = file.type;

            if (type !== 'audio/mp3' && type !== 'audio/mpeg') {
                return;
            }
        }

        $("#attach_audio_form").ajaxForm({
            success: function (data) {
                if (data != "failed") {
                    $("#attach_audio_src").val(data);
                    var content = '<audio controls="controls"><source src="' + data + '" type="audio/mp3" /></audio><div class="attach_trash white-trash btn btn-success" onclick="trash_audio(1)"></div>';
                    $("#attach_audio_play").html(content);
                }
            }
        }).submit();
    }

}

/* reset attachment input element as blank */
function trash_attachment() {
    $("#attach_question_url").val("");
    $("#attach_question_img").html("");
    document.getElementById("attach_question_form").reset();
}

/* reset audio input element as blank and question audio form */
function trash_audio(index) {
    if (index == 0) {
        $("#question_audio_src").val("");
        $("#question_audio_play").html("");
        document.getElementById("question_audio_form").reset();
    } else if (index == 1) {
        $("#attach_audio_src").val("");
        $("#attach_audio_play").html("");
        document.getElementById("attach_audio_form").reset();
    }
}

/* trigger upload file event */
function open_upload(object) {
    var file_el_id = $(object).data('file');
    $(file_el_id).click();
}

/* close require modal  */
function close_requieModal() {
    $(".require-modal-wrapper").bPopup().close();
}

/* close save Modal */
function close_saveModal() {
    $(".save-modal-wrapper").bPopup().close();
    //window.location.href = "personal-tests.php";
}

/** update mp3 file name to english and save to folder again with updated name if test has mp3 files,
 * but I don't think this function is used.
 */
function play_words() {
    var text = $("#correct-answer-notes").val();
    var test_id = $("#test_id").val();

    if (test_id == "") {
        $(".audio-modal-wrapper").bPopup();
    } else {
        if (text != "") {
            $.ajax({
                url: "ajax/async-translate.php",
                dataType: "json",
                type: "POST",
                data: {text: text, test_id: test_id},
                success: function (data) {
                    if (data.result == "success") {
                        $("#player").html('<audio controls="controls" autoplay="autoplay" style="display: none;"><source src="upload/audio/' + test_id + '/' + data.name + '.mp3" type="audio/mp3" /></audio>');
                    }
                }
            });
        }
    }
}

/* open tooltip event on table td element this function is used for most js */
function open_tooltip(object) {
    $($(object).parent('td').find('.tooltip-testinfo')).fadeIn(100);
}

/* close tooltip event on table td element, this function is used for most js */
function close_tooltip(object) {
    $($(object).parent('td').find('.tooltip-testinfo')).fadeOut(250);
}

var click_flag = true;

/* save fill type test by test id(insert or update) */
function save_test(action) {

    var flag = true;
    $(".grade-hint").removeClass("missing-element");
    $(".test-hint").removeClass("missing-element");
    $(".question-hint").removeClass("missing-element");
    $(".answer-hint").removeClass("missing-element");

    var test_id = $("#test_id").val();
    var subject_id = $("#choose-subject-1 .dd-selected-value").val();
    var test_name = $("#test_name").val();
    var mastery_type = $("#choose-mastery-type .dd-selected-value").val();
    if (mastery_type == "") {
        mastery_type = "memorization";
    }

    var mastery_number = $("#choose-mastery-number .dd-selected-value").val();
    mastery_number = '';

    var award_time = $("#choose-award-time .dd-selected-value").val();

    var font_size = $("#choose-font-size .dd-selected-value").val();
    if (font_size == "") {
        font_size = "1.2em";
    }

    var question_width = $("#choose-question-width .dd-selected-value").val();
    if (question_width == "") {
        question_width = "260";
    }

    var form_options = [];
    form_options[0] = font_size;
    form_options[1] = question_width;

    var optional_note = $("#optional_note").val();

    var question_number = $("#Pagination .current").text();
    question_number = question_number.replace("Prev", "");
    question_number = question_number.replace("Next", "");
    var question_id = $("#question_id").val();
    var question_title = $("#question_title").val();
    var attach_question = [];
    attach_question[0] = $("#attach_question_url").val();
    attach_question[1] = $("#question_audio_src").val();
    var show_button = [];
    show_button[0] = $("#checked-show-image").is(":checked") ? 1 : 0;
    show_button[1] = $("#checked-show-button").is(":checked") ? 1 : 0;
    var answer = $("#answer").val().trim();
    var correct_answer_notes = $("#attach_audio_src").val();

    /* check form action type */
    if (action == "save_next") {

        if (subject_id == "") {
            $(".grade-hint").addClass("missing-element");
            flag = false;
        }
        if (test_name == "") {
            $(".test-hint").addClass("missing-element");
            flag = false;
        }
        if (question_title == "" && attach_question[0] == "" && attach_question[1] == "") {
            $(".question-hint").addClass("missing-element");
            flag = false;
        }
        if (answer == "") {
            $(".answer-hint").addClass("missing-element");
            flag = false;
        }

        if (!flag) {
            $(".require-modal-wrapper").bPopup();
        }
    } else if (action == "save") {
        var save_index = 0;
        if (subject_id == "") {
            $(".grade-hint").addClass("missing-element");
            flag = false;
        }
        if (test_name == "") {
            $(".test-hint").addClass("missing-element");
            flag = false;
        }
        if (question_title == "" && attach_question[0] == "" && attach_question[1] == "") {
            $(".question-hint").addClass("missing-element");
            save_index++;
            flag = false;
        }
        if (answer == "") {
            $(".answer-hint").addClass("missing-element");
            save_index++;
            flag = false;
        }

        if (save_index == 0) {
            if (!flag) {
                $(".require-modal-wrapper").bPopup();
            }
        } else if (save_index == 2) {
            if (test_id == "") {
                $(".require-modal-wrapper").bPopup();
            } else {
                $(".grade-hint").removeClass("missing-element");
                $(".test-hint").removeClass("missing-element");
                $(".question-hint").removeClass("missing-element");
                $(".answer-hint").removeClass("missing-element");
                finish_test();
            }
        } else {
            $(".require-modal-wrapper").bPopup();
        }

    }
    /* check add test/question/answer form is valid */
    if (flag && click_flag) {
        click_flag = false;
        var type = "fill";
        $.ajax({
            url: "ajax/async-tests.php?action=add",
            dataType: "json",
            type: "POST",
            data: {
                type: type,
                test_id: test_id,
                subject_id: subject_id,
                test_name: test_name,
                mastery_type: mastery_type,
                mastery_number: mastery_number,
                award_time: award_time,
                form_options: form_options,
                optional_note: optional_note,
                question_number: question_number,
                question_id: question_id,
                question_title: question_title,
                attach_question: attach_question,
                show_button: show_button,
                answer: answer,
                correct_answer_notes: correct_answer_notes
            },
            success: function (data) {
                if (data.result == "success") {
                    $("#test_id").val(data.test_id);
                    var question = data.question;
                    members = [];
                    for (var i = 0; i < question.length; i++) {
                        members.push([question[i].question_id, question[i].question_title, question[i].attach_image, question[i].attach_audio, question[i].show_image, question[i].show_button, question[i].answer, question[i].correct_note]);
                    }
                    if (action == "save_next") {
                        members.push(['', '', '', '', '', '', '', '']);
                        // go to next page
                        var opt = getOptionsFromForm();
                        var current_page = $("#Pagination .current").text();
                        current_page = current_page.replace("Prev", "");
                        current_page = current_page.replace("Next", "");
                        current_page = parseInt(current_page);
                        opt['current_page'] = current_page;
                        // Re-create pagination content with new parameters
                        $("#Pagination").pagination(members.length, opt);
                    } else if (action == "save") {
                        var opt = getOptionsFromForm();
                        var current_page = $("#Pagination .current").text();
                        current_page = current_page.replace("Prev", "");
                        current_page = current_page.replace("Next", "");
                        current_page = parseInt(current_page);
                        opt['current_page'] = current_page - 1;
                        // Re-create pagination content with new parameters
                        $("#Pagination").pagination(members.length, opt);
                        finish_test();
                    }
                    click_flag = true;
                } else if (data.result == "expire") {
                    $('.expire-modal-wrapper').bPopup();
                }
            }
        });
    }

}

/* finish modal popup */
function finish_test() {
    $(".save-modal-wrapper").bPopup();
}

/* open options modal popup, first set values to popup elements first and show */
function open_options() {
    var mastery_type = $("#choose-mastery-type .dd-selected-value").val();
    if (mastery_type != "") {
        var number_content = '';
        var description_content = '';
        if (mastery_type == "memorization") {
            description_content = 'Memorization:<br>Number of times a question needs to be answered<br>correct in a row before the question is mastered:';
            var master_number_options = '';
            $.each(master_number_arrays, function(index, element) {
                master_number_options += '<option value="' + master_number_array_keys[index] + '">' + element + '</option>';
            });
            number_content = '<select name="mastery-number" id="choose-mastery-number" data-desc="' + mastery_number + '" class="mastery-number">' + master_number_options + '</select>';
        } else if (mastery_type == "problem solving") {
            description_content = 'Problem Solving:<br>How many problems of this subject,<br>need to solved correctly in a row<br>before the subject is mastered.';
            number_content = '<select name="mastery-number" id="choose-mastery-number" data-desc="' + problem_mastery + '" class="mastery-number"><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option></select>';
        }
        $("#mastery-description").html(description_content);
        $("#mastery-number-wrapper").html(number_content);
        $("#choose-mastery-number").ddslick();
        if (mastery_number != '') {
            $("#choose-mastery-number .dd-selected-value").val(mastery_number);
            $("#choose-mastery-number .dd-selected").text(mastery_number);
        } else {
            $("#choose-mastery-number .dd-selected-value").val("");
            $("#choose-mastery-number .dd-selected").text("Use Global");
        }
    }
    $('.option-modal-wrapper').bPopup();
}

/* close option modal and save options event */
function close_optionModal() {
    var mastery_type = $("#choose-mastery-type .dd-selected-value").val();
    if (mastery_type == "") {
        mastery_type = "memorization";
    }

    mastery_number = $("#choose-mastery-number .dd-selected-value").val();
    if (mastery_number == "") {
        if (mastery_type == "memorization") {
            /* since blank = global use, remove this line */
            // mastery_number = memorization_mastery;
        } else if (mastery_type == "problem solving") {
            mastery_number = problem_mastery;
        }
    }

    var award_time = $("#choose-award-time .dd-selected-value").val();
    if (award_time == "") {
        /* since blank = global use, remove this line */
        // award_time = "1:30";
    }

    var font_size = $("#choose-font-size .dd-selected-value").val();
    if (font_size == "") {
        font_size = "1.2em";
    }

    var question_width = $("#choose-question-width .dd-selected-value").val();
    if (question_width == "") {
        question_width = "260";
    }

    var form_options = [];
    form_options[0] = font_size;
    form_options[1] = question_width;

    var test_id = $("#test_id").val();

    /* check test id is not empty */
    if (test_id != "") {
        var type = "test_options";
        $.ajax({
            url: "ajax/async-tests.php?action=add",
            dataType: "json",
            type: "POST",
            data: {
                type: type,
                test_id: test_id,
                mastery_number: mastery_number,
                award_time: award_time,
                form_options: form_options
            },
            success: function (data) {
                if (data.result == "success") {

                } else if (data.result == "expire") {
                    $('.expire-modal-wrapper').bPopup();
                }
            }
        });
    }
    $('.option-modal-wrapper').bPopup().close();
}

/* close audio modal event */
function close_audioModal() {
    $('.audio-modal-wrapper').bPopup().close();
}

/* delete question popup */
function delete_question() {
    var question_id = $("#question_id").val();
    if (question_id != "") {
        $('.delete-question-modal').bPopup();
    }
}

/* delete question event after close popup */
function close_delModal() {
    $('.delete-question-modal').bPopup().close();
    var test_id = $("#test_id").val();
    var question_id = $("#question_id").val();
    var type = "question";
    $.ajax({
        url: "ajax/async-tests.php?action=delete",
        dataType: "json",
        type: "POST",
        data: {type: type, test_id: test_id, question_id: question_id},
        success: function (data) {
            if (data.result == "success") {
                var question_number = $("#Pagination .current").text();
                question_number = question_number.replace("Prev", "");
                question_number = question_number.replace("Next", "");
                question_number = parseInt(question_number) - 1;
                members.splice(question_number, 1);
                if (members.length == 0) {
                    members = [
                        ['', '', '', '', '', '', '', '']
                    ];
                }
                var optInit = getOptionsFromForm();
                optInit['current_page'] = question_number;
                $("#Pagination").pagination(members.length, optInit);
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}
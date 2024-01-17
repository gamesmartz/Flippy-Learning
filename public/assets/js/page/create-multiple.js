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
        newcontent += '<textarea name="question" id="question_title" placeholder="Question" class="textbox form-control" style="display: inline-block; max-width: 285px; height: 90px;" onkeyup="format_answer(this,event)">' + members[i][1].replace("<br>", "\n") + '</textarea>';
        if (user_role == 2) {
            newcontent += '<div style="display: none;">';
        } else {
            newcontent += '<div style="">';
        }
        newcontent += '<button class="btn btn-success mt-3 mb-2" onclick="open_upload(this)" data-file="#attach_question" type="button">optional jpg</button>';
        newcontent += '<form class="input-file form-group" id="attach_question_form" method="post" enctype="multipart/form-data" action="ajax/async-uploadImage.php" style="margin-bottom:0px;">';
        newcontent += '<input type="file" name="imageUpload" id="attach_question" class="input-file-graphics" onchange="upload_attach(0)">';
        newcontent += '<input type="hidden" name="uploadType" value="question">';
        newcontent += '<input type="hidden" name="attach_question_url" id="attach_question_url" value="' + members[i][2] + '">';
        newcontent += '</form>';
        if (members[i][2] == "") {
            newcontent += '<div class="ml15" id="attach_question_img"></div>';
        } else {
            newcontent += '<div class="ml15" id="attach_question_img">';
            newcontent += '<img src="' + members[i][2] + '" class="attach_images rounded">';
            newcontent += '<div class="attach_trash white-trash btn btn-success" onclick="trash_attachment(0)"></div>';
            newcontent += '<div class="show-before-answer d-flex align-items-center mt-2"><span class="label-show-button">Show Before Answer</span>';
            newcontent += '<input type="checkbox" id="checked-show-image" name="checkboxes" ' + (members[i][4] == 1 ? 'checked' : '') + '>';
            newcontent += '<label for="checked-show-image"><span></span></label></div>';
            newcontent += '</div>';
        }
        newcontent += '<div>';
        newcontent += '<button class="btn btn-success btn-uploadAudio my-2" onclick="open_upload(this)" data-file="#question_audio" type="button"></button>';
        newcontent += '<hr>';
        newcontent += '<form class="input-file" id="question_audio_form" method="post" enctype="multipart/form-data" action="ajax/async-uploadAudio.php" style="margin-bottom:0px;">';
        newcontent += '<input type="file" name="audioUpload" id="question_audio" class="input-file-audio" onchange="upload_audio(0)">';
        newcontent += '<input type="hidden" name="question_audio_src" id="question_audio_src" value="' + members[i][3] + '">';
        newcontent += '</form>';
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
        newcontent += '</div>';
        //newcontent +='<span class="test-icon-iota icon-iota ml15 tooltip_1" onmouseover="open_tooltip(this)" onmouseout="close_tooltip(this)" title="<strong>Optional Graphic</strong><br>A supporting graphic which sits below your question. Example:"></span>';
        newcontent += '<div class="clear sp25"></div>';
        newcontent += '<div class="gs-answers-correct">';
        newcontent += '<div class="d-flex align-items-center mb-3">';
        newcontent += '<lable>Correct Answer:</label><br class="mb-1">';
        newcontent += '<input type="text" name="answer" id="answer_text1" placeholder="Potential Answer 1" class="textbox form-control span200" value="' + members[i][6] + '" onkeyup="format_answer(this,event)">';
        if (members[i][7] == 1) {
            newcontent += '<div class="radio-button-div ml-3 extra-answer"><input type="radio" name="correct_answer" id="correct_answer1" class="ml15" checked><label for="correct_answer1"><span></span></label></div>';
        } else {
            newcontent += '<div class="radio-button-div ml-3 extra-answer"><input type="radio" name="correct_answer" id="correct_answer1" class="ml15" checked><label for="correct_answer1"><span></span></label></div>';
        }
        if (user_role == 2) {
            newcontent += '<div style="display: none;">';
        } else {
            newcontent += '</div>';
            newcontent += '<div class="mb-2">';
        }
        newcontent += '<span class="">or </span>';
        newcontent += '<button class="btn btn-success btn-file" onclick="open_upload(this)" data-file="#attach_graphic1" type="button">graphic</button>';
        newcontent += '<form class="input-file" id="attach_graphic1_form" method="post" enctype="multipart/form-data" action="ajax/async-uploadImage.php" style="margin-bottom:0px;">';
        newcontent += '<input type="file" name="imageUpload" id="attach_graphic1" class="input-file-graphics" onchange="upload_attach(1)">';
        newcontent += '<input type="hidden" name="uploadType" value="answer">';
        newcontent += '<input type="hidden" name="attach_graphic1_url" id="attach_graphic1_url" value="' + members[i][8] + '">';
        newcontent += '<div class="clearboth"></div>';
        newcontent += '</form>';
        if (members[i][8] == "") {
            newcontent += '<div class="answer_attachment ml15" id="attach_graphic1_img"></div>';
        } else {
            newcontent += '<div class="answer_attachment ml15" id="attach_graphic1_img"><img src="' + members[i][8] + '" class="attach_images"><div class="attach_trash white-trash btn btn-success" onclick="trash_attachment(1)"></div></div>';
        }
        newcontent += '</div>';
        newcontent += '</div>';
        newcontent += '<div class="d-flex align-items-center mb-3 extra-answer">';
        newcontent += '<input type="text" name="answer" id="answer_text2" placeholder="Potential Answer 2" class="textbox form-control span200" value="' + members[i][9] + '" onkeyup="format_answer(this,event)">';
        if (members[i][10] == 1) {
            newcontent += '<div class="radio-button-div ml-3"><input type="radio" name="correct_answer" id="correct_answer2" value="1" class="ml15" checked><label for="correct_answer2"><span></span></label></div>';
        } else {
            newcontent += '<div class="radio-button-div ml-3"><input type="radio" name="correct_answer" id="correct_answer2" value="1" class="ml15"><label for="correct_answer2"><span></span></label></div>';
        }
        if (user_role == 2) {
            newcontent += '<div style="display: none;">';
        } else {
            newcontent += '</div>';
            newcontent += '<div class="mb-2 extra-answer">';
        }
        newcontent += '<span class="">or </span>';
        newcontent += '<button class="btn btn-success btn-file" onclick="open_upload(this)" data-file="#attach_graphic2" type="button">graphic</button>';
        newcontent += '<form class="input-file" id="attach_graphic2_form" method="post" enctype="multipart/form-data" action="ajax/async-uploadImage.php" style="margin-bottom:0px;">';
        newcontent += '<input type="file" name="imageUpload" id="attach_graphic2" class="input-file-graphics" onchange="upload_attach(2)">';
        newcontent += '<input type="hidden" name="uploadType" value="answer">';
        newcontent += '<input type="hidden" name="attach_graphic2_url" id="attach_graphic2_url" value="' + members[i][11] + '">';
        newcontent += '<div class="clearboth"></div>';
        newcontent += '</form>';
        if (members[i][11] == "") {
            newcontent += '<div class="answer_attachment ml15" id="attach_graphic2_img"></div>';
        } else {
            newcontent += '<div class="answer_attachment ml15" id="attach_graphic2_img"><img src="' + members[i][11] + '" class="attach_images"><div class="attach_trash white-trash btn btn-success" onclick="trash_attachment(2)"></div></div>';
        }
        newcontent += '</div>';
        newcontent += '</div>';
        newcontent += '<div class="d-flex align-items-center mb-3 extra-answer">';
        newcontent += '<input type="text" name="answer" id="answer_text3" placeholder="Potential Answer 3" class="textbox form-control span200" value="' + members[i][12] + '" onkeyup="format_answer(this,event)">';
        if (members[i][13] == 1) {
            newcontent += '<div class="radio-button-div ml-3"><input type="radio" name="correct_answer" id="correct_answer3" value="1" class="ml15" checked><label for="correct_answer3"><span></span></label></div>';
        } else {
            newcontent += '<div class="radio-button-div ml-3"><input type="radio" name="correct_answer" id="correct_answer3" value="1" class="ml15"><label for="correct_answer3"><span></span></label></div>';
        }
        if (user_role == 2) {
            newcontent += '<div style="display: none;">';
        } else {
            newcontent += '</div>';
            newcontent += '<div class="mb-2 extra-answer">';
        }
        newcontent += '<span class="">or </span>';
        newcontent += '<button class="btn btn-success btn-file" onclick="open_upload(this)" data-file="#attach_graphic3" type="button">graphic</button>';
        newcontent += '<form class="input-file" id="attach_graphic3_form" method="post" enctype="multipart/form-data" action="ajax/async-uploadImage.php" style="margin-bottom:0px;">';
        newcontent += '<input type="file" name="imageUpload" id="attach_graphic3" class="input-file-graphics" onchange="upload_attach(3)">';
        newcontent += '<input type="hidden" name="uploadType" value="answer">';
        newcontent += '<input type="hidden" name="attach_graphic3_url" id="attach_graphic3_url" value="' + members[i][14] + '">';
        newcontent += '<div class="clearboth"></div>';
        newcontent += '</form>';
        if (members[i][14] == "") {
            newcontent += '<div class="answer_attachment ml15" id="attach_graphic3_img"></div>';
        } else {
            newcontent += '<div class="answer_attachment ml15" id="attach_graphic3_img"><img src="' + members[i][14] + '" class="attach_images"><div class="attach_trash white-trash btn btn-success" onclick="trash_attachment(3)"></div></div>';
        }
        newcontent += '</div>';
        newcontent += '</div>';
        newcontent += '<div class="d-flex align-items-center mb-3 extra-answer">';
        newcontent += '<input type="text" name="answer" id="answer_text4" placeholder="Potential Answer 4" class="textbox form-control span200" value="' + members[i][15] + '" onkeyup="format_answer(this,event)">';
        if (members[i][16] == 1) {
            newcontent += '<div class="radio-button-div ml-3"><input type="radio" name="correct_answer" id="correct_answer4" value="1" class="ml15" checked><label for="correct_answer4"><span></span></label></div>';
        } else {
            newcontent += '<div class="radio-button-div ml-3"><input type="radio" name="correct_answer" id="correct_answer4" value="1" class="ml15"><label for="correct_answer4"><span></span></label></div>';
        }
        if (user_role == 2) {
            newcontent += '<div style="display: none;">';
        } else {
            newcontent += '</div>';
            newcontent += '<div class="mb-2 extra-answer">';
        }
        newcontent += '<span class="">or </span>';
        newcontent += '<button class="btn btn-success btn-file" onclick="open_upload(this)" data-file="#attach_graphic4" type="button">graphic</button>';
        newcontent += '<form class="input-file" id="attach_graphic4_form" method="post" enctype="multipart/form-data" action="ajax/async-uploadImage.php" style="margin-bottom:0px;">';
        newcontent += '<input type="file" name="imageUpload" id="attach_graphic4" class="input-file-graphics" onchange="upload_attach(4)">';
        newcontent += '<input type="hidden" name="uploadType" value="answer">';
        newcontent += '<input type="hidden" name="attach_graphic4_url" id="attach_graphic4_url" value="' + members[i][17] + '">';
        newcontent += '<div class="clearboth"></div>';
        newcontent += '</form>';
        if (members[i][17] == "") {
            newcontent += '<div class="answer_attachment ml15" id="attach_graphic4_img"></div>';
        } else {
            newcontent += '<div class="answer_attachment ml15" id="attach_graphic4_img"><img src="' + members[i][17] + '" class="attach_images"><div class="attach_trash white-trash btn btn-success" onclick="trash_attachment(4)"></div></div>';
        }
        newcontent += '</div>';
        newcontent += '</div>';
        newcontent += '<div class="d-flex align-items-center mb-3 extra-answer">';
        newcontent += '<input type="text" name="answer" id="answer_text5" placeholder="Potential Answer 5" class="textbox form-control span200" value="' + members[i][18] + '" onkeyup="format_answer(this,event)">';
        if (members[i][19] == 1) {
            newcontent += '<div class="radio-button-div ml-3"><input type="radio" name="correct_answer" id="correct_answer5" value="1" class="ml15" checked><label for="correct_answer5"><span></span></label></div>';
        } else {
            newcontent += '<div class="radio-button-div ml-3"><input type="radio" name="correct_answer" id="correct_answer5" value="1" class="ml15"><label for="correct_answer5"><span></span></label></div>';
        }
        if (user_role == 2) {
            newcontent += '<div style="display: none;">';
        } else {
            newcontent += '</div>';
            newcontent += '<div class="mb-2 extra-answer">';
        }
        newcontent += '<span class="">or </span>';
        newcontent += '<button class="btn btn-success btn-file" onclick="open_upload(this)" data-file="#attach_graphic5" type="button">graphic</button>';
        newcontent += '<form class="input-file" id="attach_graphic5_form" method="post" enctype="multipart/form-data" action="ajax/async-uploadImage.php" style="margin-bottom:0px;">';
        newcontent += '<input type="file" name="imageUpload" id="attach_graphic5" class="input-file-graphics" onchange="upload_attach(5)">';
        newcontent += '<input type="hidden" name="uploadType" value="answer">';
        newcontent += '<input type="hidden" name="attach_graphic5_url" id="attach_graphic5_url" value="' + members[i][20] + '">';
        newcontent += '<div class="clearboth"></div>';
        newcontent += '</form>';
        if (members[i][20] == "") {
            newcontent += '<div class="answer_attachment ml15" id="attach_graphic5_img"></div>';
        } else {
            newcontent += '<div class="answer_attachment ml15" id="attach_graphic5_img"><img src="' + members[i][20] + '" class="attach_images"><div class="attach_trash white-trash btn btn-success" onclick="trash_attachment(5)></div></div>';
        }
        newcontent += '</div>';
        newcontent += '</div>';
        newcontent += '</div>';
        if (user_role == 2) {
            newcontent += '<div class="clear" style="display: none;">';
        } else {
            newcontent += '<div class="my-2">';
        }
        newcontent += '<hr>';
        newcontent += '<div class="d-flex align-items-center">';
        newcontent += '<button class="btn btn-success btn-uploadAudio my-3" onclick="open_upload(this)" data-file="#attach_audio" type="button"></button><span class="ml-2">Correct Answer Audio</span>';
        newcontent += '<form class="input-file" id="attach_audio_form" method="post" enctype="multipart/form-data" action="ajax/async-uploadAudio.php" style="margin-bottom:0px;">';
        newcontent += '<input type="file" name="audioUpload" id="attach_audio" class="input-file-audio" onchange="upload_audio(1)">';
        newcontent += '<input type="hidden" name="attach_audio_src" id="attach_audio_src" value="' + members[i][21] + '">';
        newcontent += '</form>';
        newcontent += '</div>';
        if (members[i][21] == "") {
            newcontent += '<div class="inline-block ml15" id="attach_audio_play"></div>';
        } else {
            newcontent += '<div class="inline-block ml15" id="attach_audio_play"><audio controls="controls"><source src="' + members[i][21] + '" type="audio/mp3" /></audio></div>';
            newcontent += '<div class="d-flex align-items-center">';
            newcontent += '<div class="white-trash btn btn-success my-3" onclick="trash_audio(1)"></div><span class="ml-2">Delete Audio</span>';
            newcontent += '</div>';
        }
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
    var opt = { callback: pageselectCallback };
    // Set pagination config
    opt['items_per_page'] = 1;
    opt['next_text'] = "Next";
    opt['num_display_entries'] = 3;
    opt['num_edge_entries'] = 1;
    opt['prev_text'] = "Prev";
    // Avoid html injections in this demo
    var htmlspecialchars = { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" }
    $.each(htmlspecialchars, function(k, v) {
        opt.prev_text = opt.prev_text.replace(k, v);
        opt.next_text = opt.next_text.replace(k, v);
    })
    return opt;
}


$(document).ready(function() {

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

/* upload attached image event; can upload multiple images by num value maximum 5  */
function upload_attach(num) {

    if (typeof FileReader !== "undefined") {
        $('.input-file-graphics').each(function(index, element) {
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
            } catch (e) {

            }
        });
    }

    if (num == 0) {
        $("#attach_question_form").ajaxForm({
            success: function(data) {
                if (data != "failed") {
                    $("#attach_question_url").val(data);
                    var content = '<img src="' + data + '" class="attach_images"><div class="attach_trash white-trash btn btn-success" onclick="trash_attachment(0)"></div>';
                    content += '<div class="inline-block ml15 show-before-answer"><span class="label-show-button">Show Before Answer</span>';
                    content += '<input type="checkbox" id="checked-show-image" name="checkboxes" checked >';
                    content += '<label for="checked-show-image"><span></span></label></div>';
                    $("#attach_question_img").html(content);
                }
            }
        }).submit();
    } else if (num == 1) {
        $("#attach_graphic1_form").ajaxForm({
            success: function(data) {
                if (data != "failed") {
                    $("#attach_graphic1_url").val(data);
                    var content = '<img src="' + data + '" class="attach_images"><div class="attach_trash white-trash btn btn-success" onclick="trash_attachment(1)"></div>';
                    $("#attach_graphic1_img").html(content);
                }
            }
        }).submit();
    } else if (num == 2) {
        $("#attach_graphic2_form").ajaxForm({
            success: function(data) {
                if (data != "failed") {
                    $("#attach_graphic2_url").val(data);
                    var content = '<img src="' + data + '" class="attach_images"><div class="attach_trash white-trash btn btn-success" onclick="trash_attachment(2)"></div>';
                    $("#attach_graphic2_img").html(content);
                }
            }
        }).submit();
    } else if (num == 3) {
        $("#attach_graphic3_form").ajaxForm({
            success: function(data) {
                if (data != "failed") {
                    $("#attach_graphic3_url").val(data);
                    var content = '<img src="' + data + '" class="attach_images"><div class="attach_trash white-trash btn btn-success" onclick="trash_attachment(3)"></div>';
                    $("#attach_graphic3_img").html(content);
                }
            }
        }).submit();
    } else if (num == 4) {
        $("#attach_graphic4_form").ajaxForm({
            success: function(data) {
                if (data != "failed") {
                    $("#attach_graphic4_url").val(data);
                    var content = '<img src="' + data + '" class="attach_images"><div class="attach_trash white-trash btn btn-success" onclick="trash_attachment(4)"></div>';
                    $("#attach_graphic4_img").html(content);
                }
            }
        }).submit();
    } else if (num == 5) {
        $("#attach_graphic5_form").ajaxForm({
            success: function(data) {
                if (data != "failed") {
                    $("#attach_graphic5_url").val(data);
                    var content = '<img src="' + data + '" class="attach_images"><div class="attach_trash white-trash btn btn-success" onclick="trash_attachment(5)"></div>';
                    $("#attach_graphic5_img").html(content);
                }
            }
        }).submit();
    }
}

/* upload audio file event */
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
            success: function(data) {
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
            success: function(data) {
                if (data != "failed") {
                    $("#attach_audio_src").val(data);
                    var content = '<audio controls="controls"><source src="' + data + '" type="audio/mp3" /></audio><div class="attach_trash white-trash btn btn-success" onclick="trash_audio(1)"></div>';
                    $("#attach_audio_play").html(content);
                }
            }
        }).submit();
    }

}

/* reset image input element as blank */
function trash_attachment(num) {
    if (num == 0) {
        $("#attach_question_url").val("");
        $("#attach_question_img").html("");
        document.getElementById("attach_question_form").reset();
    } else if (num == 1) {
        $("#attach_graphic1_url").val("");
        $("#attach_graphic1_img").html("");
        document.getElementById("attach_graphic1_form").reset();
    } else if (num == 2) {
        $("#attach_graphic2_url").val("");
        $("#attach_graphic2_img").html("");
        document.getElementById("attach_graphic2_form").reset();
    } else if (num == 3) {
        $("#attach_graphic3_url").val("");
        $("#attach_graphic3_img").html("");
        document.getElementById("attach_graphic3_form").reset();
    } else if (num == 4) {
        $("#attach_graphic4_url").val("");
        $("#attach_graphic4_img").html("");
        document.getElementById("attach_graphic4_form").reset();
    } else if (num == 5) {
        $("#attach_graphic5_url").val("");
        $("#attach_graphic5_img").html("");
        document.getElementById("attach_graphic5_form").reset();
    }
}

/* reset audio input element as blank */
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

/* trigger to upload file event */
function open_upload(object) {
    var file_el_id = $(object).data('file');
    $(file_el_id).click();
}

/* close require modal popup  */
function close_requieModal() {
    $(".require-modal-wrapper").bPopup().close();
}

/* close save Modal Popup */
function close_saveModal() {
    $(".save-modal-wrapper").bPopup().close();
    //window.location.href = "personal-tests.php";
}

/* translate file name to en */
function play_words() {
    var text = $("#correct-answer-notes").val();
    var test_id = $("#test_id").val();
    if (test_id == "") {
        $(".audio-modal-wrapper").bPopup();
    } else {
        if (text != "") {
            $.ajax({
                url: "/ajax/async-translate",
                dataType: "json",
                type: "POST",
                data: { text: text, test_id: test_id },
                success: function(data) {
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

/* insert/update test event by test id(multiple type test) */
function save_test(action, award_time_import) {

    var flag = true;
    $(".grade-hint").removeClass("missing-element");
    $(".test-hint").removeClass("missing-element");
    $(".question-hint").removeClass("missing-element");
    $(".answer-hint").removeClass("missing-element");
    $(".answer-1-hint").removeClass("missing-element");
    $(".answer-2-hint").removeClass("missing-element");
    $(".correct-hint").removeClass("missing-element");

    var test_id = $("#test_id").val();
    var subject_id = $("#choose-subject-1 .dd-selected-value").val();
    var test_name = $("#test_name").val();
    var mastery_type = $("#choose-mastery-type .dd-selected-value").val();
    if (mastery_type == "") {
        mastery_type = "memorization";
    }

    var mastery_number = $("#choose-mastery-number .dd-selected-value").val();
    mastery_number = '';

    // since the #choose-award-time does not have a value by default, when you make a test for the, we have to put the default value for all new tests here -MN
    var award_time = $("#choose-award-time .dd-selected-value").val();
    if (award_time == "") {
        award_time = award_time_import;
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
    var answer_text_1 = $("#answer_text1").val().trim();
    var correct_1 = $("#correct_answer1").is(":checked") ? 1 : 0;
    var attach_graphic_1 = $("#attach_graphic1_url").val();
    var answer_text_2 = $("#answer_text2").val().trim();
    var correct_2 = $("#correct_answer2").is(":checked") ? 1 : 0;
    var attach_graphic_2 = $("#attach_graphic2_url").val();
    var answer_text_3 = $("#answer_text3").val().trim();
    var correct_3 = $("#correct_answer3").is(":checked") ? 1 : 0;
    var attach_graphic_3 = $("#attach_graphic3_url").val();
    var answer_text_4 = $("#answer_text4").val().trim();
    var correct_4 = $("#correct_answer4").is(":checked") ? 1 : 0;
    var attach_graphic_4 = $("#attach_graphic4_url").val();
    var answer_text_5 = $("#answer_text5").val().trim();
    var correct_5 = $("#correct_answer5").is(":checked") ? 1 : 0;
    var attach_graphic_5 = $("#attach_graphic5_url").val();
    var correct_answer_notes = $("#attach_audio_src").val();

    /* check adds form action */
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
        if (answer_text_1 == "" && attach_graphic_1 == "") {
            $(".answer-hint").addClass("missing-element");
            $(".answer-1-hint").addClass("missing-element");
            flag = false;
        }
        // removed to allow only 1 answer (for not using multiple choice)
        // if (answer_text_2 == "" && attach_graphic_2 == "") {
        //     $(".answer-hint").addClass("missing-element");
        //     $(".answer-2-hint").addClass("missing-element");
        //     flag = false;
        // }
        if (correct_1 == 0 && correct_2 == 0 && correct_3 == 0 && correct_4 == 0 && correct_5 == 0) {
            $(".correct-hint").addClass("missing-element");
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
        if (answer_text_1 == "" && attach_graphic_1 == "") {
            $(".answer-hint").addClass("missing-element");
            $(".answer-1-hint").addClass("missing-element");
            save_index++;
            flag = false;
        }
        // removed to allow only 1 answer (for not using multiple choice)
        // if (answer_text_2 == "" && attach_graphic_2 == "") {
        //     $(".answer-hint").addClass("missing-element");
        //     $(".answer-2-hint").addClass("missing-element");
        //     save_index++;
        //     flag = false;
        // }
        if (correct_1 == 0 && correct_2 == 0 && correct_3 == 0 && correct_4 == 0 && correct_5 == 0) {
            $(".correct-hint").addClass("missing-element");
            save_index++;
            flag = false;
        }

        if (save_index == 0) {
            if (!flag) {
                $(".require-modal-wrapper").bPopup();
            }
        } else if (save_index == 4) {
            if (test_id == "") {
                $(".require-modal-wrapper").bPopup();
            } else {
                $(".grade-hint").removeClass("missing-element");
                $(".test-hint").removeClass("missing-element");
                $(".question-hint").removeClass("missing-element");
                $(".answer-hint").removeClass("missing-element");
                $(".answer-1-hint").removeClass("missing-element");
                $(".answer-2-hint").removeClass("missing-element");
                $(".correct-hint").removeClass("missing-element");
                finish_test();
            }
        } else {
            $(".require-modal-wrapper").bPopup();
        }
    }

    /* check add test form is validated */
    if (flag && click_flag) {
        var session_token = $("#session_token").val();
        click_flag = false;
        var type = "multiple";
        $.ajax({
            url: "/ajax/async-tests/add",
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
                answer_text_1: answer_text_1,
                correct_1: correct_1,
                attach_graphic_1: attach_graphic_1,
                answer_text_2: answer_text_2,
                correct_2: correct_2,
                attach_graphic_2: attach_graphic_2,
                answer_text_3: answer_text_3,
                correct_3: correct_3,
                attach_graphic_3: attach_graphic_3,
                answer_text_4: answer_text_4,
                correct_4: correct_4,
                attach_graphic_4: attach_graphic_4,
                answer_text_5: answer_text_5,
                correct_5: correct_5,
                attach_graphic_5: attach_graphic_5,
                correct_answer_notes: correct_answer_notes,
                session_token: session_token
            },
            success: function(data) {
                if (data.result == "success") {
                    $("#test_id").val(data.test_id);
                    var question = data.question;
                    members = [];
                    for (var i = 0; i < question.length; i++) {
                        members.push([question[i].question_id, question[i].question_title, question[i].attach_image, question[i].attach_audio, question[i].show_image, question[i].show_button, question[i].answer[0].text, question[i].answer[0].correct, question[i].answer[0].attach, question[i].answer[1].text, question[i].answer[1].correct, question[i].answer[1].attach, question[i].answer[2].text, question[i].answer[2].correct, question[i].answer[2].attach, question[i].answer[3].text, question[i].answer[3].correct, question[i].answer[3].attach, question[i].answer[4].text, question[i].answer[4].correct, question[i].answer[4].attach, question[i].correct_note]);
                    }
                    if (action == "save_next") {
                        members.push(['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '']);
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

/* saved modal popup */
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
    // gets value and sets variable, so can be set below in DB via json
    award_time = $("#choose-award-time .dd-selected-value").val();
    if (award_time == "") {
        award_time = award_time_import;
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
            url: "/ajax/async-tests/add",
            dataType: "json",
            type: "POST",
            data: {
                type: type,
                test_id: test_id,
                mastery_number: mastery_number,
                award_time: award_time,
                form_options: form_options
            },
            success: function(data) {
                if (data.result == "success") {

                } else if (data.result == "expire") {
                    $('.expire-modal-wrapper').bPopup();
                }
            }
        });
    }
    $('.option-modal-wrapper').bPopup().close();
}

/* close audio modal popup */
function close_audioModal() {
    $('.audio-modal-wrapper').bPopup().close();
}

/* delete question modal PopUp */
function delete_question() {
    var question_id = $("#question_id").val();
    if (question_id != "") {
        $('.delete-question-modal').bPopup();
    }
}

/* close delModal event and delete question event */
function close_delModal() {
    $('.delete-question-modal').bPopup().close();
    var session_token = $("#session_token").val();
    var test_id = $("#test_id").val();
    var question_id = $("#question_id").val();
    var type = "question";
    $.ajax({
        url: "/ajax/async-tests/delete",
        dataType: "json",
        type: "POST",
        data: { type: type, test_id: test_id, question_id: question_id, session_token: session_token },
        success: function(data) {
            if (data.result == "success") {
                var question_number = $("#Pagination .current").text();
                question_number = question_number.replace("Prev", "");
                question_number = question_number.replace("Next", "");
                question_number = parseInt(question_number) - 1;
                members.splice(question_number, 1);
                if (members.length == 0) {
                    members = [
                        ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '']
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
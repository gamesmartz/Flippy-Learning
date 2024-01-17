function pageselectCallback(page_index, jq) {
    // Item per page must 1.
    var items_per_page = 1;

    var max_elem = Math.min((page_index + 1) * items_per_page, members.length);
    var newcontent = '';

    if (test_type == "multiple") {
        var answer = [];

        // Iterate through a selection of the content and build an HTML string
        for (var i = page_index * items_per_page; i < max_elem; i++) {
            newcontent += '<input type="hidden" id="question_id" value="' + members[i][0] + '">';
            if (members[i][5] != "") {
                if (members[i][6] == 1) {
                    newcontent += '<div class="question-row">';
                    newcontent += '<input type="hidden" id="question_audio" value="' + members[i][5] + '">';
                    newcontent += '<a onclick="play_words(1)" class="icon-audio"></a>';
                    newcontent += '</div>';
                }
            }
            if (members[i][4] != "") {
                newcontent += '<div class="question-img" style="margin: 0;"><img class="question-attach img-fluid" style="border-radius: 5px 5px 0px 0px;" src="' + members[i][4] + '" alt=""></div>';
            }

            // newcontent += '<div class="text-white mt-2" id="question_title" style="min-height: 50px; ' + form_options + '">' + members[i][1] + '</div>';

            newcontent += '<div class="lead-small question-row text-white p-2" id="question_title" style="' + form_options + '">' + members[i][3] + '</div>';
            newcontent += '<div style="display: flex; justify-content: center;">';

            answer = [
                [members[i][7], members[i][8], members[i][9]],
                [members[i][10], members[i][11], members[i][12]],
                [members[i][13], members[i][14], members[i][15]],
                [members[i][16], members[i][17], members[i][18]],
                [members[i][19], members[i][20], members[i][21]]
            ];

            for (var j = answer.length - 1; j >= 0; j--) {
                var k = Math.floor(Math.random() * answer.length);
                if (k > answer.length - 1) {
                    k = answer.length - 1;
                }
                if (answer[k][0] != "" || answer[k][2] != "") {
                    if (answer[k][0] != "") {
                        if (answer[k][0] == members[i][1]) {
                            if (members[i][2] == 1) {
                                newcontent += '<div class="answer-row" style="background: #9CDC48; border-radius: 5px;">';
                            } else {
                                newcontent += '<div class="answer-row" style="background: #F79953; border-radius: 5px;">';
                            }

                        } else {
                            newcontent += '<div class="answer-row">';

                        }
                    } else {
                        if (answer[k][2] == members[i][1]) {
                            if (members[i][2] == 1) {
                                newcontent += '<div class="answer-row" style="background: #9CDC48; border-radius: 5px;">';
                            } else {
                                newcontent += '<div class="answer-row" style="background: #F79953; border-radius: 5px;">';
                            }

                        } else {
                            newcontent += '<div class="answer-row">';

                        }
                    }
                    newcontent += '<div style="padding: 2px 15px;">';
                    if (answer[k][0] != "") {
                        newcontent += '<span class="answer-text" style="margin: 0;">' + answer[k][0] + '</span>';
                    }
                    if (answer[k][2] != "") {
                        newcontent += '<img src="' + answer[k][2] + '" class="attach_images">';
                    }
                    newcontent += '</div></div>';
                }
                answer.splice(k, 1);
            }

            if (members[i][1] == "dont-know") {
                newcontent += '<div style="margin: 8px 5px;">';
                newcontent += '<div style="display: flex; align-items: center; height: 39px; padding: 0 15px; color: #FFF;  font-size: 1.2em; ; border-radius: 5px; background-color: #f3821f; font-weight: 500;"><div>?</div></div>';
                newcontent += '</div>';
            } else {
                newcontent += '<div class="mt20"></div>';
            }

        }
        
        newcontent += '</div>';

    } else if (test_type == "fill") {
        // Iterate through a selection of the content and build an HTML string
        for (var i = page_index * items_per_page; i < max_elem; i++) {
            newcontent += '<input type="hidden" id="question_id" value="' + members[i][0] + '">';
            if (members[i][5] != "") {
                if (members[i][6] == 1) {
                    newcontent += '<div class="question-row">';
                    newcontent += '<input type="hidden" id="question_audio" value="' + members[i][5] + '">';
                    newcontent += '<a onclick="play_words(1)" class="icon-audio"></a>';
                    newcontent += '</div>';
                }
            }            if (members[i][4] != "") {
                newcontent += '<div class="question-row"><img class="question-attach img-fluid" style="border-radius: 5px 5px 0px 0px;" src="' + members[i][4] + '" alt=""></div>';
            }
            newcontent += '<div class="lead-small question-row text-white pt-2" id="question_title" style="' + form_options + '">' + members[i][3] + '</div>';
            newcontent += '<div class="mt10">';
            if (members[i][2] == 1) {
                newcontent += '<input type="text" id="answer" value="' + members[i][1] + '" class="textbox textbox-format">';
            } else {
                if (members[i][1] == "dont-know") {
                    newcontent += '<input type="text" id="answer" value="" class="textbox textbox-format">';
                } else {
                    newcontent += '<input type="text" id="answer" value="' + members[i][1] + '" class="textbox textbox-format" style="background:#F79953;">';
                }
            }
            newcontent += '</div>';

            if (members[i][1] == "dont-know") {
                newcontent += '<div style="display: flex; justify-content: center;" class="pos-rel"><span class="help-link"><a>?</a></span></div>';
            } else {
                newcontent += '<div class="mt20"></div>';
            }

        }
    } else if (test_type == "spelling") {
        // Iterate through a selection of the content and build an HTML string
        for (var i = page_index * items_per_page; i < max_elem; i++) {
            newcontent += '<input type="hidden" id="question_id" value="' + members[i][0] + '">';
            newcontent += '<div class="lead-small question-row">';
            newcontent += '<input type="hidden" id="word-in-sentence" value="' + members[i][4] + '">';
            newcontent += '<a onclick="play_words(2)" class="icon-playbig"></a>';
            newcontent += '</div>';

            newcontent += '<div class="mt15">';
            if (members[i][2] == 1) {
                newcontent += '<input type="text" id="answer" value="' + members[i][1] + '" class="textbox textbox-format">';
            } else {
                if (members[i][1] == "dont-know") {
                    newcontent += '<input type="text" id="answer" value="" class="textbox textbox-format">';
                } else {
                    newcontent += '<input type="text" id="answer" value="' + members[i][1] + '" class="textbox textbox-format" style="background:#F79953;">';
                }
            }
            newcontent += '</div>';

            if (members[i][1] == "dont-know") {
                newcontent += '<div style="display: flex;  justify-content: center; color: #fff;" class="view-help"><div style="padding: 5px 15px; border-radius: 5px;  background-color: #f3821f; font-weight: 500;">?</div></div>';
            } else {
                newcontent += '<div class="mt20"></div>';
            }

        }
    }

    // Replace old content with new content
    $('#question_form').html(newcontent);

    // Prevent click eventpropagation
    return false;
}

// The form contains fields for many pagiantion optiosn so you can
// quickly see the results of the different options.            
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
    optInit['current_page'] = current_member;
    $("#Pagination").pagination(members.length, optInit);
});

/* audio html element append player html */
function play_words(num) {
    if (num == 1) {
        var text = $("#question_audio").val();
    } else if (num == 2) {
        var text = $("#word-in-sentence").val();
    }
    if (text != "") {
        $("#player").html('<audio controls="controls" autoplay="autoplay" style="display: none;"><source src="' + text + '" type="audio/mp3" /></audio>');
        /*
         $.ajax({
         url: "ajax/async-translate.php",
         dataType : "json",
         type : "POST",
         data : { text : text, test_id : test_id },
         success : function(data){
         if(data.result == "success"){
         $("#player").html('<audio controls="controls" autoplay="autoplay" style="display: none;"><source src="upload/audio/'+ test_id +'/'+ data.name +'.mp3" type="audio/mp3" /></audio>');
         }
         }
         });
         */
    }
}
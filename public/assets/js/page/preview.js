function pageselectCallback(page_index, jq) {
    // Item per page must 1.
    var items_per_page = 1;

    var max_elem = Math.min((page_index + 1) * items_per_page, members.length);
    var newcontent = '';

    /* check type_type */
    if (test_type == "multiple") {
        var answer = [];

        // Iterate through a selection of the content and build an HTML string
        for (var i = page_index * items_per_page; i < max_elem; i++) {
            newcontent += '<input type="hidden" id="question_id" value="' + members[i][0] + '">';

            // newcontent += '<div class="text-white mt-2" id="question_title" style="min-height: 50px; ' + form_options + '">' + members[i][1] + '</div>';

            if (members[i][3] != "") {
                if (members[i][4] == 1) {
                    newcontent += '<div class="question-row">';
                    newcontent += '<input type="hidden" id="question_audio" value="' + members[i][3] + '">';
                    newcontent += '<a onclick="play_words(1)" class="icon-audio"></a>';
                    newcontent += '</div>';
                }
            }
            if (members[i][2] != "") {
                newcontent += '<div class="question-img" style="margin: 0;"><img class="question-attach" src="/' + members[i][2] + '" alt=""></div>';
            }

            newcontent += '<div class="text-white mt-2" id="question_title" style="min-height: 50px; ' + form_options + '">' + members[i][1] + '</div>';

            newcontent += '<div class="clear" style="margin: 10px;"></div>';

            answer = [
                [members[i][5], members[i][6], members[i][7]],
                [members[i][8], members[i][9], members[i][10]],
                [members[i][11], members[i][12], members[i][13]],
                [members[i][14], members[i][15], members[i][16]],
                [members[i][17], members[i][18], members[i][19]]
            ];

            for (var j = answer.length - 1; j >= 0; j--) {
                var k = Math.floor(Math.random() * answer.length);
                if (k > answer.length - 1) {
                    k = answer.length - 1;
                }
                if (answer[k][0] != "" || answer[k][2] != "") {
                    newcontent += '<div class="answer-row">';
                    newcontent += '<input type="radio" name="correct_answer" class="ml-2 answer-radio" style="display: none">';
                    newcontent += '<div class="answer-content">';
                    if (answer[k][0] != "") {
                        newcontent += '<div><span class="answer-text">' + answer[k][0] + '</span><span style="color:#fff">answer</span></div>';
                        newcontent += '<div><span class="answer-text">' + members[i][21] + '</span><span style="color:#fff">question</span></div>';
                    }
                    if (answer[k][2] != "") {
                        newcontent += '<img src="/' + answer[k][2] + '" class="attach_images">';
                    }
                    newcontent += '</div></div>';
                }
                answer.splice(k, 1);
            }

            newcontent += '<div class="mt20"></div>';
        }
    }


    // Replace old content with new content
    $('#question_form').html(newcontent);

    // Prevent click eventpropagation
    return false;
}

// The form contains fields for many pagination option so you can
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
    optInit['current_page'] = 0;
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

var view_start;
var row_object;

/* show video player event */
function view_video(object, video_id) {
    row_object = object;
    $("#video_id").val(video_id);
    var input_id = "#test_video_" + video_id;
    var video_link = $(input_id).val();
    if (video_link != "") {
        // create youtube player
        var player;
        player = new YT.Player('video_player', {
            height: '509',
            width: '854',
            videoId: video_link,
            events: {
                'onReady': onPlayerReady
            }
        });

        $(".test-video-modal").bPopup();
    }
}

// autoplay video
function onPlayerReady(event) {
    event.target.playVideo();
    view_start = new Date();
}

// when video ends
function onPlayerStop() {

    var view_stop = new Date();
    var view_time = (view_stop.getTime() - view_start.getTime()) / 1000;    // seconds

    var video_id = $("#video_id").val();
    var type = "view_video";
    $.ajax({
        url: "ajax/async-video.php?action=add",
        dataType: "json",
        type: "POST",
        data: {type: type, video_id: video_id, view_time: view_time},
        success: function (data) {
            if (data.result == "success") {
                $(row_object).find(".watch-time").text(data.watch_time);
            } else if (data.result == "expire") {
                $('#session-expired-modal').modal();
            }
        }
    });
}
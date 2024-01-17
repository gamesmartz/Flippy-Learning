/* submit video popup */
function submit_video() {
    $(".submit-video-modal").bPopup();
}

/* close submit modal and save video event */
function close_submitModal() {

    $(".title-error").css("display", "none");
    $(".link-error").css("display", "none");

    var video_title = $("#video_title").val();
    var video_link = $("#video_link").val();
    var flag = true;
    if (video_title == "") {
        $(".title-error").css("display", "block");
        flag = false;
    }
    if (video_link == "") {
        $(".link-error").css("display", "block");
        flag = false;
    }

    if (flag) {
        $(".submit-video-modal").bPopup().close();
        var type = "video";
        $.ajax({
            url: "ajax/async-video.php?action=add",
            dataType: "json",
            type: "POST",
            data: {type: type, test_id: test_id, video_title: video_title, video_link: video_link},
            success: function (data) {
                if (data.result == "success") {
                    if (data.user_role == 2) {
                        $(".submit-video").remove();
                    }
                } else if (data.result == "expire") {
                    $('#session-expired-modal').modal();
                }
            }
        });
    }
}

/* video like + 1 by current logged in user  */
function vote_like(object, video_id) {

    var type = "vote";
    var option = "likes";
    $.ajax({
        url: "ajax/async-video.php?action=add",
        dataType: "json",
        type: "POST",
        data: {type: type, video_id: video_id, option: option},
        success: function (data) {
            if (data.result == "success") {
                $(object).parents("tr").find(".like-count").text(data.likes + " Likes");
                if (data.user_role == 2) {
                    $(object).parents(".vote-video").remove();
                }
                $(".voting-success-modal").bPopup();
            } else if (data.result == "expire") {
                $('#session-expired-modal').modal();
            }
        }
    });
}

/* video dislike + 1 by current logged in user  */
function vote_dislike(object, video_id) {

    var type = "vote";
    var option = "dislikes";
    $.ajax({
        url: "ajax/async-video.php?action=add",
        dataType: "json",
        type: "POST",
        data: {type: type, video_id: video_id, option: option},
        success: function (data) {
            if (data.result == "success") {
                if (data.user_role == 2) {
                    $(object).parents(".vote-video").remove();
                } else {
                    $(object).parents("tr").find(".dislike-count").text(" - " + data.dislikes + " Dislikes");
                }
                $(".voting-success-modal").bPopup();
            } else if (data.result == "expire") {
                $('#session-expired-modal').modal();
            }
        }
    });
}

/* close vote modal */
function close_votModal() {
    $(".voting-success-modal").bPopup().close();
}

var view_start;
/* get video link event by video id */
function view_video(video_id) {
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
                return;
            } else if (data.result == "expire") {
                $('#session-expired-modal').modal();
            }
        }
    });

}

/* change page min width event same as extra.js*/
function changeMinWidth() {
    if ($('.in-browser-test-question').length > 0) {
        $(".page-min-width").removeClass("page-min-width").addClass("page-min-width-300");
    }
}
$(document).ready(function () {
    /* call change min width when page loads */
    changeMinWidth();
});

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

        $('#videoModal').bPopup();
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
                $('#session-expired-modal').bPopup();
            }
        }
    });
}

// Set the name of the hidden property and the change event for visibility
var hidden, visibilityChange;
if (typeof document.webkitHidden !== "undefined") {
    hidden = "webkitHidden";
    visibilityChange = "webkitvisibilitychange";
} else if (typeof document.mozHidden !== "undefined") {
    hidden = "mozHidden";
    visibilityChange = "mozvisibilitychange";
} else if (typeof document.msHidden !== "undefined") {
    hidden = "msHidden";
    visibilityChange = "msvisibilitychange";
} else if (typeof document.hidden !== "undefined") {
    hidden = "hidden";
    visibilityChange = "visibilitychange";
}

function handleVisibilityChange() {
    if (document[hidden]) {
        stopTimer();
    } else {
        var last_time = localStorage.getItem('last_time');
        if (last_time != null) {
            var browser_time = new Date();
            var elapsed_time = (browser_time.getTime() - last_time) / 1000; // seconds
            if (elapsed_time < 3600) {
                if (left_time - elapsed_time < 0) {
                    left_time = 0;
                } else {
                    left_time -= elapsed_time;
                }
            }
        }

        /// remove timer
        // if (alarm.volume != 0) {
        //     alarm.play();
        // }

       /// remove timer
       // startTimer();
    }
}

// Warn if the browser doesn't support addEventListener or the Page Visibility API
if (typeof document.addEventListener === "undefined" || typeof document[hidden] === "undefined") {
    alert("This browser does not support the Page Visibility API.");
} else {
    // Handle page visibility change
    document.addEventListener(visibilityChange, handleVisibilityChange, false);
}

var timerObj = null;
var alarm;

$(document).ready(function () {

    /// remove timer

    // var minutes = parseInt(left_time / 60, 10);
    // var seconds = parseInt(left_time % 60, 10);
    //
    // seconds = seconds < 10 ? "0" + seconds : seconds;
    //
    // $("#left-time").text(minutes + ":" + seconds);
    //
    // alarm = document.getElementById("alarm");
    // alarm.volume = 0;
    //
    // alarm.addEventListener('ended', function (e) {
    //     alarm.play();
    // });

   // startTimer();

});
/* this function is used for ionic app execute script */

function showPhoneAlertModal(message) {
    $("div.phone-alert-modal h5").text(message);
    $("div.phone-alert-modal").bPopup();
}
/* start timer event when 1 test question loads */
function startTimer() {
    timerStarted = 1;
    if (timerObj == null) {
        $("#left-time").css("display", "block");
        var minutes, seconds = 0;
        timerObj = setInterval(function () {

            minutes = parseInt(left_time / 60, 10);
            seconds = parseInt(left_time % 60, 10);

            if ('00' == parseInt(left_time % 60, 10)) {
                $.each(audioAlerts, function (index, audioAlert) {
                    if (audioAlert.audio_time * 1 === minutes && parseInt(max_time / 60, 10) !== audioAlert.audio_time * 1) {
                        tmpAudioAlert = left_time;
                        if (window.location.pathname !== '/api/test-question') {
                            var audio = new Audio('/upload/audio-alerts/' + audioAlert.id + '/' + audioAlert.audio_file);
                            audio.play();
                        }

                    }
                });
            }

            seconds = seconds < 10 ? "0" + seconds : seconds;

            var time_remaining = minutes + ":" + seconds;

            $("#left-time").text(time_remaining);

            $.ajax({
                url: "/ajax/async-updateUser.php?action=time_remaining",
                dataType: "json",
                type: "POST",
                data: {time_remaining: time_remaining},
                success: function (data) {
                    if (data.result == "success") {

                    }
                }
            });

            var last_time = new Date();
            localStorage.setItem('last_time', last_time.getTime());

            if (--left_time < 0) {
                left_time = 0;
                if (alarm.volume == 0) {
                    alarm.play();
                }
                if (alarm.volume + 1 / 80 > 1) {
                    alarm.volume = 1;
                } else {
                    alarm.volume += 1 / 80;
                }
            }
        }, 1000);
    }
}
/* stop timer event */
function stopTimer() {
    if (timerObj != null) {
        $("#left-time").css("display", "none");
        clearInterval(timerObj);
        timerObj = null;
        alarm.pause();
    }
}

/* stop alarm event */
function stopAlarm() {
    alarm.pause();
    alarm.volume = 0;
}
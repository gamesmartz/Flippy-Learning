$(function() {
    /* from new factors master to correct answered */
    $(".page-min-width").removeClass("page-min-width");

    var content = '';
    content += '<h5 style="color:#1db0f6 !important">' + report_date + ' Questions Correct: ' + correct_answered + '</h5>';
    $('#report-date').html(content);

    if (test_ids.length > 0) {
        var content = '';

        for (var i = 0; i < test_ids.length; i++) {

            content += '<div style="margin-top: 5px;">';
            if (date_flag) {
                content += '<a style="text-decoration:none;color: #1db0f6; font-size: 1.2rem; font-weight: 300;" href="../view?id=' + test_ids[i] + '&index=' + first_indexes[i] + '&user=' + user_id + '&from=reports&date=' + report_date + '">';
                content += '<span style="position:relative; top:1px; padding: 8px 25px; border-radius: 3px; background-color:' + test_colors[i] + ';"></span>';

                content += '<span style="margin-left: 10px;" class="btn btn-primary btn-app-secondary chapter-bubble">' + test_name_before_colon(test_names[i]) + '</span>';
                content += '<span style="margin-left: 10px;">' + test_name_after_colon(test_names[i]) + '</span>';
                content += '<span> - ' + grade_subject[i] + '</span>';
                content += '</a>';
            } else {
                content += '<a style="color: #fff; font-size: 1.2rem; font-weight: 300;" href="../view?id=' + test_ids[i] + '&index=' + first_indexes[i] + '&user=' + user_id + '&from=reports&report_id=' + report_id + '">' + test_names[i] + '</a>';
            }
            content += '</div>';

            // var test_video = test_videos[i];
            // for (var j = 0; j < test_video.length; j++) {
            //     content += '<div>';
            //     content += '<input type="hidden" id="test_video_' + test_video[j][0] + '" value="' + test_video[j][1] + '">';
            //     content += '<a class="video-learn-lnk" onclick="view_video(this,' + test_video[j][0] + ')">';
            //     content += '<img src="assets/images/video-test.png" class="watch-label">';
            //     content += '<span class="watch-time">' + test_video[j][4] + '</span>' + ' / ' + test_video[j][3];
            //     content += '</a>';
            //     content += '</div>';
            //

            $('#report-tests').html(content);
        }
    }

    $('#question-correct-container').highcharts({
        chart: {
            type: 'spline',
            zoomType: 'x',
            height: 650
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
        credits: {
            enabled: false
        },
        /*
         legend: {
         enabled: false
         },
         plotOptions: {
         area: {
         fillColor: {
         linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
         stops: [
         [0, Highcharts.getOptions().colors[0]],
         [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
         ]
         },
         marker: {
         radius: 2
         },
         lineWidth: 1,
         states: {
         hover: {
         lineWidth: 1
         }
         },
         threshold: null
         }
         },
         */
        tooltip: {
            useHTML: true,
            formatter: function() {
                return '<table><tr><td><strong>' + this.point.test_name + '</strong></td></tr><tr><td style="text-align: center;">Questions: <strong>' + this.y + '</strong></td></tr></table>';
            }
        },
        plotOptions: {
            series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function() {
                            if (date_flag)
                                window.location.href = '../view?id=' + this.test_id.toString() + '&index=' + this.summary_index.toString() + '&user=' + user_id + '&from=reports&date=' + report_date;
                            else
                                window.location.href = '../view?id=' + this.test_id.toString() + '&index=' + this.summary_index.toString() + '&user=' + user_id + '&from=reports&report_id=' + report_id;
                        }
                    }
                }
            }
        },

        series: [{
            //type: 'area',
            name: 'Questions',
            // Define the data points. All series have a dummy year
            // of 1970/71 in order to be compared on the same x axis. Note
            // that in JavaScript, months start at 0 for January, 1 for February etc.
            data: test_result
        }]
    });

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

    if (login_flag) {
        var view_stop = new Date();
        var view_time = (view_stop.getTime() - view_start.getTime()) / 1000; // seconds

        var video_id = $("#video_id").val();
        var type = "view_video";
        $.ajax({
            url: "/ajax/async-video/add",
            dataType: "json",
            type: "POST",
            data: { type: type, video_id: video_id, view_time: view_time },
            success: function(data) {
                if (data.result == "success") {
                    $(row_object).find(".watch-time").text(data.watch_time);
                } else if (data.result == "expire") {
                    $('#session-expired-modal').modal();
                }
            }
        });
    }
}
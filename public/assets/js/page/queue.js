$(document).ready(function() {


    if ($("#queue-tests-table .queue-rows").hasClass("queue-rows")) {

        $("#queue-tests-table").sortable({
            cancel: ".cancel-drag-drop",
            update: function(event, ui) {
                var sortedIDs = $("#queue-tests-table").sortable("toArray");
                sortedIDs = sortedIDs.filter(Boolean);
                //console.log(sortedIDs);
                var type = "resort";
                $.ajax({
                    url: "/ajax/async-tests/add",
                    dataType: "json",
                    type: "POST",
                    data: { type: type, sortedIDs: sortedIDs },
                    // Not using 'Start GS' Button
                    // success: function(data) {
                    //     if (data.result == "success") {
                    //         $(".begin-wrapper").html('<div href="test-question?id=' + sortedIDs[0] + '&from=que" class="btn btn-lg btn-primary btn-app-primary">Start GS</div>');
                    //         return;
                    //     } else if (data.result == "expire") {
                    //         $('.expire-modal-wrapper').bPopup();
                    //     }
                    // }
                });
            }
        });

    }

    $(".gs-dropdown").ddslick();

    $("#choose-award-time").ddslick();
});



/* open tooltip event on table td element this function is used for most js */
function open_tooltip(object) {
    if ($(object).parent('td').find('.test-attachment').attr("src") != "") {
        $($(object).parent('td').find('.tooltip-testinfo')).stop().fadeIn(100);
    }
}

/* close tooltip event on table td element, this function is used for most js */
function close_tooltip(object) {
    $($(object).parent('td').find('.tooltip-testinfo')).stop().fadeOut(250);
}

function remove_que(object, test_id) {
    var type = "que";
    $.ajax({
        url: "/ajax/async-tests/delete",
        dataType: "json",
        type: "POST",
        data: { type: type, test_id: test_id },
        success: function(data) {
            if (data.result == "success") {
                $(object).parents(".queue-rows").remove();
                var rows = $("#queue-tests-table").find("div");
                if (rows.length == 0) {
                    var newcontent = `<div class="row"><div class="col-md-12 text-center border-0 py-4">
                        <a href="/progress" class="nav-link text-dark">
                        Your queue is empty. Add a chapter before using the overlay software.
                      </a>
                    </div></div>`;
                    /*
                      newcontent += '<div id="queue-tests-table" style="display: flex; flex-direction: column;">';
                      newcontent += '<div class="queue-rows gs-small-table-fontsize" style="display: flex; justify-content: center; align-items: center; height: 45px; border-bottom: 1px solid #d7d7d7; width: 100%;">';
                      newcontent += '<div style="text-align: center;">';
                      newcontent += '<a href="help-how-gs-works" style="color: #000; font-family: \'Raleway\', sans-serif;">';
                      newcontent += 'Queue empty. Add a test to your queue. Click here to find out how.';
                      newcontent += '</a>';
                      newcontent += '</div>';
                      newcontent += '</div>';
                      newcontent += '</div>';
                      */

                    $("#queue-tests-table").html(newcontent);

                    $(".begin-wrapper").html('').addClass('d-none');
                }
                return;
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}

function view_video(object, test_id) {
    row_object = object;
    $("#test_id").val(test_id);
    var input_id = "#test_video_" + test_id;
    var video_id = $(input_id).val();
    if (video_id != "") {
        // create youtube player
        var player;
        player = new YT.Player('video_player', {
            height: '509',
            width: '854',
            videoId: video_id,
            events: {
                'onReady': onPlayerReady,
                'onStateChange': onPlayerStateChange
            }
        });

        $(".test-video-modal").bPopup();
    }
}

var resetProgressModal = new bootstrap.Modal(document.getElementById('reset-progress-modal'), {
    keyboard: false
});

// autoplay video
function onPlayerReady(event) {
    event.target.playVideo();
}

// when video ends
function onPlayerStateChange(event) {
    if (event.data === 0) {
        var test_id = $("#test_id").val();
        var type = "view_video";
        $.ajax({
            url: "/ajax/async-tests/add",
            dataType: "json",
            type: "POST",
            data: { type: type, test_id: test_id },
            success: function(data) {
                if (data.result == "success") {
                    $(row_object).parents("div").find(".view_video").html('<a onclick="view_video(this,' + test_id + ')">watched</a>');
                    return;
                } else if (data.result == "expire") {
                    $('.expire-modal-wrapper').bPopup();
                }
            }
        });
    }
}

function que_options(object, test_id) {
    $("#test_id").val(test_id);
    $(".option-modal-wrapper").bPopup();
    var type = "que_options";
    $.ajax({
        url: "/ajax/async-tests/get",
        dataType: "json",
        type: "POST",
        data: { type: type, test_id: test_id },
        success: function(data) {
            if (data.result == "success") {
                if (data.mastery_number == "") {
                    $("#choose-mastery-number .dd-selected-value").val("4");
                    $("#choose-mastery-number .dd-selected").text("4");
                } else {
                    $("#choose-mastery-number .dd-selected-value").val(data.mastery_number);
                    $("#choose-mastery-number .dd-selected").text(data.mastery_number);
                }
                if (data.award_time == "") {
                    var award_time = "1:30";
                } else {
                    var award_time = data.award_time;
                }
                $("#choose-award-time .dd-selected-value").val(award_time);
                $("#choose-award-time .dd-selected").text(award_time);

            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}


function close_optionModal() {

    var test_id = $("#test_id").val();

    var award_time = $("#choose-award-time .dd-selected-value").val();
    if (award_time == "") {
        award_time = "1:30";
    }

    var mastery_number = $("#choose-mastery-number .dd-selected-value").val();
    if (mastery_number == "") {
        mastery_number = "4";
    }

    var type = "que_options";
    $.ajax({
        url: "/ajax/async-tests/add",
        dataType: "json",
        type: "POST",
        data: { type: type, test_id: test_id, mastery_number: mastery_number, award_time: award_time },
        success: function(data) {
            if (data.result == "success") {
                $(".option-modal-wrapper").bPopup().close();
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}

var row_object;

function reset_progress(object, test_id) {
    row_object = object;
    $("#test_id").val(test_id);
    resetProgressModal.show();
    //$(".reset-progress-modal").bPopup();
}

function close_resetModal() {
    resetProgressModal.hide();
    //$(".reset-progress-modal").bPopup().close();

    var test_id = $("#test_id").val();
    var type = "reset_progress";
    $.ajax({
        url: "/ajax/async-tests/add",
        dataType: "json",
        type: "POST",
        data: { type: type, test_id: test_id },
        success: function(data) {
            if (data.result == "success") {
                // $(row_object).parents("tr").find(".test_progress").html('<p class=""><a href="reports?id='+ test_id +'">'+ data.progress +'</a></p>');
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });

}

$.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

function move_to_top(test_id) {
    var type = "move_to_top";
    $.ajax({
        url: "/ajax/async-tests/edit",
        dataType: "json",
        type: "POST",
        data: { type: type, test_id: test_id },
        success: function(data) {
            if (data.result == "success") {
                var item = '#queue-tests-table div[id=' + test_id + ']';
                $(item).insertBefore($('#queue-tests-table div:eq(0)'));

                $(".begin-wrapper").html('<a href="test-question?id=' + test_id + '&from=que" class="btn btn-lg btn-primary btn-app-primary">Start GS</a>');
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}

function open_video_modal_queue() {
    $(".open-video-modal-queue").bPopup({
        // onOpen: function() { console.log('onOpen fired'); },
        onClose: function() {
            // console.log('onClose fired');
            // https://stackoverflow.com/questions/15164942/stop-embedded-youtube-iframe
            $('.video-modal-close-iframe-target')[0].contentWindow.postMessage('{"event":"command","func":"' + 'stopVideo' + '","args":""}', '*');
        }
    });
}
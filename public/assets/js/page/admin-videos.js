$(document).ready(function () {

    $("#admin-nav").ddslick();

    $("#admin-nav .dd-option").click(function () {
        var nav_link = $("#admin-nav .dd-selected-value").val();
        window.location.href = nav_link;
    });

    // Create pagination element with options from form
    var optInit = getOptionsFromForm();
    $("#Pagination").pagination(members.length, optInit);

    // category dropdown
    $("#choose-grade-1").ddslick();
    $("#choose-subject-1").ddslick();

    $('#sort-admin-videos').ddslick();

    //$("#choose-tests-table").tableDnD();

    $("#public_value").ddslick();
    $("#lock_value").ddslick();

});

var items_per_page = 20;

/* define jquery Pagination */
function pageselectCallback(page_index, jq) {
    // Item per page

    var max_elem = Math.min((page_index + 1) * items_per_page, members.length);
    var newcontent = '';

    // Iterate through a selection of the content and build an HTML string
    for (var i = page_index * items_per_page; i < max_elem; i++) {
        newcontent += '<tr class="fs1-1em">';
        newcontent += '<td>';
        if (members[i][0] != "none") {
            newcontent += '<input type="hidden" id="test_video_' + members[i][0] + '" value="' + members[i][2] + '">';
            newcontent += '<a class="video-title" onclick="view_video(' + members[i][0] + ')">' + members[i][1] + '</a>';
        } else {
            newcontent += '<span>' + members[i][1] + '</span>';
        }
        newcontent += '</td>';
        newcontent += '<td>' + members[i][5] + '</td>';
        newcontent += '<td class="hidden-sm-down">' + members[i][4] + '</td>';
        newcontent += '<td><a class="public" onclick="save_public_toggle(this,' + members[i][0] + ')">';
        if (members[i][0] != "none") {
            if (members[i][3] == 1) {
                newcontent += 'Yes';
            } else {
                newcontent += 'No';
            }
        }
        newcontent += '</a></td>';
        newcontent += '<td>';
        if (members[i][0] != "none") {
            newcontent += '<div class="select dd-container" onclick="action_dropdown(this)">';
            newcontent += '<div class="dd-select">';
            newcontent += '<a class="dd-selected">actions</a>';
            newcontent += '<span class="dd-pointer dd-pointer-down"></span>';
            newcontent += '</div>';
            newcontent += '<ul class="dd-options mlneg80">';
            newcontent += '<li><a class="dd-option" onclick="edit_video(this,' + members[i][0] + ')"><label class="dd-option-text">edit</label></a></li>';
            newcontent += '<li><a class="dd-option" onclick="edit_public(this,' + members[i][0] + ')"><label class="dd-option-text">make public</label></a></li>';
            newcontent += '<li><a class="dd-option" onclick="edit_length(this,' + members[i][0] + ')"><label class="dd-option-text">length</label></a></li>';
            newcontent += '<li><a class="dd-option" onclick="delete_video(this,' + members[i][0] + ',' + i + ')"><label class="dd-option-text">delete</label></a></li>';
            newcontent += '<li><a class="dd-option" onclick="lock_account(' + members[i][0] + ')"><label class="dd-option-text">lock account</label></a></li>';
            newcontent += '</ul>';
            newcontent += '</div>';
        }
        newcontent += '</td>';
        newcontent += '</tr>';
    }

    // Replace old content with new content
    $('#choose-tests-table').html(newcontent);

    // Prevent click eventpropagation
    return false;
}

// The form contains fields for many pagiantion optiosn so you can
// quickly see the resuluts of the different options.
function getOptionsFromForm() {
    var opt = {callback: pageselectCallback};
    // Set pagination config
    opt['items_per_page'] = items_per_page;
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

/* drop down event whenever click ddslick drop down */
function action_dropdown(object) {
    if ($($(object).find(".dd-pointer")).hasClass("dd-pointer-up")) {
        $($(object).find(".dd-pointer")).removeClass("dd-pointer-up");
        $($(object).find(".dd-options")).css("display", "none");
        $($(object).find(".dd-options")).removeClass("dd-click-off-close");
    } else {
        if ($($(object).find(".dd-options")).hasClass("dd-click-off-close")) {
            $($(object).find(".dd-options")).removeClass("dd-click-off-close");
        }
        $($(object).find(".dd-pointer")).addClass("dd-pointer-up");
        $($(object).find(".dd-options")).css("display", "block");
        setTimeout(function () {
            $($(object).find(".dd-options")).addClass("dd-click-off-close");
        }, 500);
    }
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
        url: "/ajax/async-video/add",
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

var row_object;
var row_index;

/* get video info event by video id */
function edit_video(object, video_id) {
    row_object = object;
    $("#video_id").val(video_id);
    var type = "video";
    $.ajax({
        url: "/ajax/async-video/get",
        dataType: "json",
        type: "POST",
        data: {type: type, video_id: video_id},
        success: function (data) {
            if (data.result == "success") {
                $("#video_title").val(data.video_title);
                $("#video_link").val(data.video_link);
                $(".edit-modal").bPopup();
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}

/* save video info event by video id when close video edit modal */
function close_editModal() {

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
        $(".edit-modal").bPopup().close();
        var type = "video";
        var video_id = $("#video_id").val();
        $.ajax({
            url: "/ajax/async-video/edit",
            dataType: "json",
            type: "POST",
            data: {type: type, video_id: video_id, video_title: video_title, video_link: video_link},
            success: function (data) {
                if (data.result == "success") {
                    $(row_object).parents("tr").find(".video-title").text(video_title);
                } else if (data.result == "expire") {
                    $('#session-expired-modal').modal();
                }
            }
        });
    }
}

/* get public value event by video id */
function edit_public(object, video_id) {
    row_object = object;
    $("#video_id").val(video_id);
    var type = "public";
    $.ajax({
        url: "/ajax/async-video/get",
        dataType: "json",
        type: "POST",
        data: {type: type, video_id: video_id},
        success: function (data) {
            if (data.result == "success") {
                if (data.public == 1) {
                    $("#public_value .dd-selected-value").val('yes');
                    $("#public_value .dd-selected").text('yes');
                } else {
                    $("#public_value .dd-selected-value").val('no');
                    $("#public_value .dd-selected").text('no');
                }
                $(".public-modal").bPopup();
            } else if (data.result == "expire") {
                $('#session-expired-modal').modal();
            }
        }
    });
}

/* update public value event by video id */
function save_public() {

    var type = "public";
    var video_id = $("#video_id").val();
    var public_value = $("#public_value .dd-selected-value").val();
    if (public_value == "yes")
        public_value = 1;
    else
        public_value = 0;

    $(".public-modal").bPopup().close();
    $.ajax({
        url: "/ajax/async-video/edit",
        dataType: "json",
        type: "POST",
        data: {type: type, video_id: video_id, public_value: public_value},
        success: function (data) {
            if (data.result == "success") {
                if (public_value == 1) {
                    $(row_object).parents("tr").find(".public").text("Yes");
                } else {
                    $(row_object).parents("tr").find(".public").text("No");
                }
            } else if (data.result == "expire") {
                $('#session-expired-modal').modal();
            }
        }
    });

}

/* update public value event by video id */
function save_public_toggle(object, video_id) {

    var type = "public";
    var public_value = $(object).text();
    if (public_value == "Yes")
        public_value = 0;
    else
        public_value = 1;

    $.ajax({
        url: "/ajax/async-video/edit",
        dataType: "json",
        type: "POST",
        data: {type: type, video_id: video_id, public_value: public_value},
        success: function (data) {
            if (data.result == "success") {
                if (public_value == 1) {
                    $(object).text("Yes");
                } else {
                    $(object).text("No");
                }
            } else if (data.result == "expire") {
                $('#session-expired-modal').modal();
            }
        }
    });

}

/* get video length event by video id */
function edit_length(object, video_id) {
    row_object = object;
    $("#video_id").val(video_id);
    var type = "length";
    $.ajax({
        url: "/ajax/async-video/get",
        dataType: "json",
        type: "POST",
        data: {type: type, video_id: video_id},
        success: function (data) {
            if (data.result == "success") {
                $("#length_minutes").val(data.minutes);
                $("#length_seconds").val(data.seconds);
                $(".length-modal").bPopup();
            } else if (data.result == "expire") {
                $('#session-expired-modal').modal();
            }
        }
    });
}

/* update video length event by video id */
function save_length() {

    var video_id = $("#video_id").val();
    var minutes = $("#length_minutes").val();
    var seconds = $("#length_seconds").val();

    if (minutes == "") {
        minutes = 0;
    }
    if (seconds == "") {
        seconds = 0;
    }
    var length = 60 * parseInt(minutes) + parseInt(seconds);

    $(".length-modal").bPopup().close();
    var type = "length";
    $.ajax({
        url: "/ajax/async-video/edit",
        dataType: "json",
        type: "POST",
        data: {type: type, video_id: video_id, length: length},
        success: function (data) {
            if (data.result == "success") {

            } else if (data.result == "expire") {
                $('#session-expired-modal').modal();
            }
        }
    });
}

/* delete video confirm popup */
function delete_video(object, video_id, index) {
    $("#video_id").val(video_id);
    row_object = object;
    row_index = index;
    $(".delete-modal-wrapper").bPopup();
}

/* delete video event after confirmed */
function delete_confirm() {
    $(".delete-modal-wrapper").bPopup().close();
    var video_id = $("#video_id").val();
    var type = "video";
    $.ajax({
        url: "/ajax/async-video/delete",
        dataType: "json",
        type: "POST",
        data: {type: type, video_id: video_id},
        success: function (data) {
            if (data.result == "success") {
                members.splice(row_index, 1);
                $(row_object).parents("tr").remove();
                var rows = $("#choose-tests-table").find("tr");
                if (rows.length == 0) {
                    $("#choose-tests-table").html('<tr class="fs1-1em"><td colspan="5"><span>No videos</span></td></tr>');
                }
                return;
            } else if (data.result == "expire") {
                $('#session-expired-modal').modal();
            }
        }
    });
}

/* lock video modal popup */
function lock_account(video_id) {

    $("#video_id").val(video_id);
    $("#lock_value .dd-selected-value").val('1');
    $("#lock_value .dd-selected").text('no');
    $(".user-lock-modal").bPopup();
}

/* video lock event by video id */
function save_lock() {

    $(".user-lock-modal").bPopup().close();
    var type = "lock_account";
    var video_id = $("#video_id").val();
    var lock_value = $("#lock_value .dd-selected-value").val();

    $.ajax({
        url: "/ajax/async-video/edit",
        dataType: "json",
        type: "POST",
        data: {type: type, video_id: video_id, lock_value: lock_value},
        success: function (data) {
            if (data.result == "success") {

            } else if (data.result == "expire") {
                $('#session-expired-modal').modal();
            }
        }
    });
}

/* get videos by filter type */
function filter_category(num) {

    if (num == 1) {
        var filter_type = $("#choose-grade-1 .dd-selected-value").val();
        var subject_id = $("#choose-subject-1 .dd-selected-value").val();
    } else if (num == 0) {
        var filter_type = "";
        var subject_id = "";
        $("#choose-subject-1 .dd-selected-value").val('');
        $("#choose-subject-1 .dd-selected").text('choose subject');
        $("#choose-grade-1 .dd-selected-value").val('');
        $("#choose-grade-1 .dd-selected").text('choose grade');
    }

    var type = "admin_category";
    $.ajax({
        url: "/ajax/async-video/get",
        dataType: "json",
        type: "POST",
        data: {type: type, filter_type: filter_type, subject_id: subject_id},
        success: function (data) {
            if (data.result == "success") {
                members = [];
                if (data.video) {
                    for (var i = 0; i < data.video.length; i++) {
                        members.push([data.video[i].video_id, data.video[i].video_title, data.video[i].video_link, data.video[i].public, data.video[i].submitted_time, data.video[i].grade_subject]);
                    }
                } else {
                    members.push(['none', 'No videos in this category.', '', '', '', '']);
                }
                var opt = getOptionsFromForm();
                // Re-create pagination content with new parameters
                $("#Pagination").pagination(members.length, opt);
                //$("#choose-tests-table").tableDnD();
            } else if (data.result == "expire") {
                $('#session-expired-modal').modal();
            }
        }
    });

}

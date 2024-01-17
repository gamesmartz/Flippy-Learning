$(document).ready(function() {
    // Create pagination element with options from form
    var optInit = getOptionsFromForm();
    $("#Pagination").pagination(members.length, optInit);

});

var alreadyExistsInQueue = new bootstrap.Modal(document.getElementById('already-exists-in-queue'), {
    keyboard: false
});

// adds a yellow class, then removes that class
function highlightElement() {

    $('.table-tests tr').addClass("highlight");
    // call after 500ms
    setTimeout(function() {
        $('.table-tests tr').removeClass('highlight');
    }, 500);
}

var items_per_page = 100;


// The form contains fields for many pagiantion optiosn so you can
// quickly see the resuluts of the different options.
function getOptionsFromForm() {
    var opt = { callback: pageselectCallback };
    // Set pagination config
    opt['items_per_page'] = items_per_page;
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

function pageselectCallback(page_index, jq) {
    // Item per page

    var newcontent = '';

    // if a new member and first element of members array is equal to 'none', as set on the php page
    if (members[0][0] == 'none') {

        newcontent += '<tr>';
        newcontent += '<td class="text-center pt-5 border-0">';
        newcontent += '<button class="btn btn-primary btn-app-secondary">Choose Your Grade Above</button>';
        newcontent += '</td>';
        newcontent += '</tr>';
        $('.bg-secondary').hide();
    }
    // else it is not a new user, and there is a subject and grade already choosen from before and pulled from the database under 'choose_history'
    else {
        $('.bg-secondary').show();
        var max_elem = Math.min((page_index + 1) * items_per_page, members.length);

        // Iterate through a selection of the content and build an HTML string
        for (var i = page_index * items_per_page; i < max_elem; i++) {

            newcontent += '<tr class="choose-test-rows">';

            newcontent += '<td class="text-left" style="width: 100px;">';
            newcontent += '<a href="chapter?chapter=' + members[i][0] + '">';
            newcontent += '<span class="btn btn-sm btn-primary btn-app-secondary">' + test_name_before_colon(members[i][1]) + '</span>';
            newcontent += '</a>';
            newcontent += '</td>';


            newcontent += '<td class="text-left">';
            newcontent += '<a class="text-dark text-decoration-none" href="chapter?chapter=' + members[i][0] + '">' + test_name_after_colon(members[i][1]) + '</a>';
            newcontent += '</td>';


            newcontent += '<td class="text-center in-que" style="width: 120px;">';

            if (members[i][0] != "none") {
                if (mastered_overflow) {
                    if (members[i][7] == "In Queue") {
                        newcontent += '<a role="button" onclick="remove_que(this,' + members[i][0] + ')">' + members[i][7] + '</a>';
                    } else {
                        newcontent += '<a role="button" onclick="add_que(this,' + members[i][0] + ')">' + members[i][7] + '</a>';
                    }
                } else {
                    if (members[i][2] != "Mastered" && members[i][9] != 1) {
                        if (members[i][7] == "In Queue") {
                            newcontent += '<a role="button" onclick="remove_que(this,' + members[i][0] + ')">' + members[i][7] + '</a>';
                        } else {
                            newcontent += '<a role="button" onclick="add_que(this,' + members[i][0] + ')">' + members[i][7] + '</a>';
                        }
                    }
                }
            }
            newcontent += '</td>';


            // not using Mastery info right now
            // newcontent += '<td class="left-align progress-info th-width-nowrap">';
            // if (subscription && members[i][0] != "none" && members[i][10] == 1) {
            //     newcontent += '<a href="reports?id=' + members[i][0] + '">' + members[i][2] + '</a>';
            // } else {
            //     newcontent += members[i][2];
            // }
            // newcontent += '</td>';
            // not using video right now
            // newcontent += '<td class="left-align watch-learn">';
            // if (members[i][0] != "none" && members[i][11] != 0) {
            //     newcontent += '<a href="watch-learn.php?id=' + members[i][0] + '">';
            //     newcontent += '<img src="/assets/images/gs-play-sm.png">';
            //     newcontent += '</a>';
            // }
            // newcontent += '</td>';


            newcontent += '<td class="dropdown text-center" style="width: 120px;">';
            if (members[i][0] != "none") {
                newcontent += '<a class="nav-link text-muted fw-bold dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false">Actions</a>';
                if (filter_type == "personal") {
                    newcontent += '<ul class="dropdown-menu">';
                    newcontent += '<li><a class="dropdown-item" role="button" onclick="add_que(this,' + members[i][0] + ')">add to queue</a></li>';
                    if (members[i][4] == "multiple") {
                        newcontent += '<li><a class="dropdown-item" role="button" href="create-test-multiple-choice.php?id=' + members[i][0] + '">edit test</a></li>';
                    } else if (members[i][4] == "fill") {
                        newcontent += '<li><a class="dropdown-item" role="button" href="create-test-fill-in-the-blank.php?id=' + members[i][0] + '">edit test</a></li>';
                    } else if (members[i][4] == "spelling") {
                        newcontent += '<li><a class="dropdown-item" role="button" href="create-test-spelling.php?id=' + members[i][0] + '">edit test</a></li>';
                    }
                    newcontent += '<li><a class="dropdown-item" role="button" onclick="delete_test(this,' + members[i][0] + ',' + i + ')">delete test</a></li>';
                    if (user_role != 2) {
                        newcontent += '<li><a class="dropdown-item" role="button" onclick="public_review(this,' + members[i][0] + ',' + i + ')">submit your test to teach others!</a></li>';
                    }
                    newcontent += '</ul>';
                } else if (filter_type == "history") {
                    newcontent += '<ul class="dropdown-menu">';
                    newcontent += '<li><a class="dropdown-item" role="button" onclick="add_que(this,' + members[i][0] + ')">add to queue</a></li>';
                    newcontent += '</ul>';
                } else {
                    newcontent += '<ul class="dropdown-menu">';
                    newcontent += '<li><a class="dropdown-item" role="button" onclick="add_que(this,' + members[i][0] + ')">add to queue</a></li>';
                    newcontent += '</ul>';
                }
            }
            newcontent += '</td>';

            newcontent += '</tr>';
        }
    }

    // Replace old content with new content
    $('#choose-tests-table').html(newcontent);

    // Prevent click eventpropagation
    return false;
}


/* get tests event by filter type */
function filter_category() {

    // not used
    // var session_token = $("#session_token").val();

    var subject_id = $("#choose-subject-id").val();
    var filter_type = $("#choose-grade-id").val();

    var type = "category";
    $.ajax({
        url: "ajax/async-tests.php?action=get",
        dataType: "json",
        type: "POST",
        data: { type: type, filter_type: filter_type, subject_id: subject_id },
        success: function(data) {
            if (data.result == "success") {
                members = [];
                if (data.test) {
                    for (var i = 0; i < data.test.length; i++) {
                        var info = '';
                        if (data.test[i].post_test >= data.test[i].question_length) {
                            var temp_time = data.test[i].post_complete_time + time_difference;
                            temp_time = new Date(temp_time);
                            temp_time = (temp_time.getMonth() + 1).toString() + "-" + temp_time.getDate().toString() + "-" + temp_time.getFullYear().toString().substr(2, 2);
                            info = 'Post-Test (' + temp_time + ') ' + data.test[i].correct_post_test + '/' + data.test[i].question_length + ' ' + Math.round(100 * data.test[i].correct_post_test / data.test[i].question_length) + '%';
                        } else if (data.test[i].pre_test >= data.test[i].question_length) {
                            var temp_time = data.test[i].pre_complete_time + time_difference;
                            temp_time = new Date(temp_time);
                            temp_time = (temp_time.getMonth() + 1).toString() + "-" + temp_time.getDate().toString() + "-" + temp_time.getFullYear().toString().substr(2, 2);
                            info = 'Pre-Test (' + temp_time + ') ' + data.test[i].correct_pre_test + '/' + data.test[i].question_length + ' ' + Math.round(100 * data.test[i].correct_pre_test / data.test[i].question_length) + '%';
                        }
                        members.push([data.test[i].test_id, data.test[i].test_name, info, data.test[i].video_link, data.test[i].test_type, data.test[i].attachment, data.test[i].grade_subject, data.test[i].in_que, data.test[i].view_video, data.test[i].in_game_mastered, data.test[i].result_exist, data.test[i].videos_number]);
                    }
                } else {
                    if (subject_id == "") {
                        members.push(['none', 'Choose tests above.', '', '', '', '', '', '', '', '', '', '']);
                    } else {
                        members.push(['none', 'No tests in this category.', '', '', '', '', '', '', '', '', '', '']);
                    }
                }
                var opt = getOptionsFromForm();
                // Re-create pagination content with new parameters
                $("#Pagination").pagination(members.length, opt);
                //$("#choose-tests-table").tableDnD();

                // adds the highlight effect after the table elements have rendered
                highlightElement();

            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });

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
        setTimeout(function() {
            $($(object).find(".dd-options")).addClass("dd-click-off-close");
        }, 500);
    }
}

/* add test to que  */
function add_que(object, test_id) {
    var session_token = $("#session_token").val();
    var type = "que";
    $.ajax({
        url: "ajax/async-tests.php?action=add",
        dataType: "json",
        type: "POST",
        data: { type: type, test_id: test_id, session_token: session_token },
        error: function(xhr) {
            //console.log(xhr.responseText);
        },
        success: function(data) {

            if (data.result == "success") {
                // console.log(data);

                $("#que_menu").text("QUE (" + data.que + ")");
                if (data.mastered_flag == "success") {
                    //mastered_overflow = false;
                    filter_category();
                } else {
                    //$(object).parent("div").addClass("in-que-row");
                    $(object).parents(".choose-test-rows").children(".in-que").html('<a role="button" onclick="remove_que(this,' + test_id + ')">In Queue</a>');



                }
            } else if (data.result == "failed") {

                // alert("This already exist in your queue!");
                //console.log("This already exist in your queue!");

                alreadyExistsInQueue.show();

            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}

/* remove que event by test id */
function remove_que(object, test_id) {
    // not used
    // var session_token = $("#session_token").val();

    var type = "que";
    $.ajax({
        url: "ajax/async-tests.php?action=delete",
        dataType: "json",
        type: "POST",
        data: { type: type, test_id: test_id },
        error: function(xhr) {
            // console.log(xhr.responseText);
        },
        success: function(data) {
            if (data.result == "success") {
                //  console.log(data);
                $("#que_menu").text("QUE (" + data.que + ")");
                $(object).parents(".choose-test-rows").children(".in-que").html('<a role="button" onclick="add_que(this,' + test_id + ')">Add to Queue</a>');
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}

/* get video link event by test id */
function view_video(object, test_id) {
    _object = object;
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
            url: "ajax/async-tests.php?action=add",
            dataType: "json",
            type: "POST",
            data: { type: type, test_id: test_id },
            success: function(data) {
                if (data.result == "success") {
                    return;
                } else if (data.result == "expire") {
                    $('.expire-modal-wrapper').bPopup();
                }
            }
        });
    }
}

var _object;
var _index;

/* public review popup */
function public_review(object, test_id, index) {
    $(".review-modal-wrapper").bPopup();
    $("#test_id").val(test_id);
    _object = object;
    _index = index;
}

/* update public_review event by test id */
function review_confirm() {
    $(".review-modal-wrapper").bPopup().close();
    var test_id = $("#test_id").val();
    var type = "public_review";
    $.ajax({
        url: "ajax/async-tests.php?action=edit",
        dataType: "json",
        type: "POST",
        data: { type: type, test_id: test_id },
        success: function(data) {
            if (data.result == "success") {
                members.splice(_index, 1);
                $(_object).parents("tr").remove();
                var rows = $("#choose-tests-table").find("tr");
                if (rows.length == 0) {
                    $("#choose-tests-table").html('<tr class="fs1-1em"><td colspan="6"><span>Make Some Personal Tests to View Them Here</span></td></tr>');
                }
                return;
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}

/* delete test modal popup */
function delete_test(object, test_id, index) {
    $(".delete-modal-wrapper").bPopup();
    $("#test_id").val(test_id);
    _object = object;
    _index = index;
}

/* confirmed delete test event by test id  */
function delete_confirm() {
    $(".delete-modal-wrapper").bPopup().close();
    var test_id = $("#test_id").val();
    var type = "test";
    $.ajax({
        url: "ajax/async-tests.php?action=delete",
        dataType: "json",
        type: "POST",
        data: { type: type, test_id: test_id },
        success: function(data) {
            if (data.result == "success") {
                members.splice(_index, 1);
                $(_object).parents("tr").remove();
                var rows = $("#choose-tests-table").find("tr");
                if (rows.length == 0) {
                    $("#choose-tests-table").html('<tr class="fs1-1em"><td colspan="6"><span>No Tests in this Category</span></td></tr>');
                }
                return;
            } else if (data.result == "expire") {
                $('#session-expired-modal').modal();
            }
        }
    });
}

/* youtube modal popup */
function submit_play() {
    $(".review-modal-wrapper").bPopup().close();
    $(".youtube-modal-wrapper").bPopup();
}
/* reset progress event by test id: first show confirm popup */

function reset_progress(object, test_id) {
    $(".reset-progress-modal").bPopup();
    $("#test_id").val(test_id);
    _object = object;
}

function close_AlreadyExistModal() {
    alreadyExistsInQueue.hide();
}

/* close resetModal window and reset confirmed event */
function close_resetModal() {
    $(".reset-progress-modal").bPopup().close();

    var test_id = $("#test_id").val();
    var type = "reset_progress";
    $.ajax({
        url: "ajax/async-tests.php?action=add",
        dataType: "json",
        type: "POST",
        data: { type: type, test_id: test_id },
        success: function(data) {
            if (data.result == "success") {
                $(_object).parents("tr").find(".progress-info").html(data.progress);
                $(_object).parents("tr").removeClass("mastered-row");
            } else if (data.result == "expire") {
                $('#session-expired-modal').modal();
            }
        }
    });

}
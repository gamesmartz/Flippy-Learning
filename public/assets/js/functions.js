// var b = document.documentElement;
// b.setAttribute('data-useragent',  navigator.userAgent);
// b.setAttribute('data-platform', navigator.platform);


///////////////////////// DDSLICK SETUPS ///////////////////////// DDSLICK SETUPS ///////////////////////// DDSLICK SETUPS

// remap jQuery to $
(function ($) {

    /* trigger when page is ready */
    $(document).ready(function () {

        // closing dropdown menu
        $(document).on('click touchstart', function (elem) {
            if (elem.target.className == 'dd-option-text') {
                return;
            }
            $('.dd-click-off-close').css('display', 'none');
        });

        // show login pop-up when sign in link is clicked
        $('#signin-text a, .startbtn').click(function (e) {
            if (window.location.search != "") {
                window.location.href = "login.php" + window.location.search;
            } else {
                window.location.href = "login.php";
            }
        });

        // show youtube css pop-up
        $('.modal-css a').click(function (e) {
            if (e.currentTarget.innerHTML.indexOf("not-watched") != -1) {
                if (typeof page_name !== 'undefined') {
                    $.ajax({
                        url: "/ajax/async-updateUser/watch_history",
                        dataType: "json",
                        type: "POST",
                        data: {page_name: page_name},
                        success: function (data) {
                            if (data.result == "success") {
                                $(".watched-txt").removeClass("not-watched-txt");
                                $(".watched-button").removeClass("not-watched-button");
                            }
                        }
                    });
                }
            }
            $(".youtube-modal").find("iframe").attr("src", $(this).find(".video_src").val());
            $(document.body).addClass('modal-open');
            $(".youtube-modal").css("display", "block");
        });

        $('.playvid-wrapper-general a').click(function (e) {
            $('.youtube-modal-wrapper').bPopup();
        });

        $('#how-to-save-test-video').click(function (e) {
            $('.save-test-video-modal-wrapper').bPopup();
        });

        $('#learn-subject-anchor').click(function (e) {
            if ($(".learn-subject-modal-wrapper").find("iframe").attr("src") != "") {
                $('.learn-subject-modal-wrapper').bPopup();
            }
        });

        $('#sort-tests').ddslick();
        $('#view-test').ddslick();
        $('.select').ddslick();
        $('.select-green').ddslick();
        $('.mastery-type').ddslick();
        /*
         $('.table-tests tr td span').mouseover(function(e) {
         $($(e.target).parent('td').find('.tooltip-testinfo')).fadeIn(100);
         });
         $('.table-tests tr td span').mouseleave(function(e) {
         $($(e.target).parent('td').find('.tooltip-testinfo')).fadeOut(250);
         });
         */
        $('.tooltip').tooltipsy();

        $('.btn-file').click(function (e) {
            var file_el_id = $(this).data('file');
            $(file_el_id).click();
        });

        /*
         close the file size error dialog
         */
        $('.graphics-file-size-error-close').click(function (e) {
            $('.graphics-file-size-error').bPopup().close();
        });

        /*
         handles the opening of correct video size format
         */
        $('.correct-size-image').click(function (e) {
            $('.graphics-file-size-error').bPopup().close();
            $('.correct-size-image-video').bPopup();
        });

        /*
         Check attach size
         */
        $('.input-file-graphics').change(function (e) {
            if (typeof FileReader !== "undefined") {
                $('.input-file-graphics').each(function (index, element) {
                    try {
                        var file = document.getElementsByClassName('input-file-graphics').item(index).files[0];
                        // check file size
                        var size = parseInt(file.size / 1024);
                        var type = file.type;

                        if (typeof size !== 'NaN') {
                            if (size > 300 || (type !== 'image/jpeg' && type !== 'image/png')) {
                                e.preventDefault();
                                $('.graphics-file-size-error').bPopup();
                            }
                        }
                    }
                    catch (e) {

                    }
                });
            }
        });

        $(".modal-back").click(function () {
            $(".modal").css("display", "none");
            $(document.body).removeClass('modal-open');
            if ($(this).parents(".modal").hasClass("youtube-modal")) {
                $(this).parents(".modal").find("iframe").attr("src", "");
            }
        });

        // kill sound when closing video
        $(".modal-close").click(function () {
            $(".modal").css("display", "none");
            $(document.body).removeClass('modal-open');
            if ($(this).parents(".modal").hasClass("youtube-modal") || $(this).parents(".modal").hasClass("youtube-modal-margin-left")) {
                $(this).parents(".modal").find("iframe").attr("src", "");
            }
        });

    });


    /* optional triggers
     $(window).load(function() {
     });
     $(window).resize(function() {
     });
     */

})(window.jQuery);

var grade_value;

/* different event whenever click ddslick dropdown; different action on id values */
function select_slick(object) {
    var id = object.id;
    /* check ddslick id */
    if (id == "choose-sub-category-1") {
        var content = $("#choose-sub-category-1 .dd-options").html();
        if (content == "") {
            $("#choose-sub-category-1 .dd-options").html('<li><a class="dd-option">Please Choose Subject</a></li>');
        }
    } else if (id == "choose-subject-1") {
        var subject = $(object).find(".dd-selected-value").val();
        if (subject != "") {
            var type = "sub-category";
            $.ajax({
                url: "/ajax/async-category/get",
                dataType: "json",
                type: "POST",
                data: {type: type, subject: subject},
                success: function (data) {
                    var sub_category = data.sub_category;
                    var content = '<select name="sub-category-1" id="choose-sub-category-1" data-desc="sub-category">';
                    if (sub_category) {
                        for (var i = 0; i < sub_category.length; i++) {
                            content += '<option value="' + sub_category[i].id + '">' + sub_category[i].name + '</option>';
                        }
                    }
                    content += '</select>';
                    $("#sub-category-wrapper").html(content);
                    $("#choose-sub-category-1").ddslick();
                }
            });
        }

        var content = $("#choose-subject-1 .dd-options").html();
        if (content == "") {
            $("#choose-subject-1 .dd-options").html('<li><a class="dd-option">Please Choose Grade</a></li>');
        }

    } else if (id == "choose-grade-1") {
        $("#choose-sub-category-1 .dd-selected").html('sub-category');
        if (grade_value != $(object).find(".dd-selected-value").val()) {
            grade_value = $(object).find(".dd-selected-value").val();
            var grade = grade_value;
            if (grade != "") {
                var type = "subject";
                $.ajax({
                    url: "/ajax/async-category/get",
                    dataType: "json",
                    type: "POST",
                    data: {type: type, grade: grade},
                    success: function (data) {
                        var subject = data.subject;
                        var content = '<select name="subject-1" id="choose-subject-1" data-desc="choose subject">';
                        if (subject) {
                            if (object.className == "choose-test-grade dd-container") {
                                content += '<option value="all">All</option>';
                            }
                            for (var i = 0; i < subject.length; i++) {
                                content += '<option value="' + subject[i].subject_id + '">' + subject[i].subject_name + '</option>';
                            }
                        }
                        content += '</select>';
                        // dump new html content
                        $("#subject-1-wrapper").html(content);

                        // reattach ddslick method
                        $("#choose-subject-1").ddslick({
                            onSelected: function (data) {
                               // console.log(data);
                               filter_category();
                            }
                        });
                        if (object.className == "choose-test-grade dd-container") {
                            $("#choose-subject-1 .dd-selected-value").val('all');
                            $("#choose-subject-1 .dd-selected").text('All');
                        }
                        // reset sub-category
                        $("#choose-sub-category-1 .dd-options").html('<li><a class="dd-option">Please Choose Subject</a></li>');
                        $("#choose-sub-category-1 .dd-selected-value").val("");
                    }
                });
            }
        }
    } else if (id == "choose-grade-3") {
        var grade = $(object).find(".dd-selected-value").val();
        if (grade != "") {
            var type = "subject";
            $.ajax({
                url: "/ajax/async-category/get",
                dataType: "json",
                type: "POST",
                data: {type: type, grade: grade},
                success: function (data) {
                    var subject = data.subject;
                    var content = '<select name="subject-2" id="choose-subject-2" data-desc="subject">';
                    if (subject) {
                        for (var i = 0; i < subject.length; i++) {
                            content += '<option value="' + subject[i].subject_id + '">' + subject[i].subject_name + '</option>';
                        }
                    }
                    content += '</select>';
                    $("#subject-2-wrapper").html(content);
                    $("#choose-subject-2").ddslick();
                }
            });
        }
    } else if (id == "level-points") {
        var level = $(object).find(".dd-selected-value").val();
        if (level != "") {
            var type = "level_points";
            $.ajax({
                url: "/ajax/async-config/get",
                dataType: "json",
                type: "POST",
                data: {type: type, level: level},
                success: function (data) {
                    if (data.result = "success") {
                        $("#level-points-value").val(data.value);
                        return;
                    } else {
                        $("#level-points-value").val("");
                        return;
                    }
                }
            });
        }
    } else if (id == "professor-points") {
        var level = $(object).find(".dd-selected-value").val();
        if (level != "") {
            var type = "professor_points";
            $.ajax({
                url: "/ajax/async-config/get",
                dataType: "json",
                type: "POST",
                data: {type: type, level: level},
                success: function (data) {
                    if (data.result = "success") {
                        $("#professor-points-value").val(data.value);
                        return;
                    } else {
                        $("#professor-points-value").val("");
                        return;
                    }
                }
            });
        }
    } else if (id == "sort-tests") {
        var subject_id = $("#choose-subject-1 .dd-selected-value").val();
        var sort = $(object).find(".dd-selected-value").val();
        $(object).find(".dd-selected-value").val("");
        $(object).find(".dd-selected").html("Sort");
        if (sort != "" && subject_id != "") {
            var type = "sort";
            filter_type = $("#choose-grade-1 .dd-selected-value").val();

            $.ajax({
                url: "/ajax/async-tests.php/get",
                dataType: "json",
                type: "POST",
                data: {type: type, sort: sort, filter_type: filter_type, subject_id: subject_id},
                success: function (data) {
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
                            members.push(['none', 'No tests in this category.', '', '', '', '', '', '', '', '', '', '']);
                        }
                        var opt = getOptionsFromForm();
                        // Re-create pagination content with new parameters
                        $("#Pagination").pagination(members.length, opt);
                        //$("#choose-tests-table").tableDnD();
                    } else if (data.result == "expire") {
                        $('.expire-modal-wrapper').bPopup();
                    }
                }
            });
        }
    } else if (id == "sort-admin-tests") {
        var subject_id = $("#choose-subject-1 .dd-selected-value").val();
        var sort = $(object).find(".dd-selected-value").val();
        $(object).find(".dd-selected-value").val("");
        $(object).find(".dd-selected").html("Sort");
        if (sort != "") {
            var type = "sort-admin";
            var filter_type = $("#choose-grade-1 .dd-selected-value").val();
            $.ajax({
                url: "/ajax/async-tests/get",
                dataType: "json",
                type: "POST",
                data: {type: type, sort: sort, filter_type: filter_type, subject_id: subject_id},
                success: function (data) {
                    if (data.result == "success") {
                        members = [];
                        if (data.test) {
                            for (var i = 0; i < data.test.length; i++) {
                                members.push([data.test[i].test_id, data.test[i].test_name, data.test[i].user_email, data.test[i].popularity, data.test[i].public, data.test[i].test_type, data.test[i].attachment, data.test[i].created_time, data.test[i].grade_subject, data.test[i].question_num, data.test[i].school_year, data.test[i].video_link]);
                            }
                        } else {
                            members.push(['none', 'No tests in this category.', '', '', '', '', '', '', '', '', '', '']);
                        }
                        var opt = getOptionsFromForm();
                        // Re-create pagination content with new parameters
                        $("#Pagination").pagination(members.length, opt);
                        //$("#choose-tests-table").tableDnD();
                    } else if (data.result == "expire") {
                        $('.expire-modal-wrapper').bPopup();
                    }
                }
            });
        }
    } else if (id == "sort-admin-videos") {
        var subject_id = $("#choose-subject-1 .dd-selected-value").val();
        var sort = $(object).find(".dd-selected-value").val();
        $(object).find(".dd-selected-value").val("");
        $(object).find(".dd-selected").html("Sort");
        if (sort != "") {
            var type = "sort-admin-videos";
            var filter_type = $("#choose-grade-1 .dd-selected-value").val();
            $.ajax({
                url: "/ajax/async-video/get",
                dataType: "json",
                type: "POST",
                data: {type: type, sort: sort, filter_type: filter_type, subject_id: subject_id},
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
                        $('.expire-modal-wrapper').bPopup();
                    }
                }
            });
        }
    } else if (id == "view-test") {
        var view_num = $(object).find(".dd-selected-value").val();
        if (view_num != "") {
            items_per_page = parseInt(view_num);
            var opt = getOptionsFromForm();
            // Re-create pagination content with new parameters
            $("#Pagination").pagination(members.length, opt);
            //$("#choose-tests-table").tableDnD();
        }
    } else if (id == "sort-personal") {
        var sort = $(object).find(".dd-selected-value").val();
        $(object).find(".dd-selected-value").val("");
        $(object).find(".dd-selected").html("Sort");
        if (sort != "") {
            var type = "sort-personal";
            $.ajax({
                url: "/ajax/async-tests/get",
                dataType: "json",
                type: "POST",
                data: {type: type, sort: sort},
                success: function (data) {
                    if (data.result == "success") {
                        members = [];
                        if (data.test) {
                            for (var i = 0; i < data.test.length; i++) {
                                members.push([data.test[i].test_id, data.test[i].test_name, data.test[i].test_info, data.test[i].video_link, data.test[i].test_type, data.test[i].attachment]);
                            }
                        } else {
                            members.push(['none', 'No tests.', '', '', '', '']);
                        }
                        var opt = getOptionsFromForm();
                        // Re-create pagination content with new parameters
                        $("#Pagination").pagination(members.length, opt);
                        //$("#choose-tests-table").tableDnD();
                    } else if (data.result == "expire") {
                        $('.expire-modal-wrapper').bPopup();
                    }
                }
            });
        }
    } else if (id == "sort-level") {
        var sort = $(object).find(".dd-selected-value").val();
        $(object).find(".dd-selected-value").val("");
        $(object).find(".dd-selected").html("Sort");
        if (sort != "") {
            var type = "sort-level";
            $.ajax({
                url: "/ajax/async-tests/get",
                dataType: "json",
                type: "POST",
                data: {type: type, sort: sort},
                success: function (data) {
                    if (data.result == "success") {
                        members = [];
                        if (data.test) {
                            for (var i = 0; i < data.test.length; i++) {
                                members.push([data.test[i].test_id, data.test[i].test_name, data.test[i].test_info, data.test[i].video_link, data.test[i].attachment]);
                            }
                        } else {
                            members.push(['none', 'No tests.', '', '', '']);
                        }
                        var opt = getOptionsFromForm();
                        // Re-create pagination content with new parameters
                        $("#Pagination").pagination(members.length, opt);
                        //$("#choose-tests-table").tableDnD();
                    } else if (data.result == "expire") {
                        $('.expire-modal-wrapper').bPopup();
                    }
                }
            });
        }
    } else if (id == "sort-users") {
        var sort = $(object).find(".dd-selected-value").val();
        $(object).find(".dd-selected-value").val("");
        $(object).find(".dd-selected").html("Sort");
        if (sort != "") {
            var type = "sort-users";
            $.ajax({
                url: "/ajax/async-updateUser/get",
                dataType: "json",
                type: "POST",
                data: {type: type, sort: sort},
                success: function (data) {
                    if (data.result == "success") {
                        members = [];
                        if (data.user) {
                            for (var i = 0; i < data.user.length; i++) {
                                members.push([data.user[i].user_id, data.user[i].user_name, data.user[i].user_email, data.user[i].login_time, data.user[i].user_registered_time, data.user[i].level, data.user[i].subscription, data.user[i].total_points]);
                            }
                        } else {
                            members.push(['none', 'No users.', '', '', '', '', '', '']);
                        }
                        var opt = getOptionsFromForm();
                        // Re-create pagination content with new parameters
                        $("#Pagination").pagination(members.length, opt);
                        //$("#choose-tests-table").tableDnD();
                    }
                }
            });
        }
    } else if (object.className == "gs-dropdown dd-container") {
        var test_id = object.id;
        test_id = test_id.replace("dropdown_", "");

        var action = $(object).find(".dd-selected-value").val();
        $(object).find(".dd-selected").html("actions");
        $(object).find(".dd-selected-value").val("");
        if (action != "") {
            if (action == "move to top") {
                move_to_top(test_id);
            } else if (action == "reset test progress") {
                reset_progress(object, test_id);
            }
        }
    }

}

function start_drag() {

    setTimeout(function () {
        //$("#choose-tests-table").tableDnD();
    }, 500);

}

var format_flag = true;
/* set answer object as zero */
function format_answer(object, e) {
    var key = e.keyCode;

    var answer = $(object).val();
    var unformat = numeral().unformat(answer);
    if (answer != "") {
        if (answer.replace(/,/g, "") == unformat.toString()) {
            var arr = unformat.toString().split('.');
            if (arr[1]) {
                var formating = '0,0.[' + '0'.repeat(arr[1].length) + ']';
                answer = numeral(unformat).format(formating);
            } else {
                answer = numeral(unformat).format('0,0');
            }
            $(object).val(answer);
        }
    }
}

// # href smooth scrolling

$(document).ready(function () {
    $('.smooth_scroll').click(function () {
        $('html, body').animate({
            scrollTop: $($(this).attr('href')).offset().top
        }, 1500);
        return false;
    });
});

// filter summary
function filter_summary(summary) {
    var stamp = summary[3] + time_difference;
    return stamp >= beginningOfDay;
}

// sort summary (ascending)
/* I don't think this function is used */
function sort_summary(a, b) {
    return a[3] - b[3];
}

// report summary
function report_summary(summary) {
    var stamp = summary[3] + time_difference;
    var date = new Date(stamp);
    var date_string = (date.getMonth() + 1).toString() + "-" + date.getDate().toString() + "-" + date.getFullYear().toString().substr(2, 2);
    if (date_string == report_date) {
        if (temp_index == -1) {
            temp_index = summary[6];
        }
        return true;
    } else {
        return false;
    }
}

var increase_index = -1;
// view summary
/* I don't think this function is used */
function view_summary(summary) {
    var stamp = summary[3] + time_difference;
    var date = new Date(stamp);
    var date_string = (date.getMonth() + 1).toString() + "-" + date.getDate().toString() + "-" + date.getFullYear().toString().substr(2, 2);
    if (date_string == report_date) {
        increase_index++;
        if (stamp == index_stamp) {
            current_member = increase_index;
        }
        return true;
    } else {
        return false;
    }
}

// members map
/* I don't think this function is used  */
function member_map(member) {
    if (typeof questions[member[0]] !== 'undefined') {
        member[3] = questions[member[0]][0];
        member[4] = questions[member[0]][1];
        member[5] = questions[member[0]][2];
        if (test_type == "multiple") {
            member[6] = questions[member[0]][3];
            member[7] = questions[member[0]][4];
            member[8] = questions[member[0]][5];
            member[9] = questions[member[0]][6];
            member[10] = questions[member[0]][7];
            member[11] = questions[member[0]][8];
            member[12] = questions[member[0]][9];
            member[13] = questions[member[0]][10];
            member[14] = questions[member[0]][11];
            member[15] = questions[member[0]][12];
            member[16] = questions[member[0]][13];
            member[17] = questions[member[0]][14];
            member[18] = questions[member[0]][15];
            member[19] = questions[member[0]][16];
            member[20] = questions[member[0]][17];
            member[21] = questions[member[0]][18];
            member[22] = questions[member[0]][19];
        } else if (test_type == "fill") {
            member[6] = questions[member[0]][3];
            member[7] = questions[member[0]][4];
            member[8] = questions[member[0]][5];
        }
        return member;
    }
}

/* I don't think this function is used */
function empty_remove(member) {
    if (typeof member !== 'undefined') {
        return true;
    } else {
        return false;
    }
}

// initialize map
function history_map(member) {
    member[member.length - 1] = null;
    member[member.length - 2] = null;
    return member;
}

// Modal function
function modal_open(modal_class) {
    var name = "." + modal_class;
    $(document.body).addClass('modal-open');
    $(name).css("display", "block");
}

// Modal function
function modal_hide(modal_class) {
    var name = "." + modal_class;
    $(document.body).removeClass('modal-open');
    $(name).css("display", "none");
    if ($(name).hasClass("youtube-modal")) {
        var src = $(name).find("iframe").attr("src");
        $(name).find("iframe").attr("src", src);
    }
}

function notify_alert(notify_title, notify_message, notify_type)
{
    $.notify({
        // options
        icon: '',
        title: notify_title,
        message: notify_message,
        url: 'javascript:void(0)',
        target: '_blank'
    },{
        // settings
        element: 'body',
        position: null,
        type: notify_type,
        allow_dismiss: true,
        newest_on_top: false,
        showProgressbar: false,
        placement: {
            from: "top",
            align: "right"
        },
        offset: 20,
        spacing: 10,
        z_index: 1031,
        delay: 5000,
        timer: 1000,
        url_target: '_blank',
        mouse_over: null,
        animate: {
            enter: 'animated fadeInDown',
            exit: 'animated fadeOutUp'
        },
        onShow: null,
        onShown: null,
        onClose: null,
        onClosed: null,
        icon_type: 'class',
        template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0}" role="alert">' +
        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
        '<span data-notify="icon"></span> ' +
        '<span data-notify="title">{1}</span> ' +
        '<span data-notify="message">{2}</span>' +
        '<div class="progress" data-notify="progressbar">' +
        '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
        '</div>' +
        '<a href="{3}" target="{4}" data-notify="url"></a>' +
        '</div>'
    });
}


// string functions
function firstWordToUpperCase(word) {
    if (word) {
        return word.charAt(0).toUpperCase() + word.slice(1);
    }
    else {
        console.log('nothing true passed into function: firstWordToUpperCase')
    }
}
// allWordsToUpperCase
function allWordsToUpperCase(word){
    if (word) {
        return word.toLowerCase().replace( /\b./g, function(a){ return a.toUpperCase(); } );
    }
    else {
        console.log('nothing true passed into function: allWordsToUpperCase')
    }
}
// spacestoDashes
function spacestoDashes (word) {
    if (word) {
        return word.replace(/\s+/g, '-');
    }
    else {
        console.log('nothing true passed into function: spacestoDashes')
    }
}
// spaces to %20
function spaces_to_html_spaces (word) {
    if (word) {
        return word.replace(/\s+/g, '%20');
    }
    else {
        console.log('nothing true passed into function: spacesToHTMLSpaces')
    }
}
function html_spaces_to_spaces (word) {
    if (word) {
        return word.replace(/\%20+/g, ' ');
    }
    else {
        console.log('nothing true passed into function: spacesToHTMLSpaces')
    }
}

// removeEverythingAfterDash
function removeEverythingAfterDash (word) {
    if (word !== undefined) {
        word = word.split('-')[0];
        word = $.trim(word);
        return word;
    }
    else {
        console.log('nothing true passed into function: removeEverythingAfterDash')
    }
}
// removeExtrasFromTestName - from DB, take out : ' spaces on back
function removeExtrasFromTestName (word) {
    if (word) {
        return word.replace(/:|,|'/g, '');
    }
    else {
        console.log('nothing true passed into function: removeColon')
    }
}
// Take name from DB and return a clean string
function cleanUpGameSmartzTestName (word){
    if (word !== undefined) {
     return spacestoDashes(removeExtrasFromTestName(removeEverythingAfterDash($.trim(word))));
    }
    else {
        console.log('nothing true passed into function: cleanUpGameSmartzTestName')
    }
}

// keep html escaped apostrophes &#39; and keep & symbols, remove everything else
// split on : , keep 1st array
function test_name_before_colon (word) {
    if (word !== undefined) {
        word = word.split(':')[0]
        word = $.trim(word).replace(/[^\w\s\&\&#39;\-']/gm, '');
        return word;
    }
    else {
        console.log('nothing passed in')
    }
}


// keep html escaped apostrophes &#39; and keep & symbols, remove everything else
// split on : , keep 1st array
function test_name_after_colon (word) {
    if (word !== undefined) {
        word = word.split(':')[1];
        word = $.trim(word).replace(/[^\w\s\&\&#39;\-']/gm, '');
        return word;
    }
    else {
        console.log('nothing passed in')
    }
}

// remove everything but alphanumeric and spaces
function remove_all_non_characters (word) {
    if (word !== undefined) {
        word = $.trim(word).replace(/[^\w\s\ ]/gm, '');
        return word;
    }
    else {
        console.log('nothing passed in')
    }
}

//Modal resize on window size
function resizeModal() {
    var modalWidth = $(window).width();
    var buttonMargin = 66;
    var modalHeight = ((modalWidth - buttonMargin) * 0.596);


    if (modalWidth > 920) {
        $('.modal-content iframe').css("width", 854);
        $('.modal-content iframe').css("height", 509);
    }
    else {
        modalWidth -= buttonMargin;
        $('.modal-content iframe').css("width", modalWidth);
        $('.modal-content iframe').css("height", modalHeight);
    }
}


$(document).ready(function () {
    /* resizeModal call when page loads */
    resizeModal();
});
$(window).resize(function () {
    /* resizeModal call when change browser size */
    resizeModal();
});


function getQueryVariablefromURL(variable)
{
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");
        if(pair[0] == variable){
            return html_spaces_to_spaces(pair[1].toLowerCase()) ;
        }
    }
    return(false);
}
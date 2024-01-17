$(document).ready(function () {
    /* admin nav jQuery ddslick  */
    $("#admin-nav").ddslick();

    /* redirect to other page whenever admin nav dd click */
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

    $('#sort-admin-tests').ddslick();

    //$("#choose-tests-table").tableDnD();

    $("#public_value").ddslick();
    $("#gradeless_value").ddslick();

});

var items_per_page = 100;

/* define pagination UI with members array */
function pageselectCallback(page_index, jq) {
    // Item per page

    var max_elem = Math.min((page_index + 1) * items_per_page, members.length);
    var newcontent = '';

    // Iterate through a selection of the content and build an HTML string
    for (var i = page_index * items_per_page; i < max_elem; i++) {
        newcontent += '<tr class="fs1-1em ' + (members[i][4] == 2 ? 'admin-public' : '') + '">';
        newcontent += '<td>';
        if (members[i][0] != "none") {
            newcontent += '<a href="/preview/' + members[i][0] + '" onmouseover="open_tooltip(this)" onmouseout="close_tooltip(this)">' + members[i][1] + '</a>';
        } else {
            newcontent += '<span>' + members[i][1] + '</span>';
        }
        newcontent += '<div class="tooltip-testinfo">';
        newcontent += '<ul>';
        newcontent += '<li><img class="test-attachment" src="' + members[i][6] + '"></li>';
        newcontent += '</ul>';
        newcontent += '</div>';
        newcontent += '</td>';
        newcontent += '<td class="grade-subject">' + members[i][8] + '</td>';
        newcontent += '<td class="hidden-sm-down">' + members[i][2] + '</td>';
        newcontent += '<td class="hidden-sm-down">' + members[i][7] + '</td>';
        newcontent += '<td class="hidden-sm-down">' + members[i][9] + '</td>';
        newcontent += '<td class="order hidden-sm-down">' + members[i][10] + '</td>';
        newcontent += '<td class="popularity hidden-sm-down">' + members[i][3] + '</td>';
        newcontent += '<td><a class="public" onclick="save_public_toggle(this,' + members[i][0] + ')">';
        if (members[i][0] != "none") {
            if (members[i][4] == 2) {
                newcontent += 'Yes';
            } else {
                newcontent += 'No';
            }
        }
        newcontent += '</a></td>';
        newcontent += '<td class="action-dropdown">';
        if (members[i][0] != "none") {
            newcontent += '<input type="hidden" id="test_video_' + members[i][0] + '" class="test_video" value="' + members[i][11] + '">';
            newcontent += '<div class="select dd-container" onclick="action_dropdown(this)">';
            newcontent += '<div class="dd-select">';
            newcontent += '<a class="dd-selected">actions</a>';
            newcontent += '<span class="dd-pointer dd-pointer-down"></span>';
            newcontent += '</div>';
            newcontent += '<ul class="dd-options mlneg80">';
            newcontent += '<li><a class="dd-option" href="/preview/' + members[i][0] + '"><label class="dd-option-text">preview test</label></a></li>';
            newcontent += '<li><a class="dd-option" href="/chapter/' + members[i][0] + '"><label class="dd-option-text">chapter view</label></a></li>';
            newcontent += '<li><a class="dd-option" href="/test-question/' + members[i][0] + '/admin"><label class="dd-option-text">take test</label></a></li>';
            if (members[i][5] == "multiple") {
                newcontent += '<li><a class="dd-option" href="/create-test-multiple-choice/' + members[i][0] + '"><label class="dd-option-text">edit test</label></a></li>';
            } else if (members[i][5] == "fill") {
                newcontent += '<li><a class="dd-option" href="/create-test-fill-in-the-blank/' + members[i][0] + '"><label class="dd-option-text">edit test</label></a></li>';
            } else if (members[i][5] == "spelling") {
                newcontent += '<li><a class="dd-option" href="/create-test-spelling/' + members[i][0] + '"><label class="dd-option-text">edit test</label></a></li>';
            }

            newcontent += '<li><a class="dd-option" onclick="add_que(' + members[i][0] + ')"><label class="dd-option-text">add to queue</label></a></li>';

            if (user_role == 1) {
                newcontent += '<li><a class="dd-option" onclick="edit_popularity(this,' + members[i][0] + ')"><label class="dd-option-text">popularity score</label></a></li>';
                newcontent += '<li><a class="dd-option" onclick="edit_public(this,' + members[i][0] + ')"><label class="dd-option-text">edit public</label></a></li>';
                newcontent += '<li><a class="dd-option" onclick="more_subject(' + members[i][0] + ')"><label class="dd-option-text">more subject IDs</label></a></li>';
                newcontent += '<li><a class="dd-option" onclick="edit_gradeless(this,' + members[i][0] + ')"><label class="dd-option-text">make test gradeless</label></a></li>';
                newcontent += '<li><a class="dd-option" onclick="edit_video(this,' + members[i][0] + ')"><label class="dd-option-text">video</label></a></li>';
                newcontent += '<li><a class="dd-option" onclick="edit_school_year(this,' + members[i][0] + ')"><label class="dd-option-text">order</label></a></li>';
                newcontent += '<li><a class="dd-option" onclick="open_upload(this,' + members[i][0] + ')" data-file="#attach_image"><label class="dd-option-text">attach image</label></a></li>';
                newcontent += '<li><a class="dd-option" onclick="edit_max_height(' + members[i][0] + ')"><label class="dd-option-text">max height</label></a></li>';
                newcontent += '<li><a class="dd-option" onclick="reset_progress(this,' + members[i][0] + ')"><label class="dd-option-text">reset test progress</label></a></li>';
                newcontent += '<li><a class="dd-option" onclick="delete_test(this,' + members[i][0] + ',' + i + ')"><label class="dd-option-text">delete test</label></a></li>';
            }
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

/* add que event when hit add to que in table td */
function add_que(test_id) {
    var type = "que";
    $.ajax({
        url: "/ajax/async-tests/add",
        dataType: "json",
        type: "POST",
        data: {type: type, test_id: test_id},
        success: function (data) {
            if (data.result == "success") {
                $("#que_menu").text("QUE (" + data.que + ")");
            } else if (data.result == "failed") {
                alert("This already exist in your queue!");
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}

var row_object;
var row_index;

/* get popularity value for test */
function edit_popularity(object, test_id) {
    row_object = object;
    $("#test_id").val(test_id);
    var type = "popularity";
    $.ajax({
        url: "/ajax/async-tests/get",
        dataType: "json",
        type: "POST",
        data: {type: type, test_id: test_id},
        success: function (data) {
            if (data.result == "success") {
                $("#popularity_score").val(data.popularity);
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
    $(".popularity-modal").bPopup();
}

/* update popularity event for test */
function save_popularity() {
    var type = "popularity";
    var test_id = $("#test_id").val();
    var score = $("#popularity_score").val();
    $.ajax({
        url: "/ajax/async-tests/edit",
        dataType: "json",
        type: "POST",
        data: {type: type, test_id: test_id, score: score},
        success: function (data) {
            if (data.result == "success") {
                $(".popularity-modal").bPopup().close();
                $(row_object).parents("tr").find(".popularity").text(score);
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}

/* get public value event when hit edit public by test id  */
function edit_public(object, test_id) {
    row_object = object;
    $("#test_id").val(test_id);
    var type = "public";
    $.ajax({
        url: "/ajax/async-tests/get",
        dataType: "json",
        type: "POST",
        data: {type: type, test_id: test_id},
        success: function (data) {
            if (data.result == "success") {
                if (data.public == 2) {
                    $("#public_value .dd-selected-value").val('yes');
                    $("#public_value .dd-selected").text('yes');
                } else {
                    $("#public_value .dd-selected-value").val('no');
                    $("#public_value .dd-selected").text('no');
                }
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
    $(".public-modal").bPopup();
}

/* update public value event by test id for test But I don't think this function is used */
function save_public() {
    var type = "public";
    var test_id = $("#test_id").val();
    var score = $("#public_value .dd-selected-value").val();
    if (score == "yes")
        score = 2;
    else
        score = 1;

    $.ajax({
        url: "/ajax/async-tests/edit",
        dataType: "json",
        type: "POST",
        data: {type: type, test_id: test_id, score: score},
        success: function (data) {
            if (data.result == "success") {
                $(".public-modal").bPopup().close();
                if (score == 2) {
                    $(row_object).parents("tr").find(".public").text("Yes");
                    $(row_object).parents("tr").addClass("admin-public");
                } else {
                    $(row_object).parents("tr").find(".public").text("No");
                    $(row_object).parents("tr").removeClass("admin-public");
                }
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });

}

/* update public value event by test id for test */
function save_public_toggle(object, test_id) {
    var type = "public";
    var score = $(object).text();
    if (score == "Yes")
        score = 1;
    else
        score = 2;

    $.ajax({
        url: "/ajax/async-tests/edit",
        dataType: "json",
        type: "POST",
        data: {type: type, test_id: test_id, score: score},
        success: function (data) {
            if (data.result == "success") {
                if (score == 2) {
                    $(object).text("Yes");
                    $(object).parents("tr").addClass("admin-public");
                } else {
                    $(object).text("No");
                    $(object).parents("tr").removeClass("admin-public");
                }
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });

}

/* get school year value event by test id  */
function edit_school_year(object, test_id) {
    row_object = object;
    $("#test_id").val(test_id);
    var type = "school_year";
    $.ajax({
        url: "/ajax/async-tests/get",
        dataType: "json",
        type: "POST",
        data: {type: type, test_id: test_id},
        success: function (data) {
            if (data.result == "success") {
                $("#school_year").val(data.school_year);
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
    $(".school-year-modal").bPopup();
}

/* update school year value event by test id  */
function save_school_year() {
    var type = "school_year";
    var test_id = $("#test_id").val();
    var score = $("#school_year").val();
    $.ajax({
        url: "/ajax/async-tests/edit",
        dataType: "json",
        type: "POST",
        data: {type: type, test_id: test_id, score: score},
        success: function (data) {
            if (data.result == "success") {
                $(".school-year-modal").bPopup().close();
                $(row_object).parents("tr").find(".order").text(score);
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}

/* open upload event that open file explorer to choose file for upload */
function open_upload(object, test_id) {
    row_object = object;
    $('input[name="test_id"]').val(test_id);
    var file_el_id = $(object).data('file');
    $(file_el_id).click();
}

/* upload image file event */
function upload_attach() {

    if (typeof FileReader !== "undefined") {

        var object = document.getElementById("attach_image");
        var file = object.files[0];
        // check file size
        var size = parseInt(file.size / 1024);
        var type = file.type;

        if (typeof size !== 'NaN') {
            if (size > 300 || (type !== 'image/jpeg' && type !== 'image/png')) {
                //$(object).parents("form").reset();
                $('.graphics-file-size-error').bPopup();
                return;
            } else {
                $("#attach_image_form").ajaxForm({
                    success: function (data) {
                        if (data.result == "success") {
                            $(row_object).parents("tr").find(".test-attachment").attr("src", data.attachment);
                        } else {
                            alert("Attachment Upload Failed!");
                        }
                    }
                }).submit();
            }
        }

    }

}

/* change subject event: get tests by filter type and subject id */
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
        url: "/ajax/async-tests/get",
        dataType: "json",
        type: "POST",
        data: {type: type, filter_type: filter_type, subject_id: subject_id},
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

/* delete test event by test id: first show confirm popup */
function delete_test(object, test_id, index) {
    $(".delete-modal-wrapper").bPopup();
    $("#test_id").val(test_id);
    row_object = object;
    row_index = index;
}

/* delete confirmed event: delete test actually by test id   */
function delete_confirm() {
    $(".delete-modal-wrapper").bPopup().close();
    var test_id = $("#test_id").val();
    var type = "test";
    $.ajax({
        url: "/ajax/async-tests/delete",
        dataType: "json",
        type: "POST",
        data: {type: type, test_id: test_id},
        success: function (data) {
            if (data.result == "success") {
                members.splice(row_index, 1);
                $(row_object).parents("tr").remove();
                var rows = $("#choose-tests-table").find("tr");
                if (rows.length == 0) {
                    $("#choose-tests-table").html('<tr class="fs1-1em"><td colspan="3"><span>No Tests in this Category</span></td></tr>');
                }
                return;
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}

/* get more subject value event by test id */
function more_subject(test_id) {
    $("#test_id").val(test_id);
    var type = "more_subject";
    $.ajax({
        url: "/ajax/async-tests/get",
        dataType: "json",
        type: "POST",
        data: {type: type, test_id: test_id},
        success: function (data) {
            if (data.result == "success") {
                $("#more_subject").val(data.more_subject);
                $(".more-subject-modal").bPopup();
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}

/* update more_subject value event by test id */
function save_more_subject() {
    var type = "more_subject";
    var test_id = $("#test_id").val();
    var more_subject = $("#more_subject").val();
    more_subject = more_subject.replace(/ /g, '');
    $.ajax({
        url: "/ajax/async-tests/edit",
        dataType: "json",
        type: "POST",
        data: {type: type, test_id: test_id, more_subject: more_subject},
        success: function (data) {
            if (data.result == "success") {
                $(".more-subject-modal").bPopup().close();
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}

/* reset progress event by test id: first show confirm popup */
function reset_progress(object, test_id) {
    $(".reset-progress-modal").bPopup();
    $("#test_id").val(test_id);
}

/* close resetModal window and reset confirmed event */
function close_resetModal() {
    $(".reset-progress-modal").bPopup().close();

    var test_id = $("#test_id").val();
    var type = "reset_progress";
    $.ajax({
        url: "/ajax/async-tests/add",
        dataType: "json",
        type: "POST",
        data: {type: type, test_id: test_id},
        success: function (data) {
            if (data.result == "success") {

            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}

/* get max height value event by test id  */
function edit_max_height(test_id) {
    $("#test_id").val(test_id);
    var type = "max_height";
    $.ajax({
        url: "/ajax/async-tests/get",
        dataType: "json",
        type: "POST",
        data: {type: type, test_id: test_id},
        success: function (data) {
            if (data.result == "success") {
                $("#max_height").val(data.max_height);
                $(".max-height-modal").bPopup();
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}

/* update max_height value event by test id */
function save_max_height() {
    var type = "max_height";
    var test_id = $("#test_id").val();
    var max_height = $("#max_height").val();
    $.ajax({
        url: "/ajax/async-tests/edit",
        dataType: "json",
        type: "POST",
        data: {type: type, test_id: test_id, max_height: max_height},
        success: function (data) {
            if (data.result == "success") {
                $(".max-height-modal").bPopup().close();
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}

/* get gradeless value event by test id */
function edit_gradeless(object, test_id) {
    row_object = object;
    $("#test_id").val(test_id);
    var type = "gradeless";
    $.ajax({
        url: "/ajax/async-tests/get",
        dataType: "json",
        type: "POST",
        data: {type: type, test_id: test_id},
        success: function (data) {
            if (data.result == "success") {
                if (data.gradeless == 1) {
                    $("#gradeless_value .dd-selected-value").val('yes');
                    $("#gradeless_value .dd-selected").text('yes');
                } else {
                    $("#gradeless_value .dd-selected-value").val('no');
                    $("#gradeless_value .dd-selected").text('no');
                }
                $(".gradeless-modal").bPopup();
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}

/* update gradeless value event by test id */
function save_gradeless() {
    var type = "gradeless";
    var test_id = $("#test_id").val();
    var score = $("#gradeless_value .dd-selected-value").val();
    if (score == "yes")
        score = 1;
    else
        score = 0;

    $.ajax({
        url: "/ajax/async-tests/edit",
        dataType: "json",
        type: "POST",
        data: {type: type, test_id: test_id, score: score},
        success: function (data) {
            if (data.result == "success") {
                $(row_object).parents("tr").find(".grade-subject").text(data.gradeless);
                $(".gradeless-modal").bPopup().close();
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });

}

/* edit video event: show edit video popup */
function edit_video(object, test_id) {
    row_object = object;
    $("#test_id").val(test_id);
    var input_id = "#test_video_" + test_id;
    var video_link = $(input_id).val();
    $("#video_link").val(video_link);
    $(".video-link-modal").bPopup();
}

/* update video link value event by test id  */
function save_video() {
    var type = "video_link";
    var test_id = $("#test_id").val();
    var video_link = $("#video_link").val();

    $.ajax({
        url: "/ajax/async-tests/edit",
        dataType: "json",
        type: "POST",
        data: {type: type, test_id: test_id, video_link: video_link},
        success: function (data) {
            if (data.result == "success") {
                $(row_object).parents("tr").find(".test_video").val(video_link);
                $(".video-link-modal").bPopup().close();
            } else if (data.result == "expire") {
                $('.expire-modal-wrapper').bPopup();
            }
        }
    });
}
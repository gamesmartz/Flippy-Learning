var items_per_page = 10;

function pageselectCallback(page_index, jq) {

    // Item per page

    var max_elem = Math.min((page_index + 1) * items_per_page, members.length);
    var newcontent = '';

    // Iterate through a selection of the content and build an HTML string
    for (var i = page_index * items_per_page; i < max_elem; i++) {
        newcontent += '<tr class="fs1-1em">';
        newcontent += '<td class="pd30-left">';
        if (members[i][0] != "none") {
            newcontent += '<span onmouseover="open_tooltip(this)" onmouseout="close_tooltip(this)">' + members[i][1] + '</span>';
        } else {
            newcontent += '<span>' + members[i][1] + '</span>';
        }
        newcontent += '<div class="tooltip-testinfo">';
        newcontent += '<ul>';
        newcontent += '<li><img class="test-attachment" src="' + members[i][5] + '"></li>';
        newcontent += '</ul>';
        newcontent += '</div>';
        newcontent += '</td>';
        newcontent += '<td class="left-align">' + members[i][2] + '</td>';
        newcontent += '<td>';
        if (members[i][0] != "none") {
            newcontent += '<div class="select dd-container" onclick="action_dropdown(this)">';
            newcontent += '<div class="dd-select">';
            newcontent += '<a class="dd-selected">actions</a>';
            newcontent += '<span class="dd-pointer dd-pointer-down"></span>';
            newcontent += '</div>';
            newcontent += '<ul class="dd-options">';
            newcontent += '<li><a class="dd-option" href="test-question.php?id=' + members[i][0] + '&from=personal"><label class="dd-option-text">take test</label></a></li>';
            if (members[i][4] == "multiple") {
                newcontent += '<li><a class="dd-option" href="create-test-multiple-choice.php?id=' + members[i][0] + '"><label class="dd-option-text">edit test</label></a></li>';
            } else if (members[i][4] == "fill") {
                newcontent += '<li><a class="dd-option" href="create-test-fill-in-the-blank.php?id=' + members[i][0] + '"><label class="dd-option-text">edit test</label></a></li>';
            } else if (members[i][4] == "spelling") {
                newcontent += '<li><a class="dd-option" href="create-test-spelling.php?id=' + members[i][0] + '"><label class="dd-option-text">edit test</label></a></li>';
            }
            newcontent += '<li><a class="dd-option" onclick="add_que(' + members[i][0] + ')"><label class="dd-option-text">add to queue</label></a></li>';
            if (members[i][3] != "") {
                newcontent += '<li>';
                newcontent += '<a class="dd-option" onclick="view_video(' + members[i][0] + ')"><label class="dd-option-text">view video</label></a>';
                newcontent += '<input type="hidden" id="test_video_' + members[i][0] + '" value="' + members[i][3] + '">';
                newcontent += '</li>';
            }
            newcontent += '<li><a class="dd-option" onclick="delete_test(this,' + members[i][0] + ',' + i + ')"><label class="dd-option-text">delete test</label></a></li>';
            newcontent += '<li><a class="dd-option" onclick="public_review(this,' + members[i][0] + ',' + i + ')"><label class="dd-option-text">submit your test to teach others - earn professor points</label></a></li>';
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


$(document).ready(function () {

    // Create pagination element with options from form
    var optInit = getOptionsFromForm();
    $("#Pagination").pagination(members.length, optInit);

    // sort
    $("#sort-personal").ddslick();

    //$("#choose-tests-table").tableDnD();

});

/* open tooltip event on table td element this function is used for most js */
function open_tooltip(object) {
    if ($(object).parent('td').find('.test-attachment').attr("src") != "") {
        $($(object).parent('td').find('.tooltip-testinfo')).fadeIn(100);
    }
}

/* close tooltip event on table td element, this function is used for most js */
function close_tooltip(object) {
    $($(object).parent('td').find('.tooltip-testinfo')).fadeOut(250);
}

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

function add_que(test_id) {
    var type = "que";
    $.ajax({
        url: "ajax/async-tests.php?action=add",
        dataType: "json",
        type: "POST",
        data: {type: type, test_id: test_id},
        success: function (data) {
            if (data.result == "success") {
                $("#que_menu").text("QUE (" + data.que + ")");
            } else if (data.result == "failed") {
                alert("This already exist in your queue!");
            } else if (data.result == "expire") {
                $('#session-expired-modal').modal();
            }
        }
    });
}

function view_video(test_id) {
    var input_id = "#test_video_" + test_id;
    var url = $(input_id).val();
    if (url != "") {
        $(".test-video-modal").find("iframe").attr("src", url);
        $(".test-video-modal").bPopup();
    }
}

var _object;
var _index;

function public_review(object, test_id, index) {
    $(".review-modal-wrapper").bPopup();
    $("#test_id").val(test_id);
    _object = object;
    _index = index;
}

function review_confirm() {
    $(".review-modal-wrapper").bPopup().close();
    var test_id = $("#test_id").val();
    var type = "public_review";
    $.ajax({
        url: "ajax/async-tests.php?action=edit",
        dataType: "json",
        type: "POST",
        data: {type: type, test_id: test_id},
        success: function (data) {
            if (data.result == "success") {
                members.splice(_index, 1);
                $(_object).parents("tr").remove();
                var rows = $("#choose-tests-table").find("tr");
                if (rows.length == 0) {
                    $("#choose-tests-table").html('<tr class="fs1-1em"><td colspan="3"><span>No Tests in this Category</span></td></tr>');
                }
                return;
            } else if (data.result == "expire") {
                $('#session-expired-modal').modal();
            }
        }
    });
}

function delete_test(object, test_id, index) {
    $(".delete-modal-wrapper").bPopup();
    $("#test_id").val(test_id);
    _object = object;
    _index = index;
}

function delete_confirm() {
    $(".delete-modal-wrapper").bPopup().close();
    var test_id = $("#test_id").val();
    var type = "test";
    $.ajax({
        url: "ajax/async-tests.php?action=delete",
        dataType: "json",
        type: "POST",
        data: {type: type, test_id: test_id},
        success: function (data) {
            if (data.result == "success") {
                members.splice(_index, 1);
                $(_object).parents("tr").remove();
                var rows = $("#choose-tests-table").find("tr");
                if (rows.length == 0) {
                    $("#choose-tests-table").html('<tr class="fs1-1em"><td colspan="3"><span>No Tests in this Category</span></td></tr>');
                }
                return;
            } else if (data.result == "expire") {
                $('#session-expired-modal').modal();
            }
        }
    });
}
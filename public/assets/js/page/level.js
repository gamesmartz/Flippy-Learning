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
        newcontent += '<li><img class="test-attachment" src="' + members[i][4] + '"></li>';
        newcontent += '</ul>';
        newcontent += '</div>';
        newcontent += '</td>';
        newcontent += '<td class="left-align"><a href="reports?id=' + members[i][0] + '">results</a></td>';
        newcontent += '<td class="left-align">' + members[i][2] + '</td>';
        newcontent += '<td>';
        if (members[i][0] != "none") {
            newcontent += '<div class="select dd-container" onclick="action_dropdown(this)">';
            newcontent += '<div class="dd-select">';
            newcontent += '<a class="dd-selected">actions</a>';
            newcontent += '<span class="dd-pointer dd-pointer-down"></span>';
            newcontent += '</div>';
            newcontent += '<ul class="dd-options">';
            newcontent += '<li><a class="dd-option" href="reports?id=' + members[i][0] + '"><label class="dd-option-text">results</label></a></li>';
            newcontent += '<li><a class="dd-option" href="test-question.php?id=' + members[i][0] + '&from=level"><label class="dd-option-text">take test</label></a></li>';
            newcontent += '<li><a class="dd-option" onclick="add_que(' + members[i][0] + ')"><label class="dd-option-text">add to queue</label></a></li>';
            if (members[i][3] != "") {
                newcontent += '<li>';
                newcontent += '<a class="dd-option" onclick="view_video(' + members[i][0] + ')"><label class="dd-option-text">view video</label></a>';
                newcontent += '<input type="hidden" id="test_video_' + members[i][0] + '" value="' + members[i][3] + '">';
                newcontent += '</li>';
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


$(document).ready(function() {

    // Create pagination element with options from form
    var optInit = getOptionsFromForm();
    $("#Pagination").pagination(members.length, optInit);

    // category dropdown
    $("#choose-grade-1").ddslick();
    $("#choose-subject-1").ddslick();

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

/* add que event */
function add_que(test_id) {
    var type = "que";
    $.ajax({
        url: "ajax/async-tests.php?action=add",
        dataType: "json",
        type: "POST",
        data: { type: type, test_id: test_id },
        success: function(data) {
            if (data.result == "success") {
                // removed php extension
                window.location.href = "queue";
            } else if (data.result == "failed") {
                alert("This already exist in your queue!");
            } else if (data.result == "expire") {
                $('#session-expired-modal').modal();
            }
        }
    });
}

/* view video popup */
function view_video(test_id) {
    var input_id = "#test_video_" + test_id;
    var url = $(input_id).val();
    if (url != "") {
        $(".test-video-modal").find("iframe").attr("src", url);
        $(".test-video-modal").bPopup();
    }
}
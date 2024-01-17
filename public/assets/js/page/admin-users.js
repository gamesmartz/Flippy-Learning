$(document).ready(function () {

    $("#admin-nav").ddslick();

    $("#admin-nav .dd-option").click(function () {
        var nav_link = $("#admin-nav .dd-selected-value").val();
        window.location.href = nav_link;
    });

    // Create pagination element with options from form
    var optInit = getOptionsFromForm();
    $("#Pagination").pagination(members.length, optInit);

    // sort
    $("#sort-users").ddslick();

    //$("#choose-tests-table").tableDnD();

    $("#user_role").ddslick();
    $("#user_subscription").ddslick();
    $("#user_status").ddslick();
    $("#show_leader").ddslick();

});

var items_per_page = 20;

/* define jquery pagination  */
function pageselectCallback(page_index, jq) {

    // Item per page

    var max_elem = Math.min((page_index + 1) * items_per_page, members.length);
    var newcontent = '';

    // Iterate through a selection of the content and build an HTML string
    for (var i = page_index * items_per_page; i < max_elem; i++) {
        newcontent += '<tr class="fs1-1em">';
        newcontent += '<td>' + members[i][1] + '</td>';
        newcontent += '<td class="hidden-sm-down">' + numeral(members[i][7]).format('0,0') + '</td>';
        newcontent += '<td class="hidden-sm-down">' + members[i][2] + '</td>';
        newcontent += '<td class="">' + members[i][3] + '</td>';
        newcontent += '<td class="hidden-sm-down">' + members[i][4] + '</td>';
        newcontent += '<td class="hidden-sm-down">' + members[i][5] + '</td>';
        newcontent += '<td class="subscript hidden-sm-down">' + members[i][6] + '</td>';
        newcontent += '<td class="action-dropdown">';
        if (members[i][0] != "none") {
            newcontent += '<div class="select dd-container" onclick="action_dropdown(this)">';
            newcontent += '<div class="dd-select">';
            newcontent += '<a class="dd-selected">actions</a>';
            newcontent += '<span class="dd-pointer dd-pointer-down"></span>';
            newcontent += '</div>';
            newcontent += '<ul class="dd-options mlneg80">';
            newcontent += '<li><a class="dd-option" onclick="change_password(' + members[i][0] + ')"><label class="dd-option-text">change password</label></a></li>';
            newcontent += '<li><a class="dd-option" onclick="change_subscription(this,' + members[i][0] + ',' + i + ')"><label class="dd-option-text">subscription</label></a></li>';
            newcontent += '<li><a class="dd-option" onclick="account_lock(' + members[i][0] + ')"><label class="dd-option-text">account lock</label></a></li>';
            newcontent += '<li><a class="dd-option" onclick="make_admin(' + members[i][0] + ')"><label class="dd-option-text">user privileges</label></a></li>';
            newcontent += '<li><a class="dd-option" onclick="leader_board(' + members[i][0] + ')"><label class="dd-option-text">leader board</label></a></li>';
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


var user_id;

/* change password popup */
function change_password(id) {
    user_id = id;
    $(".password-modal").bPopup();
}

/* update with new password */
function save_password() {
    var new_password = $("#user_pass").val();
    $.ajax({
        url: "/ajax/async-updateUser/password",
        dataType: "json",
        type: "POST",
        data: {user_id: user_id, new_password: new_password},
        success: function (data) {
            if (data.result == "success") {
                $(".password-modal").bPopup().close();
            }
        }
    });
}


var row_object;
var row_index;

/* get subscription value event by user id  */
function change_subscription(object, id, index) {
    user_id = id;
    row_object = object;
    row_index = index;
    var type = "subscription";
    $.ajax({
        url: "/ajax/async-updateUser/get",
        dataType: "json",
        type: "POST",
        data: {type: type, user_id: user_id},
        success: function (data) {
            if (data.result == "success") {
                $("#user_subscription .dd-selected-value").val(data.subscription);
                if (data.subscription == 0) {
                    $("#user_subscription .dd-selected").text('no');
                } else if (data.subscription == 1) {
                    $("#user_subscription .dd-selected").text('yes');
                }
            }
        }
    });
    $(".subscription-modal").bPopup();
}

/* update subscription value event by user id  */
function save_subscription() {
    var subscription = $("#user_subscription .dd-selected-value").val();
    $.ajax({
        url: "/ajax/async-updateUser/subscription",
        dataType: "json",
        type: "POST",
        data: {user_id: user_id, subscription: subscription},
        success: function (data) {
            if (data.result == "success") {
                members[row_index][6] = subscription;
                $(row_object).parents("tr").find(".subscript").text(subscription);
                $(".subscription-modal").bPopup().close();
            }
        }
    });
}

/* get user account status event by user id */
function account_lock(id) {
    user_id = id;
    var type = "status";
    $.ajax({
        url: "/ajax/async-updateUser/get",
        dataType: "json",
        type: "POST",
        data: {type: type, user_id: user_id},
        success: function (data) {
            if (data.result == "success") {
                $("#user_status .dd-selected-value").val(data.status);
                if (data.status == 1) {
                    $("#user_status .dd-selected").text('no');
                } else if (data.status == 0) {
                    $("#user_status .dd-selected").text('yes');
                }
            }
        }
    });
    $(".account-modal").bPopup();
}

/* update user status event by user id  */
function save_status() {
    var user_status = $("#user_status .dd-selected-value").val();
    $.ajax({
        url: "/ajax/async-updateUser/status",
        dataType: "json",
        type: "POST",
        data: {user_id: user_id, user_status: user_status},
        success: function (data) {
            if (data.result == "success") {
                $(".account-modal").bPopup().close();
            }
        }
    });
}

/* get user role event by user id  */
function make_admin(id) {
    user_id = id;
    var type = "role";
    $.ajax({
        url: "/ajax/async-updateUser/get",
        dataType: "json",
        type: "POST",
        data: {type: type, user_id: user_id},
        success: function (data) {
            if (data.result == "success") {
                $("#user_role .dd-selected-value").val(data.role);
                if (data.role == 1) {
                    $("#user_role .dd-selected").text('admin');
                } else if (data.role == 2) {
                    $("#user_role .dd-selected").text('user');
                } else if (data.role == 3) {
                    $("#user_role .dd-selected").text('loading');
                }
            }
        }
    });
    $(".admin-modal").bPopup();
}

/* update user role event by user id */
function save_role() {
    var user_role = $("#user_role .dd-selected-value").val();
    $.ajax({
        url: "/ajax/async-updateUser/role",
        dataType: "json",
        type: "POST",
        data: {user_id: user_id, user_role: user_role},
        success: function (data) {
            if (data.result == "success") {
                $(".admin-modal").bPopup().close();
            }
        }
    });
}

/* get leader value event by user id  */
function leader_board(id) {
    user_id = id;
    var type = "leader";
    $.ajax({
        url: "/ajax/async-updateUser/get",
        dataType: "json",
        type: "POST",
        data: {type: type, user_id: user_id},
        success: function (data) {
            if (data.result == "success") {
                $("#show_leader .dd-selected-value").val(data.show_leader);
                if (data.show_leader == 1) {
                    $("#show_leader .dd-selected").text('yes');
                } else if (data.show_leader == 0) {
                    $("#show_leader .dd-selected").text('no');
                }
            }
        }
    });
    $(".leader-modal").bPopup();
}

/* save leader value event by user id */
function save_leader() {
    var show_leader = $("#show_leader .dd-selected-value").val();
    $.ajax({
        url: "/ajax/async-updateUser/leader",
        dataType: "json",
        type: "POST",
        data: {user_id: user_id, show_leader: show_leader},
        success: function (data) {
            if (data.result == "success") {
                $(".leader-modal").bPopup().close();
            }
        }
    });
}
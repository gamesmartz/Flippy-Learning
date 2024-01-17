/* Game Defined List*/
function list_games() {

    var newcontent = '';

    // Iterate through a selection of the content and build an HTML string
    for (var i = 0; i < members.length; i++) {
        newcontent += '<tr class="fs1-1em">';
        newcontent += '<td class="pd30-left name">' + members[i][0] + '</td>';
        newcontent += '<td class="left-align exe hidden-sm-down">' + members[i][1] + '</td>';
        newcontent += '<td class="left-align link hidden-sm-down">' + members[i][2] + '</td>';
        newcontent += '<td class="left-align style hidden-sm-down">' + members[i][3] + '</td>';
        newcontent += '<td>';

        newcontent += '<div class="select dd-container" onclick="action_dropdown(this)">';
        newcontent += '<div class="dd-select">';
        newcontent += '<a class="dd-selected">actions</a>';
        newcontent += '<span class="dd-pointer dd-pointer-down"></span>';
        newcontent += '</div>';
        newcontent += '<ul class="dd-options">';
        newcontent += '<li><a class="dd-option" onclick="edit_game(this,' + i + ')"><label class="dd-option-text">edit</label></a></li>';
        newcontent += '<li><a class="dd-option" onclick="delete_game(' + i + ')"><label class="dd-option-text">delete</label></a></li>';
        newcontent += '</ul>';
        newcontent += '</div>';

        newcontent += '</td>';
        newcontent += '</tr>';
    }

    if (newcontent == "") {
        newcontent = '<tr><td class="pd30-left" colspan="5">No games defined</td></tr>';
    }

    // Replace old content with new content
    $('#games-table').html(newcontent);

}

/* compliments List*/
function list_compliments() {

    var newcontent = '';

    // Iterate through a selection of the content and build an HTML string
    for (var i = 0; i < compliments.length; i++) {
        newcontent += '<tr class="fs1-1em">';
        newcontent += '<td class="pd30-left">';
        newcontent += '<button class="btn btn-success btn-file" onclick="open_upload(this,&#39;img&#39;,' + compliments[i][0] + ')" data-file="#attach_compliment" type="button">graphic</button>';
        newcontent += '<div class="inline-block ml15" id="compliment_img_' + compliments[i][0] + '">';
        newcontent += '<img src="/' + compliments[i][1] + '" class="attach_images">';
        newcontent += '</div>';
        newcontent += '</td>';
        newcontent += '<td class="left-align">'
        newcontent += '<button class="btn btn-success btn-uploadAudio" onclick="open_upload(this,&#39;audio&#39;,' + compliments[i][0] + ')" data-file="#attach_compliment" type="button"></button>';
        newcontent += '<div class="inline-block ml15 mt5" id="compliment_audio_' + compliments[i][0] + '">';
        newcontent += '<audio controls="controls" src="/' + compliments[i][2] + '" type="audio/mpeg"></audio>';
        newcontent += '</div>';
        newcontent += '</td>';
        newcontent += '<td>';

        newcontent += '<div class="select dd-container" onclick="action_dropdown(this)">';
        newcontent += '<div class="dd-select">';
        newcontent += '<a class="dd-selected">actions</a>';
        newcontent += '<span class="dd-pointer dd-pointer-down"></span>';
        newcontent += '</div>';
        newcontent += '<ul class="dd-options">';
        newcontent += '<li><a class="dd-option" onclick="delete_compliment(this,' + compliments[i][0] + ')"><label class="dd-option-text">delete</label></a></li>';
        newcontent += '</ul>';
        newcontent += '</div>';

        newcontent += '</td>';
        newcontent += '</tr>';
    }

    if (newcontent == "") {
        newcontent = '<tr><td class="pd30-left" colspan="3">No compliments defined</td></tr>';
    }

    // Replace old content with new content
    $('#compliments-table').html(newcontent);
}
/* List Audio Alerts */
function listAudioAlerts() {

    var newcontent = '';

    // Iterate through a selection of the content and build an HTML string
    for (var i = 0; i < audioAlerts.length; i++) {
        var timeOptionsHtml = '';
        for (var ii = 1; ii <= 10; ii++) {
            if (audioAlerts[i][3] == ii)
                timeOptionsHtml += '<option value="' + ii + '" selected>' + ii + ':00</option>';
            else
                timeOptionsHtml += '<option value="' + ii + '">' + ii + ':00</option>';
        }
        newcontent += '<tr class="fs1-1em" data-id="' + audioAlerts[i][0] + '">';
        newcontent += '<td class="pd30-left">';
        newcontent += '<select name="audio_time" id="audio_time" class="select form-control gs-default-drop-down-width">' + timeOptionsHtml + '</select>';
        newcontent += '</td>';
        newcontent += '<td class="left-align">';
        newcontent += '<button class="btn btn-success btn-uploadAudio float-left mr-4" onclick="open_upload_alert(this,&#39;audio&#39;,' + audioAlerts[i][0] + ')" data-file="#attach_audio_alert" type="button"></button>';
        newcontent += '<div class="inline-block ml15 mt5" id="aa_audio_' + audioAlerts[i][0] + '">';
        if (audioAlerts[i][2] != '' && audioAlerts[i][2] != null) {

            newcontent += '<audio controls="controls" class="hidden-sm-down" src="/' + audioAlerts[i][2] + '" type="audio/mpeg"></audio>';
        }
        newcontent += '</div>';
        newcontent += '</td>';
        newcontent += '<td>';

        newcontent += '<div class="select dd-container" onclick="action_dropdown(this)">';
        newcontent += '<div class="dd-select">';
        newcontent += '<a class="dd-selected">actions</a>';
        newcontent += '<span class="dd-pointer dd-pointer-down"></span>';
        newcontent += '</div>';
        newcontent += '<ul class="dd-options">';
        newcontent += '<li><a class="dd-option" onclick="deleteObject(this,' + audioAlerts[i][0] + ', &#39;audio-alert&#39;)"><label class="dd-option-text">delete</label></a></li>';
        newcontent += '</ul>';
        newcontent += '</div>';

        newcontent += '</td>';
        newcontent += '</tr>';
    }

    if (newcontent == "") {
        newcontent = '<tr><td class="pd30-left" colspan="3">No Audio Alerts defined</td></tr>';
    }

    // Replace old content with new content
    $('#audio_alerts_table').html(newcontent);
}

/* list Persons */
function list_persons() {

    var newcontent = '';

    // Iterate through a selection of the content and build an HTML string
    for (var i = 0; i < persons.length; i++) {
        newcontent += '<tr class="fs1-1em">';
        newcontent += '<td class="pd30-left name">' + persons[i][0] + '</td>';
        newcontent += '<td class="left-align exe">' + persons[i][1] + '</td>';
        newcontent += '<td>';

        newcontent += '<div class="select dd-container" onclick="action_dropdown(this)">';
        newcontent += '<div class="dd-select">';
        newcontent += '<a class="dd-selected">actions</a>';
        newcontent += '<span class="dd-pointer dd-pointer-down"></span>';
        newcontent += '</div>';
        newcontent += '<ul class="dd-options">';
        newcontent += '<li><a class="dd-option" onclick="edit_person_game(this,' + i + ')"><label class="dd-option-text">edit</label></a></li>';
        newcontent += '<li><a class="dd-option" onclick="delete_person_game(' + i + ')"><label class="dd-option-text">delete</label></a></li>';
        newcontent += '</ul>';
        newcontent += '</div>';

        newcontent += '</td>';
        newcontent += '</tr>';
    }

    if (newcontent == "") {
        newcontent = '<tr><td class="pd30-left" colspan="5">No 1st person games defined</td></tr>';
    }

    // Replace old content with new content
    $('#person-games-table').html(newcontent);

}
/* ddclick action drop down */
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

$(document).ready(function () {

    // list defined games when page loads
    list_games();
    list_compliments();
    listAudioAlerts();
    list_persons();

    $("#admin-nav").ddslick();
    $("#choose-grade-1").ddslick();
    $("#choose-grade-2").ddslick();
    $("#choose-grade-3").ddslick();
    $("#choose-subject-1").ddslick();
    $("#choose-subject-2").ddslick();
    $("#choose-sub-category-1").ddslick();

    /* redirect to pages whenever change admin nav */
    $("#admin-nav .dd-option").click(function () {
        var nav_link = $("#admin-nav .dd-selected-value").val();
        window.location.href = nav_link;
    });

    /* save grade click event But I don't think this function is used on admin-options*/
    $("#save-grade").click(function () {
        var grade = $("#new-grade").val();
        if (grade != "") {
            $("#create-category").css("display", "block");
            $("#delete-category").css("display", "none");
            $("#delete-confirm").css("display", "none");
            $(".option-modal-wrapper").bPopup({
                positionStyle: 'fixed', //'fixed' or 'absolute'
                position: ['20%', '30%'] //x, y
            });
            $("#category-name").text(grade);
            $("#type").val("grade");
        }
    });

    /* save subject click event for grade But I don't think this function is used on admin-options*/
    $("#save-subject").click(function () {
        var subject = $("#new-subject").val();
        var grade = $("#choose-grade-2 .dd-selected-value").val();
        if (subject != "" && grade != "") {
            var grade_text = $("#choose-grade-2 .dd-selected-text").text();
            $("#create-category").css("display", "block");
            $("#delete-category").css("display", "none");
            $("#delete-confirm").css("display", "none");
            $(".option-modal-wrapper").bPopup({
                positionStyle: 'fixed', //'fixed' or 'absolute'
                position: ['20%', '30%'] //x, y
            });
            $("#category-name").text(grade_text + " - " + subject);
            $("#type").val("subject");
        }
    });

    /* save sub category click event for subject I don't think this function is used on admin-options*/
    $("#save-sub-category").click(function () {
        var sub_category = $("#new-sub-category").val();
        var subject = $("#choose-subject-2 .dd-selected-value").val();
        if (sub_category != "" && subject != "") {
            var subject_text = $("#choose-subject-2 .dd-selected-text").text();
            var grade_text = $("#choose-grade-3 .dd-selected-text").text();
            $("#create-category").css("display", "block");
            $("#delete-category").css("display", "none");
            $("#delete-confirm").css("display", "none");
            $(".option-modal-wrapper").bPopup({
                positionStyle: 'fixed', //'fixed' or 'absolute'
                position: ['20%', '30%'] //x, y
            });
            $("#category-name").text(grade_text + " - " + subject_text + " - " + sub_category);
            $("#type").val("sub-category");
        }
    });

    /* remove category from the category list I don't think this function is used on admin-options*/
    $("#submit-delete-category").click(function () {
        var grade = $("#choose-grade-1 .dd-selected-value").val();
        var subject = $("#choose-subject-1 .dd-selected-value").val();
        var sub_category = $("#choose-sub-category-1 .dd-selected-value").val();
        if (grade != "") {
            $("#create-category").css("display", "none");
            $("#delete-category").css("display", "block");
            $("#delete-confirm").css("display", "none");
            $(".option-modal-wrapper").bPopup({
                positionStyle: 'fixed', //'fixed' or 'absolute'
                position: ['20%', '30%'] //x, y
            });
            var grade_text = $("#choose-grade-1 .dd-selected-text").text();
            if (subject != "") {
                var subject_text = $("#choose-subject-1 .dd-selected-text").text();
                if (sub_category != "") {
                    var sub_category_text = $("#choose-sub-category-1 .dd-selected-text").text();
                    $(".delete-category-name").text(grade_text + " - " + subject_text + " - " + sub_category_text);
                    $("#type").val("sub-category");
                } else {
                    $(".delete-category-name").text(grade_text + " - " + subject_text);
                    $("#type").val("subject");
                }
            } else {
                $(".delete-category-name").text(grade_text);
                $("#type").val("grade");
            }
        }
    });

});

/* save game modal open*/
function add_game() {
    $(".game-field").removeClass("required");
    $(".game-field").val("");
    $(".game-modal-wrapper").bPopup({
        positionStyle: 'fixed', //'fixed' or 'absolute'
        position: ['20%', '30%'] //x, y
    });
}

/* save game modal open */
function edit_game(object, index) {
    $(".game-field").removeClass("required");
    $("#game_index").val(index);
    $("#game_name").val($(object).parents("tr").find(".name").text());
    $("#game_exe").val($(object).parents("tr").find(".exe").text());
    $("#game_link").val($(object).parents("tr").find(".link").text());
    $("#game_style").val($(object).parents("tr").find(".style").text());
    $(".game-modal-wrapper").bPopup({
        positionStyle: 'fixed', //'fixed' or 'absolute'
        position: ['20%', '30%'] //x, y
    });
}

/* save event from save game modal. add/update game configuration values on Games Defined section on admin options php */
function save_game() {
    $(".game-field").removeClass("required");
    var game_index = $("#game_index").val();
    var game_name = $("#game_name").val();
    var game_exe = $("#game_exe").val();
    var game_link = $("#game_link").val();
    var game_style = $("#game_style").val();

    var flag = true;
    if (game_name == "") {
        $("#game_name").addClass("required");
        flag = false;
    } else if (game_exe == "") {
        $("#game_exe").addClass("required");
        flag = false;
    } else if (game_link == "") {
        $("#game_link").addClass("required");
        flag = false;
    } else if (game_style == "") {
        $("#game_style").addClass("required");
        flag = false;
    }

    if (flag) {
        var type = "games";
        $.ajax({
            url: "/ajax/async-config/save",
            dataType: "json",
            type: "POST",
            data: {
                type: type,
                game_index: game_index,
                game_name: game_name,
                game_exe: game_exe,
                game_link: game_link,
                game_style: game_style
            },
            success: function (data) {
                if (data.result == "success") {
                    members = [];
                    if (data.games) {
                        for (var i = 0; i < data.games.length; i++) {
                            members.push([data.games[i].name, data.games[i].exe, data.games[i].link, data.games[i].style]);
                        }
                    }
                    list_games();
                    $(".game-modal-wrapper").bPopup().close();
                }
            }
        });
    }
}

/* delete game modal show when hit delete on Game Defined section */
function delete_game(index) {
    $("#delete_index").val(index);
    $(".delete-game-modal").bPopup({
        positionStyle: 'fixed', //'fixed' or 'absolute'
        position: ['20%', '30%'] //x, y
    });
}

/* delete game action after confirm game on Game defined section */
function confirm_delete() {
    var delete_index = $("#delete_index").val();
    var type = "games";
    $.ajax({
        url: "/ajax/async-config/delete",
        dataType: "json",
        type: "POST",
        data: {type: type, delete_index: delete_index},
        success: function (data) {
            if (data.result == "success") {
                members = [];
                if (data.games) {
                    for (var i = 0; i < data.games.length; i++) {
                        members.push([data.games[i].name, data.games[i].exe, data.games[i].link, data.games[i].style]);
                    }
                }
                list_games();
                $(".delete-game-modal").bPopup().close();
            }
        }
    });
}

/* add compliments event from Compliments section when hit add on admin options php */
function add_compliment() {

    var newcontent = '';
    var type = "compliment";
    $.ajax({
        url: "/ajax/async-config/save",
        dataType: "json",
        type: "POST",
        data: {type: type},
        success: function (data) {
            if (data.result == "success") {
                newcontent += '<tr class="fs1-1em">';
                newcontent += '<td class="pd30-left">';
                newcontent += '<button class="btn btn-success btn-file" onclick="open_upload(this,&#39;img&#39;,' + data.new_dir + ')" data-file="#attach_compliment" type="button">graphic</button>';
                newcontent += '<div class="inline-block ml15" id="compliment_img_' + data.new_dir + '"></div>';
                newcontent += '</td>';
                newcontent += '<td class="left-align">'
                newcontent += '<button class="btn btn-success btn-uploadAudio" onclick="open_upload(this,&#39;audio&#39;,' + data.new_dir + ')" data-file="#attach_compliment" type="button"></button>';
                newcontent += '<div class="inline-block ml15 mt5" id="compliment_audio_' + data.new_dir + '"></div>';
                newcontent += '</td>';
                newcontent += '<td>';
                newcontent += '<div class="select dd-container" onclick="action_dropdown(this)">';
                newcontent += '<div class="dd-select">';
                newcontent += '<a class="dd-selected">actions</a>';
                newcontent += '<span class="dd-pointer dd-pointer-down"></span>';
                newcontent += '</div>';
                newcontent += '<ul class="dd-options">';
                newcontent += '<li><a class="dd-option" onclick="delete_compliment(this,' + data.new_dir + ')"><label class="dd-option-text">delete</label></a></li>';
                newcontent += '</ul>';
                newcontent += '</div>';
                newcontent += '</td>';
                newcontent += '</tr>';

                $('#compliments-table').append(newcontent);
            }
        }
    });
}

/* add audio alert event from Audio Alerts section when hit add on admin options php */
function addAudioAlert() {
    var newcontent = '';
    var type = "audio-alert";
    var timeOptionsHtml = '';
    for (var i = 1; i <= 10; i++) {
        timeOptionsHtml += '<option value="' + i + '">' + i + ':00</option>';
    }

    $.ajax({
        url: "/ajax/async-config/save",
        dataType: "json",
        type: "POST",
        data: {type: type},
        success: function (data) {
            if (data.result == "success") {
                newcontent += '<tr class="fs1-1em" data-id="' + data.new_dir + '">';
                newcontent += '<td class="pd30-left">';
                newcontent += '<select name="audio_time" id="audio_time" class="select">' + timeOptionsHtml + '</select>';
                newcontent += '</td>';
                newcontent += '<td class="left-align">';
                newcontent += '<button class="btn btn-success btn-uploadAudio" onclick="open_upload_alert(this,&#39;audio&#39;,' + data.new_dir + ')" data-file="#attach_audio_alert" type="button"></button>';
                newcontent += '<div class="inline-block ml15 mt5" id="aa_audio_' + data.new_dir + '"></div>';
                newcontent += '</td>';
                newcontent += '<td>';
                newcontent += '<div class="select dd-container" onclick="action_dropdown(this)">';
                newcontent += '<div class="dd-select">';
                newcontent += '<a class="dd-selected">actions</a>';
                newcontent += '<span class="dd-pointer dd-pointer-down"></span>';
                newcontent += '</div>';
                newcontent += '<ul class="dd-options">';
                newcontent += '<li><a class="dd-option" onclick="deleteObject(this,' + data.new_dir + ', &#39;audio-alert&#39)"><label class="dd-option-text">delete</label></a></li>';
                newcontent += '</ul>';
                newcontent += '</div>';
                newcontent += '</td>';
                newcontent += '</tr>';

                $('#audio_alerts_table').append(newcontent);
            }
        }
    });
}
var delete_object;
var delete_dir;

/* delete compliment modal show*/
function delete_compliment(object, dir) {
    delete_object = object;
    delete_dir = dir;
    $(".delete-compliment-modal").bPopup({
        positionStyle: 'fixed', //'fixed' or 'absolute'
        position: ['20%', '30%'] //x, y
    });
}

/* delete Object modal open when delete. just implemented on audio alert section yet*/
function deleteObject(object, id, type) {
    delete_object = object;
    $('div.delete-object-modal h4').text('Are you sure you want to delete this Audio Alert?');
    $('div.delete-object-modal button').attr('onclick', 'confirmedDeleteObject("' + object + '", "' + id + '", "' + type + '")');
    $(".delete-object-modal").bPopup({
        positionStyle: 'fixed', //'fixed' or 'absolute'
        position: ['20%', '30%'] //x, y
    });
}

/* delete Object event just implemented on audio alert section yet*/
function confirmedDeleteObject(object, id, type) {
    $.ajax({
        url: "/ajax/async-config/delete",
        dataType: "json",
        type: "POST",
        data: {type: type, object_id: id},
        success: function (data) {
            if (data.result == "success") {
                $(delete_object).closest("tr").remove();
                $("div.delete-object-modal").bPopup().close();
            }
        }
    });
}

/* action delete compliment after confirm delete compliment*/
function confirm_compliment_delete() {
    var type = "compliment";
    $.ajax({
        url: "/ajax/async-config/delete",
        dataType: "json",
        type: "POST",
        data: {type: type, delete_dir: delete_dir},
        success: function (data) {
            if (data.result == "success") {
                $(delete_object).parents("tr").remove();
                $(".delete-compliment-modal").bPopup().close();
            }
        }
    });
}

/* add person game modal shows when hit add button */
function add_person_game() {
    $(".game-field").removeClass("required");
    $(".game-field").val("");
    $(".person-modal-wrapper").bPopup({
        positionStyle: 'fixed', //'fixed' or 'absolute'
        position: ['20%', '30%'] //x, y
    });
}

/* edit person game modal shows when hit edit button */
function edit_person_game(object, index) {
    $(".game-field").removeClass("required");
    $("#person_index").val(index);
    $("#person_name").val($(object).parents("tr").find(".name").text());
    $("#person_exe").val($(object).parents("tr").find(".exe").text());
    $(".person-modal-wrapper").bPopup({
        positionStyle: 'fixed', //'fixed' or 'absolute'
        position: ['20%', '30%'] //x, y
    });
}

/* add/update person game data on 1st Person Games section on admin options php*/
function save_person_game() {
    $(".game-field").removeClass("required");
    var person_index = $("#person_index").val();
    var person_name = $("#person_name").val();
    var person_exe = $("#person_exe").val();

    var flag = true;
    if (person_name == "") {
        $("#person_name").addClass("required");
        flag = false;
    } else if (person_exe == "") {
        $("#person_exe").addClass("required");
        flag = false;
    }

    if (flag) {
        var type = "persons";
        $.ajax({
            url: "/ajax/async-config/save",
            dataType: "json",
            type: "POST",
            data: {type: type, person_index: person_index, person_name: person_name, person_exe: person_exe},
            success: function (data) {
                if (data.result == "success") {
                    persons = [];
                    if (data.games) {
                        for (var i = 0; i < data.games.length; i++) {
                            persons.push([data.games[i].name, data.games[i].exe]);
                        }
                    }
                    list_persons();
                    $(".person-modal-wrapper").bPopup().close();
                }
            }
        });
    }
}

/* delete person game confirm modal open when hit delete from 1st Person Games section */
function delete_person_game(index) {
    $("#delete_person_index").val(index);
    $(".delete-person-modal").bPopup({
        positionStyle: 'fixed', //'fixed' or 'absolute'
        position: ['20%', '30%'] //x, y
    });
}

function confirm_person_delete() {
    var delete_index = $("#delete_person_index").val();
    var type = "persons";
    $.ajax({
        url: "/ajax/async-config/delete",
        dataType: "json",
        type: "POST",
        data: {type: type, delete_index: delete_index},
        success: function (data) {
            if (data.result == "success") {
                persons = [];
                if (data.games) {
                    for (var i = 0; i < data.games.length; i++) {
                        persons.push([data.games[i].name, data.games[i].exe]);
                    }
                }
                list_persons();
                $(".delete-person-modal").bPopup().close();
            }
        }
    });
}

/* save category, sub category, grade but I don't think this function is used on admin options php*/
function save_category() {
    var type = $("#type").val();
    if (type == "grade") {
        var grade = $("#new-grade").val();
        $.ajax({
            url: "/ajax/async-category/add",
            dataType: "json",
            type: "POST",
            data: {type: type, grade: grade},
            success: function (data) {
                if (data.result == "success") {
                    alert("Category Added!");
                    window.location.href = "admin-options.php"
                    return;
                } else {
                    alert("Category Add Failed!");
                    return;
                }
            }
        });
    } else if (type == "subject") {
        var subject = $("#new-subject").val();
        var grade = $("#choose-grade-2 .dd-selected-value").val();
        $.ajax({
            url: "/ajax/async-category/add",
            dataType: "json",
            type: "POST",
            data: {type: type, grade: grade, subject: subject},
            success: function (data) {
                if (data.result == "success") {
                    alert("Category Added!");
                    window.location.href = "admin-options.php"
                    return;
                } else {
                    alert("Category Add Failed!");
                    return;
                }
            }
        });
    } else if (type == "sub-category") {
        var sub_category = $("#new-sub-category").val();
        var subject = $("#choose-subject-2 .dd-selected-value").val();
        $.ajax({
            url: "/ajax/async-category/add",
            dataType: "json",
            type: "POST",
            data: {type: type, subject: subject, sub_category: sub_category},
            success: function (data) {
                if (data.result == "success") {
                    alert("Category Added!");
                    window.location.href = "admin-options.php"
                    return;
                } else {
                    alert("Category Add Failed!");
                    return;
                }
            }
        });
    }
}

/* delete category but I don't think this function is used on admin options php*/
function delete_category() {
    $("#create-category").css("display", "none");
    $("#delete-category").css("display", "none");
    $("#delete-confirm").css("display", "block");
}

/* delete confirm but I don't think this function is used on admin options php*/
function delete_confirm() {
    var type = $("#type").val();
    if (type == "grade") {
        var grade = $("#choose-grade-1 .dd-selected-value").val();
        $.ajax({
            url: "/ajax/async-category/delete",
            dataType: "json",
            type: "POST",
            data: {type: type, grade: grade},
            success: function (data) {
                if (data.result == "success") {
                    alert("Category Deleted!");
                    window.location.href = "admin-options.php"
                    return;
                } else {
                    alert("Category Delete Failed!");
                    return;
                }
            }
        });
    } else if (type == "subject") {
        var subject = $("#choose-subject-1 .dd-selected-value").val();
        $.ajax({
            url: "/ajax/async-category/delete",
            dataType: "json",
            type: "POST",
            data: {type: type, subject: subject},
            success: function (data) {
                if (data.result == "success") {
                    alert("Category Deleted!");
                    window.location.href = "admin-options.php"
                    return;
                } else {
                    alert("Category Delete Failed!");
                    return;
                }
            }
        });
    } else if (type == "sub-category") {
        var sub_category = $("#choose-sub-category-1 .dd-selected-value").val();
        $.ajax({
            url: "/ajax/async-category/delete",
            dataType: "json",
            type: "POST",
            data: {type: type, sub_category: sub_category},
            success: function (data) {
                if (data.result == "success") {
                    alert("Category Deleted!");
                    window.location.href = "admin-options.php"
                    return;
                } else {
                    alert("Category Delete Failed!");
                    return;
                }
            }
        });
    }
}

/* save several wait time configuration values on Memorization section on admin options */
function save_wait_time() {
    var miss_wait_time = $("#miss_wait_time").val();
    var first_wait_time = $("#first_wait_time").val();
    var second_wait_time = $("#second_wait_time").val();
    var third_wait_time = $("#third_wait_time").val();
    var fourth_wait_time = $("#fourth_wait_time").val();
    var fifth_wait_time = $("#fifth_wait_time").val();
    var sixth_wait_time = $("#sixth_wait_time").val();

    var type = "question_intervals";
    $.ajax({
        url: "/ajax/async-config/save",
        dataType: "json",
        type: "POST",
        data: {
            type: type,
            miss_wait_time: miss_wait_time,
            first_wait_time: first_wait_time,
            second_wait_time: second_wait_time,
            third_wait_time: third_wait_time,
            fourth_wait_time: fourth_wait_time,
            fifth_wait_time: fifth_wait_time,
            sixth_wait_time: sixth_wait_time
        },
        success: function (data) {
            if (data.result == "success") {
                $(".save-modal-wrapper").bPopup({
        positionStyle: 'fixed', //'fixed' or 'absolute'
        position: ['20%', '30%'] //x, y
    });
                return;
            } else {
                return;
            }
        }
    });
}

/* save problem solving configuration value on Problem Solving section on admin options php */
function save_problem_mastery() {
    var problem_mastery = $("#problem-mastery-value").val();

    var type = "problem_mastery";
    $.ajax({
        url: "/ajax/async-config/save",
        dataType: "json",
        type: "POST",
        data: {type: type, problem_mastery: problem_mastery},
        success: function (data) {
            if (data.result == "success") {
                $(".save-modal-wrapper").bPopup({
        positionStyle: 'fixed', //'fixed' or 'absolute'
        position: ['20%', '30%'] //x, y
    });
                return;
            } else {
                return;
            }
        }
    });
}

/* save level points configuration value on Set the Points Needed Per Level section on admin options php */
function save_level_points() {
    var level = $("#level-points .dd-selected-value").val();
    if (level == "") {
        level = "1";
    }
    var value = $("#level-points-value").val();
    var type = "level_points";
    $.ajax({
        url: "/ajax/async-config/save",
        dataType: "json",
        type: "POST",
        data: {type: type, level: level, value: value},
        success: function (data) {
            if (data.result == "success") {
                $(".save-modal-wrapper").bPopup({
        positionStyle: 'fixed', //'fixed' or 'absolute'
        position: ['20%', '30%'] //x, y
    });
                return;
            } else {
                return;
            }
        }
    });
}

/* save professor points configuration value but I don't think this function is used on admin options php its commented */
function save_professor_points() {
    var level = $("#professor-points .dd-selected-value").val();
    if (level == "") {
        level = "1";
    }
    var value = $("#professor-points-value").val();
    var type = "professor_points";
    $.ajax({
        url: "/ajax/async-config/save",
        dataType: "json",
        type: "POST",
        data: {type: type, level: level, value: value},
        success: function (data) {
            if (data.result == "success") {
                $(".save-modal-wrapper").bPopup({
        positionStyle: 'fixed', //'fixed' or 'absolute'
        position: ['20%', '30%'] //x, y
    });
                return;
            } else {
                return;
            }
        }
    });
}

/* trigger file upload even when hit click upload button(labeled as graphic, audio icon file) on Compliment section */
function open_upload(object, type, dir) {
    $("#sub_directory").val(dir);
    $("#compliment_type").val(type);
    if (type == "img") {
        var html_id = "#compliment_img_" + dir;
        var src = $(html_id).find("img");
    } else if (type == "audio") {
        var html_id = "#compliment_audio_" + dir;
        var src = $(html_id).find("audio");
    }
    if (src.length == 0) {
        $("#old_file").val("");
    } else {
        src = src[0].getAttribute("src");
        var old_file = src.replace("upload/compliments/" + dir + "/", "");
        $("#old_file").val(old_file);
    }

    var file_el_id = $(object).data('file');

    $(file_el_id).click();
}

/* trigger file upload even when hit click upload button(audio icon file) on Audio Alert section */
function open_upload_alert(object, type, dir) {
    $("form#attach_form_aa #audio_alert_id").val(dir);
    $("form#attach_form_aa #type").val(type);
    var html_id = '';
    if (type == "img") {
        html_id = "#aa_img_" + dir;
        var src = $(html_id).find("img");
    } else if (type == "audio") {
        html_id = "#aa_audio_" + dir;
        var src = $(html_id).find("audio");
    }
    if (src.length == 0) {
        $("form#attach_form_aa #aa_old_file").val("");
    } else {
        src = src[0].getAttribute("src");
        var old_file = src.replace("upload/compliments/" + dir + "/", "");
        $("form#attach_form_aa #aa_old_file").val(old_file);
    }

    var file_el_id = $(object).data('file');
    $(file_el_id).click();
}

/* update compliment file and image to server event - ajax form submit*/
function upload_compliment() {
    var compliment_type = $("#compliment_type").val();
    var sub_directory = $("#sub_directory").val();

    if (typeof FileReader !== "undefined") {
        var file = document.getElementById('attach_compliment').files[0];
        // check file type
        var file_type = file.type;
        if (compliment_type == "img" && file_type !== 'image/jpeg' && file_type !== 'image/png') {
            return;
        } else if (compliment_type == "audio" && file_type !== 'audio/mp3' && file_type !== 'audio/mpeg') {
            return;
        }
    }

    $("#attach_form_compliment").ajaxForm({
        success: function (data) {
            if (data != "failed") {
                if (compliment_type == "img") {
                    var html_id = "#compliment_img_" + sub_directory;
                    var content = '<img src="' + data + '" class="attach_images">';
                    $(html_id).html(content);
                } else if (compliment_type == "audio") {
                    var html_id = "#compliment_audio_" + sub_directory;
                    var content = '<audio controls="controls" src="' + data + '" type="audio/mpeg"></audio>';
                    $(html_id).html(content);
                }
            }
        }
    }).submit();
}

/* update audio alert table event with audio time */
$(document).on('change', 'select#audio_time', function (e) {
    var thisObj = $(this);
    var id = $(this).closest('tr').data('id');
    $.ajax({
        url: "/ajax/async-upload-audio-alert",
        dataType: "json",
        type: "POST",
        data: {type: 'audio-time', audio_alert_id: id, audio_time: thisObj.val()},
        success: function (data) {
            if (data.result == "success") {
                $(".save-modal-wrapper").bPopup({
        positionStyle: 'fixed', //'fixed' or 'absolute'
        position: ['20%', '30%'] //x, y
    });
                return;
            } else {
                return;
            }
        }
    });
});

/* update audio alert file to server*/
function upload_audio_alert() {
    var type = $("form#attach_form_aa #type").val();
    var audioAlertId = $("form#attach_form_aa #audio_alert_id").val();

    if (typeof FileReader !== "undefined") {
        var file = document.getElementById('attach_audio_alert').files[0];
        // check file type
        var file_type = file.type;
        if (type == "img" && file_type !== 'image/jpeg' && file_type !== 'image/png') {
            return;
        } else if (type == "audio" && file_type !== 'audio/mp3' && file_type !== 'audio/mpeg') {
            return;
        }
    }

    $("#attach_form_aa").ajaxForm({
        success: function (data) {
            var html_id = '';
            var content = '';
            if (data != "failed") {
                if (type == "img") {
                    html_id = "#aa_img_" + audioAlertId;
                    content = '<img src="' + data + '" class="attach_images">';
                    $(html_id).html(content);
                } else {
                    html_id = "#aa_audio_" + audioAlertId;
                    content = '<audio controls="controls" src="' + data + '" type="audio/mpeg"></audio>';
                    $(html_id).html(content);
                }
            }
        }
    }).submit();
}

/* save reports email configuration value on App Reports Email section on admin options php */
function save_all_reports() {
    var all_reports = $("#all_reports").val();

    var type = "all_reports";
    $.ajax({
        url: "/ajax/async-config/save",
        dataType: "json",
        type: "POST",
        data: {type: type, all_reports: all_reports},
        success: function (data) {
            if (data.result == "success") {
                $(".save-modal-wrapper").bPopup({
        positionStyle: 'fixed', //'fixed' or 'absolute'
        position: ['20%', '30%'] //x, y
    });
                return;
            } else {
                return;
            }
        }
    });
}
/*
 function save_new_mastered_num(){
 var new_mastered_num = $("#new_mastered_num").val();

 var type = "new_mastered_num";
 $.ajax({
 url: "/ajax/async-config/save",
 dataType : "json",
 type : "POST",
 data : { type : type, new_mastered_num : new_mastered_num },
 success : function(data){
 if(data.result == "success"){
 $(".save-modal-wrapper").bPopup({
        positionStyle: 'fixed', //'fixed' or 'absolute'
        position: ['20%', '30%'] //x, y
    });
 return;
 }else{
 return;
 }
 }
 });
 }
 */
/* save check report interval but I don't think this function is used on admin options php, this code parts is commented */
function save_report_check_interval() {
    /* save reports email configuration value But I don't think this function is used on admin options php cause this code part is commented*/
    var report_check_interval = $("#report_check_interval").val();

    var type = "report_check_interval";
    $.ajax({
        url: "/ajax/async-config/save",
        dataType: "json",
        type: "POST",
        data: {type: type, report_check_interval: report_check_interval},
        success: function (data) {
            if (data.result == "success") {
                $(".save-modal-wrapper").bPopup({
        positionStyle: 'fixed', //'fixed' or 'absolute'
        position: ['20%', '30%'] //x, y
    });
                return;
            } else {
                return;
            }
        }
    });
}

/* save memorization mastery configuration value on Memorization section on admin options php */
function save_memorization_mastery() {
    var memorization_mastery = $("#memorization-mastery-value").val();

    var type = "memorization_mastery";
    $.ajax({
        url: "/ajax/async-config/save",
        dataType: "json",
        type: "POST",
        data: {type: type, memorization_mastery: memorization_mastery},
        success: function (data) {
            if (data.result == "success") {
                $(".save-modal-wrapper").bPopup({
        positionStyle: 'fixed', //'fixed' or 'absolute'
        position: ['20%', '30%'] //x, y
    });
                return;
            } else {
                return;
            }
        }
    });
}

/* save free days configuration value on Free Days Before Paid section on admin options php */
function save_free_days() {
    var free_days = $("#free_days").val();

    var type = "free_days";
    $.ajax({
        url: "/ajax/async-config/save",
        dataType: "json",
        type: "POST",
        data: {type: type, free_days: free_days},
        success: function (data) {
            if (data.result == "success") {
                $(".save-modal-wrapper").bPopup({
        positionStyle: 'fixed', //'fixed' or 'absolute'
        position: ['20%', '30%'] //x, y
    });
                return;
            } else {
                return;
            }
        }
    });
}

/* save time_award value */
function save_time_award() {
    var time_award_value = $("select#time_award_value").val();

    var type = "time_award";
    $.ajax({
        url: "/ajax/async-config/save",
        dataType: "json",
        type: "POST",
        data: {type: type, time_award_value: time_award_value},
        success: function (data) {
            if (data.result == "success") {
                $(".save-modal-wrapper").bPopup({
        positionStyle: 'fixed', //'fixed' or 'absolute'
        position: ['20%', '30%'] //x, y
    });
                return;
            } else {
                return;
            }
        }
    });
}

/* save max time value */
function save_max_time()
{
    var max_time_value = $('select#max_time_value').val();

    var type = "max_time";
    $.ajax({
        url: "/ajax/async-config/save",
        dataType: "json",
        type: "POST",
        data: {type: type, max_time_value: max_time_value},
        success: function (data) {
            if (data.result == "success") {
                $(".save-modal-wrapper").bPopup({
        positionStyle: 'fixed', //'fixed' or 'absolute'
        position: ['20%', '30%'] //x, y
    });
                return;
            } else {
                return;
            }
        }
    });

}
function close_saveModal() {
    $(".save-modal-wrapper").bPopup().close();
}

$.ajaxSetup({
    headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

var items_per_page = 20;

/* BEING USED */
function pageselectCallback(page_index) {
    // Item per page



    var max_elem = Math.min((page_index + 1) * items_per_page, members.length);
    var newcontent = '';    

    // Iterate through a selection of the content and build an HTML string
    for (var i = page_index * items_per_page; i < max_elem; i++) {
       
       //debugger;
        newcontent += '<div class="row">';      
        newcontent += '<div class="col-3 py-2 remove-border-sm border-bottom text-start">';      
        if (members[i][0] != "none") {
            newcontent += '<a class="text-dark text-decoration-none" href="/reports/' + members[i][1] + '">' + members[i][1] + '</a>';
        } else {
            newcontent += '<span class="text-dark text-decoration-none">' + members[i][1] + '</span>';
        }
        newcontent += '</div>';
       
    
        newcontent += '<div class="col-9 py-2 border-bottom text-start">';
        newcontent += '<span class="text-muted fw-bold d-sm-none">Test: <br></span>';
        if (members[i][0] != "none") {
            newcontent += '<span onmouseover="open_tooltip(this)" onmouseout="close_tooltip(this)">';
            newcontent += '<a class="text-dark text-decoration-none" href="/reports/' + members[i][1] + '">';
            newcontent += '<span class="btn btn-sm btn-primary btn-app-secondary">' + test_name_before_colon(members[i][2]) + '</span>';
            newcontent += '<span> - ' + test_name_after_colon(members[i][2]) + '</span>';
            newcontent += '<span> - ' + removeEverythingAfterDash(members[i][5]) + '</span>';
            newcontent += '</a>';
            newcontent += '</span>';
        }
        newcontent += '<div class="tooltip-testinfo d-none">';
        newcontent += '<ul>';
        newcontent += '<li><img class="test-attachment" src="' + members[i][3] + '"></li>';
        newcontent += '</ul>';
        newcontent += '</div>';
        newcontent += '</div>';
        newcontent += '</div>';
    }

    // Replace old content with new content
    $('#choose-tests-table').html(newcontent);

    highlightElement();

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

    // history dropdown
    //$("#history-date").ddslick();
});


/* add one test to que page this function is used on que.js*/
function add_que(object, test_id) {
    var type = "que";
    $.ajax({
        url: "/ajax/async-tests/add",
        dataType: "json",
        type: "POST",
        data: { type: type, test_id: test_id },
        success: function(data) {
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

/* NOT BEING USED */
/* get histories list by filter history */
function filter_history() {
    var session_token = $("#session_token").val();
    var track_history = $("#history-date .dd-selected-value").val();

    var type = "track_history";    

    $.ajax({
        url: "/ajax/async-tests/get",
        dataType: "json",
        type: "POST",
        data: { type: type, track_history: track_history, session_token: session_token },
        success: function(data) {
            if (data.result == "success") {
                members = [];
                if (data.test) {
                    history_date = "";
                    for (var i = 0; i < data.test.length; i++) {
                        var time_stamp = data.test[i].submitted_time + time_difference;
                        var date = new Date(time_stamp);
                        report_date = (date.getMonth() + 1).toString() + "-" + (date.getDate() + 1).toString() + "-" + date.getFullYear().toString().substr(2, 2);
                        if (report_date != history_date) {
                            history_date = report_date;
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
                            members.unshift([data.test[i].test_id, history_date, data.test[i].test_name, data.test[i].attachment, info, data.test[i].grade_subject]);
                        }
                    }
                } else {
                    members.push(['none', 'No history in this duration', '', '', '', '']);
                }
                var opt = getOptionsFromForm();
                // Re-create pagination content with new parameters
                $("#Pagination").pagination(members.length, opt);

            } else if (data.result == "expire") {
                $('#session-expired-modal').modal();
            }
        }
    });

}


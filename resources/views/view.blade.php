@extends('layouts.app')

@section('title', 'View Test - GameSmartz')
@section('description', 'View Test - GameSmartz')

@push('head')
<?php
/* set test id by GET params id */
$test_id = $_GET['id'];
if (isset($_GET['user'])) {
    $user_id = $_GET['user'];
} else {
    $user_id = $loggedUser->user_id;
}

/* get test one row from test table by test id - primary id  */
$sql = "SELECT test_type, subject_id, test_name, video_link, form_options, max_height, gradeless FROM test WHERE test_id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $test_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($test_type, $subject_id, $test_name, $video_link, $form_options, $max_height, $gradeless);
$stmt->fetch();
$stmt->free_result();
$stmt->close();
?>
<script type="text/javascript">
    var test_id = '<?php echo $test_id; ?>';
    var test_type = '<?php echo $test_type; ?>';
    var questions = [];
</script>
<?php
/* get questions list from question table by table id  */
$sql = "SELECT question_id, question_title, attach_image, attach_audio, show_button, correct_note FROM question WHERE test_id = ? ORDER BY question_id";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $test_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($question_id, $question_title, $attach_image, $attach_audio, $show_button, $correct_note);

/* get answers from answers table by question id this query is used inside question loop */
$sql = "SELECT text, attachment, correct FROM answer WHERE question_id = ?";
$stmt_1 = $db->prepare($sql);
$stmt_1->bind_param("s", $question_id);

$ids_array = array();

/* This showing process is same as preview page */

if ($test_type == "multiple") {
    while ($stmt->fetch()) {
        $ids_array[] = $question_id;

        $stmt_1->execute();
        $stmt_1->store_result();
        $stmt_1->bind_result($text, $answer_attach, $correct);
        $j = 0;
        while ($stmt_1->fetch()) {
            $answer[$j]['text'] = $text;
            $answer[$j]['attach'] = $answer_attach;
            $answer[$j]['correct'] = $correct;
            $j++;
        }
        $stmt_1->free_result();

        for ($k = $j; $k < 5; $k++) {
            $answer[$k]['text'] = "";
            $answer[$k]['attach'] = "";
            $answer[$k]['correct'] = 0;
        }

        // this makes a <p> tag around the text following a . (period) - this make a line break on every period
        // $question_title = str_replace(array(". ", "? "), array(".<p>", "?<p>"), $question_title);
        $title_array = explode("<p>", $question_title);
        $question_title = "";
        foreach ($title_array as $p) {
            if ($p != "")
                $question_title .= "<p>" . $p . "</p>";
        }
        // this makes a <p> tag around the text following a . (period) - this make a line break on every period
        // $question_title = str_replace(array("\r\n", "\r", "\n"), "<br>", $question_title);
        $correct_note = str_replace(array("\r\n", "\r", "\n"), " ", $correct_note);

?>
        <script type="text/javascript">
            questions['<?php echo $question_id; ?>'] = [
                '<?php echo str_replace("'", "&#39;", $question_title); ?>'.replace("&#39;", "'"),
                '<?php echo str_replace("'", "&#39;", $attach_image); ?>'.replace("&#39;", "'"),
                '<?php echo str_replace("'", "&#39;", $attach_audio); ?>'.replace("&#39;", "'"),
                '<?php echo $show_button; ?>',
                '<?php echo str_replace("'", "&#39;", $answer[0]['text']); ?>'.replace("&#39;", "'"),
                '<?php echo $answer[0]['correct']; ?>',
                '<?php echo str_replace("'", "&#39;", $answer[0]['attach']); ?>'.replace("&#39;", "'"),
                '<?php echo str_replace("'", "&#39;", $answer[1]['text']); ?>'.replace("&#39;", "'"),
                '<?php echo $answer[1]['correct']; ?>',
                '<?php echo str_replace("'", "&#39;", $answer[1]['attach']); ?>'.replace("&#39;", "'"),
                '<?php echo str_replace("'", "&#39;", $answer[2]['text']); ?>'.replace("&#39;", "'"),
                '<?php echo $answer[2]['correct']; ?>',
                '<?php echo str_replace("'", "&#39;", $answer[2]['attach']); ?>'.replace("&#39;", "'"),
                '<?php echo str_replace("'", "&#39;", $answer[3]['text']); ?>'.replace("&#39;", "'"),
                '<?php echo $answer[3]['correct']; ?>',
                '<?php echo str_replace("'", "&#39;", $answer[3]['attach']); ?>'.replace("&#39;", "'"),
                '<?php echo str_replace("'", "&#39;", $answer[4]['text']); ?>'.replace("&#39;", "'"),
                '<?php echo $answer[4]['correct']; ?>',
                '<?php echo str_replace("'", "&#39;", $answer[4]['attach']); ?>'.replace("&#39;", "'"),
                '<?php echo str_replace("'", "&#39;", $correct_note); ?>'.replace("&#39;", "'")
            ];
        </script>
    <?php
    }
} elseif ($test_type == "fill") {
    while ($stmt->fetch()) {
        $ids_array[] = $question_id;

        $stmt_1->execute();
        $stmt_1->store_result();
        $stmt_1->bind_result($text, $answer_attach, $correct);
        $stmt_1->fetch();
        $stmt_1->free_result();

        // this makes a <p> tag around the text following a . (period) - this make a line break on every period
        // $question_title = str_replace(array(". ", "? "), array(".<p>", "?<p>"), $question_title);
        $title_array = explode("<p>", $question_title);
        $question_title = "";
        foreach ($title_array as $p) {
            if ($p != "")
                $question_title .= "<p>" . $p . "</p>";
        }
        // this makes a <p> tag around the text following a . (period) - this make a line break on every period
        // $question_title = str_replace(array("\r\n", "\r", "\n"), "<br>", $question_title);
        $correct_note = str_replace(array("\r\n", "\r", "\n"), " ", $correct_note);

    ?>
        <script type="text/javascript">
            questions['<?php echo $question_id; ?>'] = [
                '<?php echo str_replace("'", "&#39;", $question_title); ?>'.replace("&#39;", "'"),
                '<?php echo str_replace("'", "&#39;", $attach_image); ?>'.replace("&#39;", "'"),
                '<?php echo str_replace("'", "&#39;", $attach_audio); ?>'.replace("&#39;", "'"),
                '<?php echo $show_button; ?>',
                '<?php echo str_replace("'", "&#39;", $text); ?>'.replace("&#39;", "'"),
                '<?php echo str_replace("'", "&#39;", $correct_note); ?>'.replace("&#39;", "'")
            ];
        </script>
    <?php
    }
} elseif ($test_type == "spelling") {
    while ($stmt->fetch()) {
        $ids_array[] = $question_id;

        $stmt_1->execute();
        $stmt_1->store_result();
        $stmt_1->bind_result($text, $answer_attach, $correct);
        $stmt_1->fetch();
        $stmt_1->free_result();

        $correct_note = str_replace(array("\r\n", "\r", "\n"), " ", $correct_note);
    ?>
        <script type="text/javascript">
            questions['<?php echo $question_id; ?>'] = [
                '<?php echo str_replace("'", "&#39;", $question_title); ?>'.replace("&#39;", "'"),
                '<?php echo str_replace("'", "&#39;", $text); ?>'.replace("&#39;", "'"),
                '<?php echo str_replace("'", "&#39;", $correct_note); ?>'.replace("&#39;", "'")
            ];
        </script>
<?php
    }
}

$stmt->free_result();
$stmt->close();

$stmt_1->close();

// consider difference between server and broswer time
$server_time = time() * 1000; // second to milisecond
?>
<script type="text/javascript">
    var members = [];
    var server_time = parseInt('<?php echo $server_time; ?>');
    var browser_time = new Date();
    var time_difference = browser_time.getTime() - server_time;
    var report_date = '';
    var increase_index = 0;
    var current_member = 0;
    var submitted_time = 0;
    var date_object;
    var date_string;
</script>
<?php
$ids_string = implode(",", $ids_array);
$selected_id = $_GET['index'];
$sql = "SELECT submitted_time FROM answer_summary WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $selected_id);
$stmt->execute();
$stmt->store_result();
$row = $stmt->num_rows();
$stmt->bind_result($selected_time);
$stmt->fetch();
$stmt->free_result();
$stmt->close();

if ($row != 0) {

    $index_stamp = strtotime($selected_time) * 1000;   // second to milisecond
?>
    <script type="text/javascript">
        var selected_index = '<?php echo $selected_id; ?>';
        var index_stamp = parseInt('<?php echo $index_stamp; ?>') + time_difference;
        var date = new Date(index_stamp);
        report_date = (date.getMonth() + 1).toString() + "-" + date.getDate().toString() + "-" + date.getFullYear().toString().substr(2, 2);
    </script>
    <?php

    $report_date_before = date("Y-m-d", strtotime($selected_time) - 24 * 3600);
    $report_date_after = date("Y-m-d", strtotime($selected_time) + 24 * 3600);

    $sql = "SELECT id, question_id, answer, correct, submitted_time
            FROM answer_summary
            WHERE user_id = ? ";
    if (!empty($ids_string)) {
        $sql .= "AND question_id in (" . $ids_string . ") ";
    }
    $sql .= "AND date(submitted_time) BETWEEN ? AND ?
            ORDER BY submitted_time ASC
        ";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("sss", $user_id, $report_date_before, $report_date_after);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $question_id, $answer, $correct, $submitted_time);

    while ($stmt->fetch()) {
        $submitted_time = strtotime($submitted_time) * 1000; // second to milisecond
    ?>
        <script type="text/javascript">
            submitted_time = parseInt('<?php echo $submitted_time; ?>') + time_difference;
            date_object = new Date(submitted_time);
            date_string = (date_object.getMonth() + 1).toString() + "-" + date_object.getDate().toString() + "-" + date_object.getFullYear().toString().substr(2, 2);
            if (date_string == report_date) {
                increase_index++;
                if ('<?php echo $id; ?>' == selected_index) {
                    current_member = increase_index;
                }
                if (test_type == "multiple") {
                    members.push([
                        '<?php echo $question_id; ?>',
                        '<?php echo str_replace("'", "&#39;", $answer); ?>'.replace("&#39;", "'"),
                        parseInt('<?php echo $correct; ?>'),
                        questions['<?php echo $question_id; ?>'][0],
                        questions['<?php echo $question_id; ?>'][1],
                        questions['<?php echo $question_id; ?>'][2],
                        questions['<?php echo $question_id; ?>'][3],
                        questions['<?php echo $question_id; ?>'][4],
                        questions['<?php echo $question_id; ?>'][5],
                        questions['<?php echo $question_id; ?>'][6],
                        questions['<?php echo $question_id; ?>'][7],
                        questions['<?php echo $question_id; ?>'][8],
                        questions['<?php echo $question_id; ?>'][9],
                        questions['<?php echo $question_id; ?>'][10],
                        questions['<?php echo $question_id; ?>'][11],
                        questions['<?php echo $question_id; ?>'][12],
                        questions['<?php echo $question_id; ?>'][13],
                        questions['<?php echo $question_id; ?>'][14],
                        questions['<?php echo $question_id; ?>'][15],
                        questions['<?php echo $question_id; ?>'][16],
                        questions['<?php echo $question_id; ?>'][17],
                        questions['<?php echo $question_id; ?>'][18],
                        questions['<?php echo $question_id; ?>'][19]
                    ]);
                } else if (test_type == "fill") {
                    members.push([
                        '<?php echo $question_id; ?>',
                        '<?php echo str_replace("'", "&#39;", $answer); ?>'.replace("&#39;", "'"),
                        parseInt('<?php echo $correct; ?>'),
                        questions['<?php echo $question_id; ?>'][0],
                        questions['<?php echo $question_id; ?>'][1],
                        questions['<?php echo $question_id; ?>'][2],
                        questions['<?php echo $question_id; ?>'][3],
                        questions['<?php echo $question_id; ?>'][4],
                        questions['<?php echo $question_id; ?>'][5]
                    ]);
                } else if (test_type == "spelling") {
                    members.push([
                        '<?php echo $question_id; ?>',
                        '<?php echo str_replace("'", "&#39;", $answer); ?>'.replace("&#39;", "'"),
                        parseInt('<?php echo $correct; ?>'),
                        questions['<?php echo $question_id; ?>'][0],
                        questions['<?php echo $question_id; ?>'][1],
                        questions['<?php echo $question_id; ?>'][2]
                    ]);
                }
            }
        </script>
        <?php
    }

    $stmt->free_result();
    $stmt->close();
}

if ($test_type == "multiple" || $test_type == "fill") {
    $dirname = "upload/img/" . $test_id;
    if (is_dir($dirname)) {
        $dir_handle = opendir($dirname);
        if ($dir_handle) {
        ?>
            <script type="text/javascript">
                var imageObjs = [];
            </script>
            <?php
            while ($file = readdir($dir_handle)) {
                if ($file != "." && $file != "..") {
            ?>
                    <script type="text/javascript">
                        var imageObj = new Image();
                        imageObj.src = '<?php echo str_replace("'", "&#39;", $dirname . "/" . $file); ?>'.replace("&#39;", "'");
                        imageObjs.push(imageObj);
                    </script>
<?php
                }
            }
        }
        closedir($dir_handle);
    }
}
?>
<style>
        .app-footer {
            display: none;
        }
    </style>
@endpush

@push('foot')
<script src="/assets/js/page/view.js"></script>
@endpush

@section('content')
<input type="hidden" id="video_id" value="">
<div class="modal fade modal-youtube-video test-video-modal" id="torchlight-video" tabindex="-1" aria-labelledby="torchlight-video" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-body p-2">
                <div id="video_player"></div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid background-volcano">
    <div class="row">
        <div class="col-md-12 py-4">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6 offset-sm-3 question-form text-center">
                        <h4 class="text-white pt-3"><?php echo $test_name; ?></h4>
                        <div style="height: 75vh; display: flex; justify-content: space-between; align-items: center; flex-direction: column; margin-top: 10px;">
                            <?php
                            if ($form_options != "") {
                                $form_options = unserialize($form_options);
                                $style_font = "font-size:" . $form_options['font'] . ";";
                                $style_width = "width:" . $form_options['width'] . "px;";
                            } else {
                                $style_font = "";
                                $style_width = "";
                            }
                            ?>

                            <script type="text/javascript">
                                var form_options = '<?php echo $style_font; ?>';
                            </script>

                            <div id="question_form" class="bg-dark pb-3" style="width: 100%; max-width: 375px; border-radius: 5px;"></div>
                            <div id="player"></div>
                            <div>
                                <?php
                                $back_to = "";
                                if (isset($_GET['from'])) {
                                    $back_to .= $_GET['from'] . ".php";
                                    if (isset($_GET['date'])) {
                                        $back_to .= "?date=" . $_GET['date'];
                                        if (isset($_GET['user'])) {
                                            $back_to .= "&user=" . $_GET['user'];
                                        }
                                    } else if (isset($_GET['report_id'])) {
                                        $back_to .= "?id=" . $_GET['report_id'];
                                    }
                                }
                                ?>
                                <div id="Pagination" class="pagination" style="display: inline-block;"></div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@php
// IN WEB TEST QUESTION PAGE
@endphp

<?php
use Illuminate\Support\Facades\DB;
$test_id = $test->test_id;

$sql = "SELECT option_value FROM config WHERE option_name = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $option_name);

// chains the sql call, changing the ? every time its called, this is because there is no 'key' with config db, the values are all unique
$option_name = "memorization_mastery";
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($option_value);
$stmt->fetch();
$stmt->free_result();
$memorization_mastery = $option_value;

$option_name = "problem_mastery";
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($option_value);
$stmt->fetch();
$stmt->free_result();
$problem_mastery = $option_value;

$option_name = "max_time";
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($option_value);
$stmt->fetch();
$stmt->free_result();
$max_time = $option_value;

$option_name = "overlay_version";
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($overlay_version);
$stmt->fetch();
$stmt->free_result();
$overlay_version = trim($overlay_version);

$option_name = "time_award";
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($option_value);
$stmt->fetch();
$stmt->free_result();
$stmt->close();
$admin_time_award = $option_value;

$sql = "SELECT mastery_type, mastery_number, test_type, subject_id, test_name, video_link, form_options, max_height, gradeless, award_time, subject_extra_name FROM test WHERE test_id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $test_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($mastery_type, $mastery_number, $test_type, $subject_id, $test_name, $video_link, $form_options, $max_height, $gradeless, $award_time, $subject_extra_name);
$stmt->fetch();
$stmt->free_result();
$stmt->close();
?>

@section('title', 'Take Your Test, Get Time and Awards - GameSmartz')
@section('description', 'Choose a Test and get Busy Learning')

@push('head')
<link rel="stylesheet" href="{{ asset('assets/plugin/devbridge-autocomplete/devbridge-autocomplete-non-game.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/question-progress.css') }}" />
<link href="https://fonts.googleapis.com/css?family=Luckiest+Guy&display=swap" rel="stylesheet">
<style>
    .app-footer {
        display: none;
    }
</style>
@endpush

@push('foot')
<script src="/assets/plugin/devbridge-autocomplete/jquery.autocomplete.min.js"></script>
    <?php // Page Specific js - Needed to write correc answer data to charts 
    ?>
    <script src="/assets/plugin/chart/js/highcharts.js"></script>
    <script src="/assets/plugin/chart/js/modules/data.js"></script>
    <script src="/assets/plugin/chart/js/modules/exporting.js"></script>

    <script type="text/javascript">
        var members = [];
    </script>

    <?php

    // this checks to see if award_time is blank and if so, uses the global $admin_time_award, but this will never be blank, after the last update, but keeping the fall back anyways - 5-11-18
    if ($award_time == '')
        $award_time = $admin_time_award;

    // at one point a test could have a different mastery number set than the rest of the tests (for harder or easier tests). That is why this value is here, 
    // however right now we are setting the mastery number in the global config, so this local value will be largely blank, and in this case is will use the global value grabbed above. - 5-11-18
    // this following squence tranfers the values from php to js, depending on what type of test we are taking
    if ($mastery_number == "") {
        if ($mastery_type == "memorization") {
    ?>
            <script type="text/javascript">
                var mastery_number = parseInt('<?php echo $memorization_mastery; ?>');
            </script>
        <?php
        } else {
        ?>
            <script type="text/javascript">
                var mastery_number = parseInt('<?php echo $problem_mastery; ?>');
            </script>
        <?php
        }
    } else {
        ?>
        <script type="text/javascript">
            var mastery_number = parseInt('<?php echo $mastery_number; ?>');
        </script>
    <?php
    }

    // redefine sql statement
    $sql = "SELECT total_points, audio, zoom, user_level FROM users WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $user_id = $loggedUser->user_id;
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($total_points, $audio, $zoom, $user_level);
    $stmt->fetch();
    $stmt->free_result();
    $stmt->close();

    /* get today total answered  */
    $date = new DateTime("now");
    $current_date = $date->format("n-j-y g:iA");
    $current_date_array = explode(" ", $current_date);

    $sql = "SELECT * FROM cron_email WHERE user_id = $user_id AND SUBSTRING_INDEX(SUBSTRING_INDEX(report_date, ' ', 1), ' ', -1) = '" . $current_date_array[0] . "'";
    $cron_email = DB::select($sql);
    if ($cron_email) :
        $cron_email = json_decode(json_encode($cron_email), true);
        $today_total_answered = 2 * $cron_email[0]['correct_answer_num'] - $cron_email[0]['total_answer_num'];
    else :
        $today_total_answered = 0;
    endif;
    ?>

    <script type="text/javascript">
        var max_time = parseInt('<?php echo get_seconds($max_time); ?>');
        var total_points = parseInt('<?php echo $today_total_answered; ?>');
        var toggled = true;
        var zoom = parseInt('<?php echo $zoom; ?>');
        var test_id = parseInt('<?php echo $test_id; ?>');
        var mastery_type = '<?php echo $mastery_type; ?>';
        var completion_num = 0;
        var pre_test = 0;
        var post_test = -1;
        var test_result = [];
        var question_history = [];
        var question_start_point = 0;
        var correct_answered = 0;
        var total_answered = 0;
        var playFlag = false;
        var audioAlerts = <?php echo json_encode($audioAlerts) ?>;
        var user_level = <?php echo $user_level ?>
    </script>
    <?php
    /* get questions from question table by test id order by question id ASC */
    $sql = "SELECT question_id, question_title, attach_image, attach_audio, show_image, show_button, correct_note, full_audio, wiki_link FROM question WHERE test_id = ? ORDER BY question_id";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $test_id);

    $stmt->execute();
    $stmt->store_result();
    $question_length = $stmt->num_rows();
    $stmt->bind_result($question_id, $question_title, $attach_image, $attach_audio, $show_image, $show_button, $correct_note, $full_audio, $wiki_link);

    /* get answers from answers table by question id inside question loop not ready for execute */
    $sql = "SELECT text, attachment, correct FROM answer WHERE question_id = ?";
    $stmt_2 = $db->prepare($sql);
    $stmt_2->bind_param("s", $question_id);

    /* get user_test one row from user_test table by user id and test id inside question loop not ready for execute */
    $sql = "SELECT id, test_info, question_history, completion_rate, pre_test, post_test, question_start_point FROM user_test WHERE user_id = ? AND test_id = ?";
    $stmt_1 = $db->prepare($sql);
    $stmt_1->bind_param("ss", $user_id, $test_id);

    $user_id = $loggedUser->user_id;
    $stmt_1->execute();
    $stmt_1->store_result();
    $row_1 = $stmt_1->num_rows();
    $stmt_1->bind_result($user_test_id, $test_info, $question_history, $completion_rate, $pre_test, $post_test, $question_start_point);
    $stmt_1->fetch();
    $stmt_1->free_result();
    $stmt_1->close();

    // study start
    if ($row_1 != 0) {
        $sql = "UPDATE user_test SET start_date = now() WHERE id = ?";
        $stmt_1 = $db->prepare($sql);
        $stmt_1->bind_param("s", $user_test_id);
        $stmt_1->execute();
        $stmt_1->close();

        if ($mastery_number != "") {
    ?>
            <script type="text/javascript">
                mastery_number = parseInt('<?php echo $mastery_number; ?>');
            </script>
        <?php
        }
        ?>
        <script type="text/javascript">
            pre_test = parseInt('<?php echo $pre_test; ?>');
            post_test = parseInt('<?php echo $post_test; ?>');
        </script>
        <?php
        if (($test_info == "Mastered" || $completion_rate == 100) && $pre_test >= $question_length) {
            $sql = "UPDATE user_test SET test_info = '', completion_rate = 0, question_history = '', question_start_point = 0 WHERE id = ?";
            $stmt_1 = $db->prepare($sql);
            $stmt_1->bind_param("s", $user_test_id);
            $stmt_1->execute();
            $stmt_1->close();
            $question_history = "";
            $completion_rate = 0;
        } else {

            if ($question_history != "") {
                $question_history = unserialize($question_history);
            }
        ?>
            <script type="text/javascript">
                if (mastery_type == "memorization") {
                    completion_num = Math.round(parseInt('<?php echo $question_length; ?>') * parseInt('<?php echo $completion_rate; ?>') / 100);
                } else if (mastery_type == "problem solving") {
                    completion_num = Math.round(mastery_number * parseInt('<?php echo $completion_rate; ?>') / 100);
                }
                question_start_point = parseInt('<?php echo $question_start_point; ?>');
            </script>
    <?php
        }
    } else {
        $sql = "INSERT INTO user_test (user_id, test_id, start_date, test_info, view_video, mastery_date, completion_rate, pre_test, mode_number, question_history, question_start_point, award_time, mastery_number, in_game_mastered ) VALUES (?, ?, now(), '', 0, 0, 0, 0, 0, '', 0, '', '', 0 )";
        $stmt_1 = $db->prepare($sql);
        $stmt_1->bind_param("ss", $user_id, $test_id);
        $stmt_1->execute();
        $stmt_1->close();

        $completion_rate = 0;
    }

    // consider difference between server and broswer time
    $server_time = time() * 1000; // second to milisecond
    ?>
    <script type="text/javascript">
        var server_time = parseInt('<?php echo $server_time; ?>');
        var browser_time = new Date();
        var report_date = (browser_time.getMonth() + 1).toString() + "-" + browser_time.getDate().toString() + "-" + browser_time.getFullYear().toString().substr(2, 2);
        var beginningOfDay = browser_time.getTime() - browser_time.getHours() * 60 * 60 * 1000 - browser_time.getMinutes() * 60 * 1000 - browser_time.getSeconds() * 1000 - browser_time.getMilliseconds();
        var time_difference = browser_time.getTime() - server_time;
        var start_time;
        var submitted_time = 0;
        var correct = 0;
    </script>
    <?php
    /* get mode_number number value from user_test by user id and test id  */
    $sql = "SELECT mode_number FROM user_test WHERE user_id = ? AND test_id = ?";
    $stmt_1 = $db->prepare($sql);
    $stmt_1->bind_param("ss", $user_id, $test_id);
    $stmt_1->execute();
    $stmt_1->store_result();
    $stmt_1->bind_result($mode_number);

    while ($stmt_1->fetch()) {
    ?>
        <script type="text/javascript">
            var mode_number = parseInt('<?php echo $mode_number; ?>');
        </script>
    <?php
    }
    $stmt_1->free_result();
    $stmt_1->close();

    // get answer result
    $sql = "SELECT correct, submitted_time FROM answer_summary WHERE user_id = ? AND submitted_time > DATE_SUB(NOW(), INTERVAL 1 DAY) ORDER BY submitted_time ASC ";
    $stmt_1 = $db->prepare($sql);
    $stmt_1->bind_param("s", $user_id);
    $stmt_1->execute();
    $stmt_1->store_result();
    $stmt_1->bind_result($correct, $submitted_time);
    while ($stmt_1->fetch()) {
        $submitted_time = strtotime($submitted_time) * 1000; // second to milisecond
    ?>
        <script type="text/javascript">
            correct = parseInt('<?php echo $correct; ?>');
            submitted_time = parseInt('<?php echo $submitted_time; ?>') + time_difference;
            if (submitted_time >= beginningOfDay) {
                total_answered++;
                if (correct == 1) {
                    start_time = new Date(submitted_time);
                    correct_answered++;
                    test_result.push({
                        x: Date.UTC(start_time.getFullYear(), start_time.getMonth(), start_time.getDate(), start_time.getHours(), start_time.getMinutes(), start_time.getSeconds()),
                        y: correct_answered,
                        marker: {
                            fillColor: 'rgb(8, 243, 53)'
                        }
                    });
                } else if (correct == 0) {
                    start_time = new Date(submitted_time);
                    test_result.push({
                        x: Date.UTC(start_time.getFullYear(), start_time.getMonth(), start_time.getDate(), start_time.getHours(), start_time.getMinutes(), start_time.getSeconds()),
                        y: correct_answered,
                        marker: {
                            fillColor: 'rgb(245, 64, 50)'
                        }
                    });
                }
            }
        </script>
    <?php
    }
    $stmt_1->free_result();
    $stmt_1->close();

    /* get time_remaining value from users table by logged in id */
    $sql = "SELECT time_remaining FROM users WHERE user_id = ?";
    $stmt_1 = $db->prepare($sql);
    $stmt_1->bind_param("s", $user_id);
    $stmt_1->execute();
    $stmt_1->store_result();
    $stmt_1->bind_result($time_remaining);
    $stmt_1->fetch();
    $stmt_1->free_result();
    $stmt_1->close();

    if ($time_remaining != "") {
        $time_remaining = explode(":", $time_remaining);
        $time_remaining = 60 * intval($time_remaining[0]) + intval($time_remaining[1]);
    } else {
        $time_remaining = get_seconds($award_time); // 1:45
    }
    ?>
    <script type="text/javascript">
        var timer_flag = false;
        var award_time = parseInt('<?php echo get_seconds($award_time) ?>');
        var left_time = parseInt('<?php echo get_seconds($award_time) ?>'); // 1:45
        var time_remaining = parseInt('<?php echo $time_remaining; ?>');
        var last_time = localStorage.getItem('last_time');
        if (last_time != null) {
            var elapsed_time = (browser_time.getTime() - last_time) / 1000; // seconds
            if (elapsed_time < 3600) {
                timer_flag = true;
                if (time_remaining - elapsed_time < 0) {
                    left_time = 0;
                } else {
                    left_time = time_remaining - elapsed_time;
                }
            }
        }
    </script>
    <?php

    $i = 0;
    /**
     * Question loop under several test type(multiple, fill, spelling)
     * save several values to javascript members object to show in html view using jQuery
     * javascript members array format are different whatever test type
     */
    if ($test_type == "multiple") {
        while ($stmt->fetch()) {

            $stmt_2->execute();
            $stmt_2->store_result();
            $stmt_2->bind_result($text, $answer_attach, $correct);
            $j = 0;
            while ($stmt_2->fetch()) {
                $answer[$j]['text'] = $text;
                $answer[$j]['attach'] = $answer_attach;
                $answer[$j]['correct'] = $correct;
                $j++;
            }
            $stmt_2->free_result();

            for ($k = $j; $k < 5; $k++) {
                $answer[$k]['text'] = "";
                $answer[$k]['attach'] = "";
                $answer[$k]['correct'] = 0;
            }

            // code to split the title after a period mark. so everything after a period mark in a sentance is wrapped in its own <p> tag
            // $question_title = str_replace(array(". ", "? "), array(".<p>", "?<p>"), $question_title);
            $title_array = explode("<p>", $question_title);
            $question_title = "";
            foreach ($title_array as $p) {
                if ($p != "")
                    $question_title .= "<p>" . $p . "</p>";
            }
            $question_title = str_replace(array("\r\n", "\r", "\n"), "<br>", $question_title);
            $correct_note = str_replace(array("\r\n", "\r", "\n"), " ", $correct_note);            

            if ($question_history != "") {
                if ($question_history[$i][0] == "") {
    ?>
                    <script type="text/javascript">
                        var history_0 = null;
                    </script>
                <?php
                } else {
                ?>
                    <script type="text/javascript">
                        var history_0 = '<?php echo $question_history[$i][0]; ?>';
                    </script>
                <?php
                }
                if ($question_history[$i][1] == "") {
                ?>
                    <script type="text/javascript">
                        var history_1 = null;
                    </script>
                <?php
                } else {
                ?>
                    <script type="text/javascript">
                        var history_1 = '<?php echo $question_history[$i][1]; ?>';
                    </script>
                <?php
                }
            } else {
                ?>
                <script type="text/javascript">
                    var history_0 = null;
                    var history_1 = null;
                </script>
            <?php
            }
            ?>
            <script type="text/javascript">
                question_history.push([
                    history_0,
                    history_1
                ]);

                members.push([
                    '<?php echo $question_id; ?>',
                    '<?php echo str_replace("'", "&#39;", $question_title); ?>'.replace("&#39;", "'"),
                    '<?php echo str_replace("'", "&#39;", $attach_image); ?>'.replace("&#39;", "'"),
                    '<?php echo str_replace("'", "&#39;", $attach_audio); ?>'.replace("&#39;", "'"),
                    '<?php echo $show_image; ?>',
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
                    '<?php echo str_replace("'", "&#39;", $correct_note); ?>'.replace("&#39;", "'"),
                    history_0,
                    history_1,
                    '<?php echo str_replace("'", "&#39;", $full_audio); ?>'.replace("&#39;", "'"),
                    '<?php echo str_replace("'", "&#39;", $wiki_link); ?>'.replace("&#39;", "'")

                ]);
            </script>
        <?php

            $i++;
        }

        if (file_exists("assets/js/page/test-question/test-question-multiple-" . $overlay_version . ".js")) {
        ?>
            <script src="/assets/js/page/test-question/test-question-multiple-<?php echo $overlay_version; ?>.js?v=1.8"></script>
        <?php
        } else {
        ?>
            <script src="/assets/js/page/test-question/test-question-multiple.js"></script>
            <?php
        }
    } elseif ($test_type == "fill") {
        while ($stmt->fetch()) {

            $stmt_2->execute();
            $stmt_2->store_result();
            $stmt_2->bind_result($text, $answer_attach, $correct);
            $stmt_2->fetch();
            $stmt_2->free_result();

            $question_title = str_replace(array(". ", "? "), array(".<p>", "?<p>"), $question_title);
            $title_array = explode("<p>", $question_title);
            $question_title = "";
            foreach ($title_array as $p) {
                if ($p != "")
                    $question_title .= "<p>" . $p . "</p>";
            }
            $question_title = str_replace(array("\r\n", "\r", "\n"), "<br>", $question_title);
            $correct_note = str_replace(array("\r\n", "\r", "\n"), " ", $correct_note);

            if ($question_history != "") {
                if ($question_history[$i][0] == "") {
            ?>
                    <script type="text/javascript">
                        var history_0 = null;
                    </script>
                <?php
                } else {
                ?>
                    <script type="text/javascript">
                        var history_0 = '<?php echo $question_history[$i][0]; ?>';
                    </script>
                <?php
                }
                if ($question_history[$i][1] == "") {
                ?>
                    <script type="text/javascript">
                        var history_1 = null;
                    </script>
                <?php
                } else {
                ?>
                    <script type="text/javascript">
                        var history_1 = '<?php echo $question_history[$i][1]; ?>';
                    </script>
                <?php
                }
            } else {
                ?>
                <script type="text/javascript">
                    var history_0 = null;
                    var history_1 = null;
                </script>
            <?php
            }
            ?>
            <script type="text/javascript">
                question_history.push([
                    history_0,
                    history_1
                ]);

                members.push([
                    '<?php echo $question_id; ?>',
                    '<?php echo str_replace("'", "&#39;", $question_title); ?>'.replace("&#39;", "'"),
                    '<?php echo str_replace("'", "&#39;", $attach_image); ?>'.replace("&#39;", "'"),
                    '<?php echo str_replace("'", "&#39;", $attach_audio); ?>'.replace("&#39;", "'"),
                    '<?php echo $show_image; ?>',
                    '<?php echo $show_button; ?>',
                    '<?php echo str_replace("'", "&#39;", $text); ?>'.replace("&#39;", "'"),
                    '<?php echo str_replace("'", "&#39;", $correct_note); ?>'.replace("&#39;", "'"),
                    history_0,
                    history_1
                ]);
            </script>
        <?php

            $i++;
        }

        if (file_exists("assets/js/page/test-question/test-question-fill-" . $overlay_version . ".js")) {
        ?>
            <script src="/assets/js/page/test-question/test-question-fill-<?php echo $overlay_version; ?>.js"></script>
        <?php
        } else {
        ?>
            <script src="/assets/js/page/test-question/test-question-fill.js"></script>
            <?php
        }
    } elseif ($test_type == "spelling") {
        while ($stmt->fetch()) {

            $stmt_2->execute();
            $stmt_2->store_result();
            $stmt_2->bind_result($text, $answer_attach, $correct);
            $stmt_2->fetch();
            $stmt_2->free_result();

            $correct_note = str_replace(array("\r\n", "\r", "\n"), " ", $correct_note);
            if ($question_history != "") {
                if ($question_history[$i][0] == "") {
            ?>
                    <script type="text/javascript">
                        var history_0 = null;
                    </script>
                <?php
                } else {
                ?>
                    <script type="text/javascript">
                        var history_0 = '<?php echo $question_history[$i][0]; ?>';
                    </script>
                <?php
                }
                if ($question_history[$i][1] == "") {
                ?>
                    <script type="text/javascript">
                        var history_1 = null;
                    </script>
                <?php
                } else {
                ?>
                    <script type="text/javascript">
                        var history_1 = '<?php echo $question_history[$i][1]; ?>';
                    </script>
                <?php
                }
            } else {
                ?>
                <script type="text/javascript">
                    var history_0 = null;
                    var history_1 = null;
                </script>
            <?php
            }
            ?>
            <script type="text/javascript">
                question_history.push([
                    history_0,
                    history_1
                ]);

                members.push([
                    '<?php echo $question_id; ?>',
                    '<?php echo str_replace("'", "&#39;", $question_title); ?>'.replace("&#39;", "'"),
                    '<?php echo str_replace("'", "&#39;", $text); ?>'.replace("&#39;", "'"),
                    '<?php echo str_replace("'", "&#39;", $correct_note); ?>'.replace("&#39;", "'"),
                    history_0,
                    history_1
                ]);
            </script>
        <?php

            $i++;
        }

        if (file_exists("assets/js/page/test-question/test-question-spelling-" . $overlay_version . ".js")) {
        ?>
            <script src="/assets/js/page/test-question/test-question-spelling-<?php echo $overlay_version; ?>.js"></script>
        <?php
        } else {
        ?>
            <script src="/assets/js/page/test-question/test-question-spelling.js"></script>
    <?php
        }
    }

    $stmt->free_result();
    $stmt->close();

    $stmt_2->close();
    /** End Test Question Loop */
    ?>
    <script type="text/javascript">
        var members_copy = JSON.parse(JSON.stringify(members));
        var history_copy = JSON.parse(JSON.stringify(question_history));
    </script>
    <?php

    if ($mastery_type == "memorization") {
    ?>
        <script type="text/javascript">
            var wait_time = [];
        </script>
        <?php
        /* get config array from config table in array value and set to javascript value */
        $sql = "SELECT * FROM config WHERE option_name IN ('miss_wait_time','first_wait_time','second_wait_time','third_wait_time','fourth_wait_time','fifth_wait_time','sixth_wait_time')";
        $wait_times = DB::select($sql);
        if ($wait_times) :
            $wait_times = json_decode(json_encode($wait_times), true);
        endif;
        for ($i = 0; $i < count($wait_times); $i++) {
            if ($wait_times[$i]['option_name'] == 'miss_wait_time') {
        ?>
                <script type="text/javascript">
                    wait_time[0] = parseInt('<?php echo $wait_times[$i]['option_value']; ?>');
                </script>
            <?php
            } elseif ($wait_times[$i]['option_name'] == 'first_wait_time') {
            ?>
                <script type="text/javascript">
                    wait_time[1] = parseInt('<?php echo $wait_times[$i]['option_value']; ?>');
                </script>
            <?php
            } elseif ($wait_times[$i]['option_name'] == 'second_wait_time') {
            ?>
                <script type="text/javascript">
                    wait_time[2] = parseInt('<?php echo $wait_times[$i]['option_value']; ?>');
                </script>
            <?php
            } elseif ($wait_times[$i]['option_name'] == 'third_wait_time') {
            ?>
                <script type="text/javascript">
                    wait_time[3] = parseInt('<?php echo $wait_times[$i]['option_value']; ?>');
                </script>
            <?php
            } elseif ($wait_times[$i]['option_name'] == 'fourth_wait_time') {
            ?>
                <script type="text/javascript">
                    wait_time[4] = parseInt('<?php echo $wait_times[$i]['option_value']; ?>');
                </script>
            <?php
            } elseif ($wait_times[$i]['option_name'] == 'fifth_wait_time') {
            ?>
                <script type="text/javascript">
                    wait_time[5] = parseInt('<?php echo $wait_times[$i]['option_value']; ?>');
                </script>
            <?php
            } elseif ($wait_times[$i]['option_name'] == 'sixth_wait_time') {
            ?>
                <script type="text/javascript">
                    wait_time[6] = parseInt('<?php echo $wait_times[$i]['option_value']; ?>');
                </script>
        <?php
            }
        }
    } else if ($mastery_type == "problem solving") {
        ?>
        <script type="text/javascript">
            var wait_time = Math.floor(members.length / 2);
        </script>
    <?php
    }

    ?>
    <script type="text/javascript">
        var imageObjs = [];
        var imageObj = new Image();
        imageObj.src = '/assets/images/mastered-lightbulb.png';
        imageObjs.push(imageObj);
        var imageObj = new Image();
        imageObj.src = '/assets/images/gs-answer-logo.png';
        imageObjs.push(imageObj);
        var imageObj = new Image();
        imageObj.src = '/assets/images/que-finished-graphic.png';
        imageObjs.push(imageObj);
    </script>
    <?php
    /* save image path to javascript image object */
    if ($test_type == "multiple" || $test_type == "fill") {
        $dirname = "upload/img/" . $test_id;
        if (is_dir($dirname)) {
            $dir_handle = opendir($dirname);
            if ($dir_handle) {
                while ($file = readdir($dir_handle)) {
                    if ($file != "." && $file != "..") {
    ?>
                        <script type="text/javascript">
                            var imageObj = new Image();
                            imageObj.src = '/<?php echo str_replace("'", "&#39;", $dirname . "/" . $file); ?>'.replace("&#39;", "'");
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
    <script type="text/javascript">
        var compliments = [];
    </script>
    <?php
    $dirname = "upload/compliments";
    if (is_dir($dirname)) {
        $dir_handle = opendir($dirname);
    }
    /* load Compliments */
    if ($dir_handle) {
        while ($sub_dir = readdir($dir_handle)) {
            if ($sub_dir != "." && $sub_dir != ".." && is_dir($dirname . "/" . $sub_dir)) {
                $sub_dir_handle = opendir($dirname . "/" . $sub_dir);
                if ($sub_dir_handle) {
                    $tmp_image = "";
                    $tmp_audio = "";
                    while ($file = readdir($sub_dir_handle)) {
                        if (is_file($dirname . "/" . $sub_dir . "/" . $file)) {
                            $image_formats = array("png", "jpg");
                            $audio_formats = array("mp3", "MP3");
                            $name_array = explode(".", $file);
                            $ext = end($name_array);
                            if (in_array($ext, $image_formats)) {
                                $tmp_image = $dirname . "/" . $sub_dir . "/" . $file;
                            } else if (in_array($ext, $audio_formats)) {
                                $tmp_audio = $dirname . "/" . $sub_dir . "/" . $file;
                            }
                        }
                    }
                    if ($tmp_image == "" && $tmp_audio == "") {
                        deleteDirectory($dirname . "/" . $sub_dir);
                    } else {
                        if ($tmp_image == "") {
                            $tmp_image = "assets/images/correct.png";
                        } else {
    ?>
                            <script type="text/javascript">
                                var imageObj = new Image();
                                imageObj.src = '/<?php echo str_replace("'", "&#39;", $tmp_image); ?>'.replace("&#39;", "'");
                                imageObjs.push(imageObj);
                            </script>
                        <?php
                        }
                        ?>
                        <script type="text/javascript">
                            compliments.push(['<?php echo str_replace("'", "&#39;", $tmp_image); ?>'.replace("&#39;", "'"), '<?php echo str_replace("'", "&#39;", $tmp_audio); ?>'.replace("&#39;", "'")]);
                        </script>
    <?php
                    }
                    closedir($sub_dir_handle);
                }
            }
        }
        closedir($dir_handle);
    }
    ?>

    <script type="text/javascript">
        if (compliments.length == 0) {
            compliments.push(['/assets/images/correct.png', '']);
        }
    </script>

    <script type="text/javascript">
        // This code loads the Player API code asynchronously.
        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/player_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    </script>

    <script src="/assets/js/page/test-question.js"></script>
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

<div class="modal fade require-modal-wrapper" id="require-modal" tabindex="-1" aria-labelledby="torchlight-video" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-body p-2">
                <p class="mt25" style="font-size: 18px;" id="require_message">
                    Please give an answer
                </p>
                <hr style="margin: 10px 0px;">
                <div class="mt15">
                    <button type="submit" class="btn" style="background-color: #9cdc48;  color: #fff;" onclick="close_requieModal()" onmouseover="row_mouseover(this)" onmouseout="row_mouseout(this)">ok</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid" style="height: 100vh; background-color: #585858;">
    <div class="row">
        <div class="col-md-12 py-4">
            <div class="container">
                <a href="/chapter?c=<?php echo $test_id; ?>&v=<?php echo spacestoDashes(testNameAfterColon($test_name)); ?> ">
                    <div class="text-center" style="padding: 6px 4px; font-size: 1.2em; color: #fff; font-weight: bold;"><?php echo testNameAfterColon($test_name); ?></div>
                </a>
                <div class="row">
                    <div class="col-sm-6 offset-sm-3 question-form text-center">
                        <div id="left-time" style="display: none; position: absolute; left: 50px; font-size: 1.3em; "></div>
                        <div class="msg-complete"></div>

                        <?php if ($form_options != "") {
                            $form_options = unserialize($form_options);
                            $style_font = "font-size:" . $form_options['font'] . ";";
                            $style_width = "width:" . $form_options['width'] . "px;";
                        } else {
                            $style_font = "";
                            $style_width = "";
                        }  ?>

                        <script type="text/javascript">
                            var form_options = '<?php echo $style_font; ?>';
                        </script>

                        <div class="test-not-in-game-flag" id="question_form" style="margin-top: 15px;">
                            <?php //entire test question pulled from js //width pulled from DB options $style_width 
                            ?>
                        </div>

                        <div id="player">
                            <audio id="audio" preload="auto" tabindex="0" controls="" type="audio/mpeg" style="display: none;"></audio>
                            <audio id="alarm" src="/assets/alarm/alarm.mp3" preload="auto" controls="" type="audio/mpeg" style="display: none;"></audio>
                        </div>

                        <div id="Pagination" class="pagination" style="display: none;"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="display: none;">
    <div id="question-correct-container"></div>
    <div id="pre-post-container"></div>
</div>

<table id="datatable" style="display: none;">
    <thead>
        <tr>
            <th></th>
            <th>Pre-Test</th>
            <th>Post-Test</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>Pre-Test vs Post-Test</th>
            <td id="pre_test"></td>
            <td id="post_test"></td>
        </tr>
    </tbody>
</table>
@endsection
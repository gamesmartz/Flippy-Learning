@extends('layouts.app')

@section('title', 'Reports - GameSmartz')
@section('description', 'Reports, keep track of your progress - GameSmartz')

@push('head')
<script type="text/javascript">
    login_flag = true;
</script>
<?php

use Illuminate\Support\Facades\DB;

$user_id = $loggedUser->user_id;
/* get subscription value from users table by user id */
$sql = "SELECT subscription FROM users WHERE user_id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($subscription);
$stmt->fetch();
$stmt->free_result();
$stmt->close();
?>
<script type="text/javascript">
    var date_flag = false;
    var report_id = '';
    var last_stamp = 0;
</script>
<?php
/**
 * if "data" get params value exists, get several needed report date values.
 * if not submitted time from answer summary table by test id and user id, and set reports date with this value.
 * */
if (isset($date)) {
?>
    <script type="text/javascript">
        date_flag = true;
    </script>
<?php
    $report_date = $date;
    $date_array = explode("-", $report_date);
    $report_date_formated = $date_array[2] . "-" . $date_array[0] . "-" . $date_array[1];
    $report_date_formated1 = date("n-j-y", strtotime($report_date_formated));
    $report_date_before = date("Y-m-d", strtotime($report_date_formated) - 24 * 3600);
    $report_date_after = date("Y-m-d", strtotime($report_date_formated) + 24 * 3600);
} 

// else if (isset($_GET['id'])) {
?>
   {{-- <script type="text/javascript">
      report_id = '<?php //echo $_GET['id']; ?>';
     </script> --}}
<?php
//     $report_id = "report_" . $_GET['id'];
//     $sql = "SELECT submitted_time FROM answer_summary WHERE user_id = ? AND test_id = ? ORDER BY submitted_time DESC ";
//     $stmt = $db->prepare($sql);
//     $stmt->bind_param("ss", $user_id, $_GET['id']);
//     $stmt->execute();
//     $stmt->store_result();
//     $stmt->bind_result($last_submitted_time);
//     while ($stmt->fetch()) {
//         $report_date_before = date("Y-m-d", strtotime($last_submitted_time) - 24 * 3600);
//         $report_date_formated = date("Y-m-d", strtotime($last_submitted_time));
//         $report_date_formated1 = date("n-j-y", strtotime($last_submitted_time));
//         $report_date_after = date("Y-m-d", strtotime($last_submitted_time) + 24 * 3600);
//         $last_stamp = strtotime($last_submitted_time) * 1000;   // second to milisecond
?>
         {{-- <script type="text/javascript">
             last_stamp = parseInt('<?php // echo $last_stamp; ?>');
       </script> --}}
<?php
//         break;
//     }
//     $stmt->free_result();
//     $stmt->close();
// }

/* get award time from config table */
$awardTime = get_seconds(getConfigValue($db, 'time_award'));
/* get current date min, max time from answer summary */
$sql = "SELECT TIMEDIFF(MAX(submitted_time), MIN(submitted_time)) as submitted_period 
            FROM answer_summary 
            WHERE DATE(submitted_time) = '" . $report_date_formated . "'";
$report_submitted_period = DB::select($sql);
$report_submitted_period = json_decode(json_encode($report_submitted_period), true);
$report_submitted_period = get_seconds($report_submitted_period[0]['submitted_period']);
$today_desired_answered_num = floor($report_submitted_period / $awardTime);

/* get total answered numbers for report date */
$sql = "SELECT * FROM cron_email WHERE user_id = $user_id AND SUBSTRING_INDEX(SUBSTRING_INDEX(report_date, ' ', 1), ' ', -1) = '" . $report_date_formated1 . "'";
$cron_email = DB::select($sql);
$cron_email = json_decode(json_encode($cron_email), true);
if ($cron_email)
    $today_user_answered_num = $cron_email[0]['total_answer_num'];
else $today_user_answered_num = 0;

$negative_times = ($today_desired_answered_num - $today_user_answered_num) * $awardTime;
//    if ($negative_times < 0) $negative_times = 0;
$negative_times = conversionTempsEnHms($negative_times);

/* get answer summaries list from answer_summary table by user id and submitted_time range +- 1 day */
$sql = "
        SELECT id, question_id, correct, submitted_time, test_id, test_mode, new_mastered
        FROM answer_summary
        WHERE user_id = ? AND date(submitted_time) BETWEEN ? AND ?
        ORDER BY submitted_time ASC
    ";
$stmt = $db->prepare($sql);
$stmt->bind_param("sss", $user_id, $report_date_before, $report_date_after);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $question_id, $correct, $submitted_time, $test_id, $summary_test_mode, $new_mastered);

// consider difference between server and broswer time
$server_time = time() * 1000; // second to milisecond
?>
<script type="text/javascript">
    /* initialize javascript variables before assign */
    var user_id = '<?php echo $user_id; ?>';
    var server_time = parseInt('<?php echo $server_time; ?>');
    var browser_time = new Date();
    var time_difference = browser_time.getTime() - server_time;
    var report_date = '';
    if (date_flag) {
        report_date = '<?php echo $report_date; ?>';
    } else {
        if (last_stamp != 0) {
            last_stamp = last_stamp + time_difference;
            var date = new Date(last_stamp);
            report_date = (date.getMonth() + 1).toString() + "-" + date.getDate().toString() + "-" + date.getFullYear().toString().substr(2, 2);
        }
    }
    var test_result = [];
    var start_time;
    var correct_answered = 0;
    var test_names = [];
    var test_colors = [];
    var grade_subject = [];
    var test_ids = [];
    var first_indexes = [];
    var test_videos = [];
    var videos_arr = [];
    var video_insert_flag = false;
    var pre_tests = [];
    var post_tests = [];
    var question_lengths = [];
    var correct_pre_test = [];
    var pre_complete_time = [];
    var correct_post_test = [];
    var post_complete_time = [];
    var correct_answer_numbers = [];
    var temp_time = 0;
    var submitted_time = 0;
    var correct = 0;
    var date_string;
    var new_facts_mastered = 0;
    var negative_times = "<?php echo $negative_times ?>";
</script>
<?php

/**
 * get test and related with this test info like grade, subject using sub query
 * This query is similar to get test query in async-tests.php
 * this result is needed inside answer loop
 */
$sql = "
       SELECT
          test_subject.test_name, test_subject.gradeless, test_subject.subject_name, grade.grade_name
       FROM
          (    SELECT test.*, subject.subject_name, subject.grade_id
               FROM test
               JOIN subject
               ON test.subject_id = subject.subject_id
               WHERE test.test_id = ? 
          ) test_subject
       JOIN grade
       ON test_subject.grade_id = grade.grade_id
    ";
$stmt_1 = $db->prepare($sql);
$stmt_1->bind_param("s", $test_id);

/* get videos by test id this query is needed in test loop */
$sql = "SELECT video_id, video_title, video_link, length FROM video WHERE public = 1 AND test_id = ?";
$stmt_2 = $db->prepare($sql);
$stmt_2->bind_param("s", $test_id);

/* get watch time from user_video table by user id and video id. this query is needed in test loop */
$sql = "SELECT watch_time FROM user_video WHERE video_id = ? AND user_id = ?";
$stmt_3 = $db->prepare($sql);
$stmt_3->bind_param("ss", $video_id, $user_id);

/* get pre_test and post test value from user_test table by user id and test id. this query is needed in test loop */
$sql = "SELECT pre_test, post_test FROM user_test WHERE user_id = ? AND test_id = ?";
$stmt_4 = $db->prepare($sql);
$stmt_4->bind_param("ss", $user_id, $test_id);

/* get questions list value from question table by test id. this query is needed in test loop */
$sql = "SELECT * FROM question WHERE test_id = ?";
$stmt_5 = $db->prepare($sql);
$stmt_5->bind_param("s", $test_id);

/* get answer summaries list from answer summary table by user id and test id and correct is 1 and test mode this query inside in test loop */
$sql = "SELECT * FROM answer_summary WHERE user_id = ? AND test_id = ? AND correct = 1 AND test_mode = ?";
$stmt_6 = $db->prepare($sql);
$stmt_6->bind_param("sss", $user_id, $test_id, $test_mode);

/* get submitted_time value from answer summary table by user id and test id and test mode this query inside in test loop */
$sql = "SELECT submitted_time FROM answer_summary WHERE user_id = ? AND test_id = ? AND test_mode = ? ORDER BY submitted_time DESC ";
$stmt_7 = $db->prepare($sql);
$stmt_7->bind_param("sss", $user_id, $test_id, $test_mode);

/* test Loop */
while ($stmt->fetch()) {

    $stmt_1->execute();
    $stmt_1->store_result();
    $stmt_1->bind_result($test_name, $gradeless, $subject_name, $grade_name);
    $stmt_1->fetch();
    $stmt_1->free_result();
    if ($gradeless == 1) {
        $grade_subject = $subject_name;
    } else {
        $grade_subject = $grade_name . " - " . $subject_name;
    }

    $stmt_4->execute();
    $stmt_4->store_result();
    $stmt_4->bind_result($pre_test, $post_test);
    $stmt_4->fetch();
    $stmt_4->free_result();

    $stmt_5->execute();
    $stmt_5->store_result();
    $row_5 = $stmt_5->num_rows();
    $stmt_5->free_result();

    $test_mode = "pre_test";
    $stmt_6->execute();
    $stmt_6->store_result();
    $correct_pre_test = $stmt_6->num_rows();
    $stmt_6->free_result();

    $stmt_7->execute();
    $stmt_7->store_result();
    $stmt_7->bind_result($pre_complete_time);
    $stmt_7->fetch();
    $stmt_7->free_result();
    if (isset($pre_complete_time)) {
        $pre_complete_time = strtotime($pre_complete_time) * 1000;
    }

    $test_mode = "post_test";
    $stmt_6->execute();
    $stmt_6->store_result();
    $correct_post_test = $stmt_6->num_rows();
    $stmt_6->free_result();

    $stmt_7->execute();
    $stmt_7->store_result();
    $stmt_7->bind_result($post_complete_time);
    $stmt_7->fetch();
    $stmt_7->free_result();
    if (isset($post_complete_time)) {
        $post_complete_time = strtotime($post_complete_time) * 1000;
    }

    /* set test color */
    if (isset($_GET['date'])) {
        if (isset($_SESSION[$report_date][$test_id])) {
            $color_string = $_SESSION[$report_date][$test_id];
        } else {
            $rgbColor = array();
            foreach (array('r', 'g', 'b') as $color) {
                $rgbColor[$color] = mt_rand(0, 255);
            }
            $color_string = 'rgb(' . implode(",", $rgbColor) . ')';
            $_SESSION[$report_date][$test_id] = $color_string;
        }
    } else {
        // if (isset($_SESSION[$report_id][$test_id])) {
        //     $color_string = $_SESSION[$report_id][$test_id];
        // } else {
            $rgbColor = array();
            foreach (array('r', 'g', 'b') as $color) {
                $rgbColor[$color] = mt_rand(0, 255);
            }
            $color_string = 'rgb(' . implode(",", $rgbColor) . ')';
            //$_SESSION[$report_id][$test_id] = $color_string;
        //}
    }

    $submitted_time = strtotime($submitted_time) * 1000; // second to milisecond
?>
    <script type="text/javascript">
        /* assign values to javascript arrays */
        correct = parseInt('<?php echo $correct; ?>');
        submitted_time = parseInt('<?php echo $submitted_time; ?>') + time_difference;
        start_time = new Date(submitted_time);
        date_string = (start_time.getMonth() + 1).toString() + "-" + start_time.getDate().toString() + "-" + start_time.getFullYear().toString().substr(2, 2);
        if (date_string == report_date) {

            if (test_ids.indexOf('<?php echo $test_id; ?>') == -1) {
                video_insert_flag = true;
                test_ids.push('<?php echo $test_id; ?>');
                first_indexes.push('<?php echo $id; ?>');
                test_names.push('<?php echo str_replace("'", "&#39;", $test_name); ?>');
                test_colors.push('<?php echo $color_string; ?>');
                grade_subject.push('<?php echo str_replace("'", "&#39;", $grade_subject); ?>');
                pre_tests.push('<?php echo $pre_test; ?>');
                post_tests.push('<?php echo $post_test; ?>');
                question_lengths.push('<?php echo $row_5; ?>');
                correct_pre_test.push('<?php echo $correct_pre_test; ?>');
                correct_post_test.push('<?php echo $correct_post_test; ?>');
                correct_answer_numbers['<?php echo $test_id; ?>'] = 0;
                temp_time = parseInt('<?php echo $pre_complete_time; ?>') + time_difference;
                temp_time = new Date(temp_time);
                temp_time = (temp_time.getMonth() + 1).toString() + "-" + temp_time.getDate().toString() + "-" + temp_time.getFullYear().toString().substr(2, 2);
                pre_complete_time.push(temp_time);
                temp_time = parseInt('<?php echo $post_complete_time; ?>') + time_difference;
                temp_time = new Date(temp_time);
                temp_time = (temp_time.getMonth() + 1).toString() + "-" + temp_time.getDate().toString() + "-" + temp_time.getFullYear().toString().substr(2, 2);
                post_complete_time.push(temp_time);
            }

            /* set marker object */
            if (correct == 1) {
                if ('<?php echo $summary_test_mode; ?>' == 'test_0' && parseInt('<?php echo $new_mastered; ?>') == 1)
                    new_facts_mastered++;
                correct_answer_numbers['<?php echo $test_id; ?>']++;
                correct_answered++;
                test_result.push({
                    x: Date.UTC(start_time.getFullYear(), start_time.getMonth(), start_time.getDate(), start_time.getHours(), start_time.getMinutes(), start_time.getSeconds()),
                    y: correct_answered,
                    marker: {
                        fillColor: '<?php echo $color_string; ?>'
                    },
                    test_id: '<?php echo $test_id; ?>',
                    summary_index: '<?php echo $id; ?>',
                    test_name: '<?php echo str_replace("'", "&#39;", $test_name); ?>'
                });
            } else if (correct == 0) {
                test_result.push({
                    x: Date.UTC(start_time.getFullYear(), start_time.getMonth(), start_time.getDate(), start_time.getHours(), start_time.getMinutes(), start_time.getSeconds()),
                    y: correct_answered,
                    marker: {
                        fillColor: 'rgb(245, 64, 50)'
                    },
                    test_id: '<?php echo $test_id; ?>',
                    summary_index: '<?php echo $id; ?>',
                    test_name: '<?php echo str_replace("'", "&#39;", $test_name); ?>'
                });
            }

        }
    </script>
    <?php

    $stmt_2->execute();
    $stmt_2->store_result();
    $row_2 = $stmt_2->num_rows();
    $stmt_2->bind_result($video_id, $video_title, $video_link, $length);
    ?>
    <script type="text/javascript">
        videos_arr = [];
    </script>
    <?php
    /* if videos array are not empty videos loop */
    if ($row_2 != 0) {

        while ($stmt_2->fetch()) {

            $stmt_3->execute();
            $stmt_3->store_result();
            $row_3 = $stmt_3->num_rows();
            $stmt_3->bind_result($watch_time);
            $stmt_3->fetch();
            $stmt_3->free_result();

            if ($row_3 == 0) {
                $watch_time = "0:00";
            } else {
                $watch_time = (substr($watch_time, 3, 1) == "0" ? substr($watch_time, 4) : substr($watch_time, 3));
            }

            if ($video_link != "") {
                $pattern = '%(?:youtube\.com/(?:user/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';
                preg_match($pattern, $video_link, $matches);
                $video_link = isset($matches[1]) ? $matches[1] : "";
            }
            $length = (substr($length, 3, 1) == "0" ? substr($length, 4) : substr($length, 3));
            /* assign videos arrasy to javascript video_arr */
    ?>
            <script type="text/javascript">
                videos_arr.push([
                    '<?php echo $video_id; ?>',
                    '<?php echo $video_link; ?>',
                    '<?php echo $video_title; ?>',
                    '<?php echo $length; ?>',
                    '<?php echo $watch_time; ?>'
                ]);
            </script>
    <?php
        }
    }
    $stmt_2->free_result();
    ?>
    <script type="text/javascript">
        if (video_insert_flag) {
            video_insert_flag = false;
            test_videos.push(videos_arr);
        }
    </script>
<?php
}

$stmt->free_result();
$stmt->close();
$stmt_1->close();
$stmt_2->close();
$stmt_3->close();
$stmt_4->close();
$stmt_5->close();
$stmt_6->close();
$stmt_7->close();
?>

<script type="text/javascript">
    // This code loads the Player API code asynchronously.
    var tag = document.createElement('script');
    tag.src = "https://www.youtube.com/player_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
</script>
@endpush

@push('foot')
<script src="/assets/js/page/reports.js"></script>
<script src="/assets/plugin/chart/js/highcharts.js"></script>
<script src="/assets/plugin/chart/js/modules/data.js"></script>
<script src="/assets/plugin/chart/js/modules/exporting.js"></script>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 py-4">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 text-center" id="report-date"></div>
                    <div class="col-md-12 text-center" id="report-tests"></div>
                </div>
                <div class="row">
                    <div class="col-md-12 pt-4" id="question-correct-container"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
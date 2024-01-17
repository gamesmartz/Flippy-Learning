@extends('layouts.app')

@php
// HISTORY PAGE
@endphp

@section('title', 'History - GameSmartz')
@section('description', 'Review Your History')

@push('head')
<?php 
$sql = "SELECT track_history, subscription, user_role, watch_history FROM users WHERE id = ?";
$stmt = $db->prepare($sql);
$id = $loggedUser->id;
$stmt->bind_param("s", $id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($track_history, $subscription, $user_role, $watch_history);
$stmt->fetch();
$stmt->free_result();
$stmt->close();

// default history render, actually should not be used, because default should come from ajax call

$sql = "
SELECT t.test_id, CONVERT( a.`submitted_time`, DATE) AS submit_time, t.`test_name`, t.`attachment`, s.`subject_id`, t.`gradeless`, s.`subject_name`, g.`grade_id`, g.grade_name
FROM answer_summary a
    JOIN test t ON a.test_id=t.test_id
    JOIN subject s ON t.subject_id=s.subject_id
    JOIN grade g ON s.grade_id=g.grade_id
WHERE a.id = ? AND a.`submitted_time` > DATE_SUB(NOW(), INTERVAL ? DAY)
GROUP BY submit_time
";
$stmt = $db->prepare($sql);
$stmt->bind_param("ss", $id, $track_history);

$stmt->execute();
$stmt->store_result();
$row = $stmt->num_rows();
$stmt->bind_result($test_id, $submitted_time, $test_name, $attachment, $subject_id, $gradeless, $subject_name, $grade_id, $grade_name);

/* get questions by test id  */
$sql = "SELECT * FROM question WHERE test_id = ?";
$stmt_1 = $db->prepare($sql);
$stmt_1->bind_param("s", $test_id);

/* get pre post and post test value by test id and user id cause this row is given by these 2 values */
$sql = "SELECT pre_test, post_test FROM user_test WHERE id = ? AND test_id = ?";
$stmt_2 = $db->prepare($sql);
$stmt_2->bind_param("ss", $id, $test_id);

// answer_summaries



//    $sql = "SELECT * FROM answer_summary WHERE id = ? AND test_id = ? AND correct = 1 AND test_mode = ?";
//    $stmt_3 = $db->prepare($sql);
//    $stmt_3->bind_param("sss", $id, $test_id, $test_mode);

//    $sql = "SELECT submitted_time FROM answer_summary WHERE id = ? AND test_id = ? AND test_mode = ? ORDER BY submitted_time DESC ";
//    $stmt_4 = $db->prepare($sql);
//    $stmt_4->bind_param("sss", $id, $test_id, $test_mode);

$server_time = time() * 1000; // second to milisecond

?>
<script type="text/javascript">
        var server_time = parseInt('<?php echo $server_time; ?>');
        var browser_time = new Date();
        var time_difference = browser_time.getTime() - server_time;
        var report_date = "";
        var history_date = "";
    </script>

    <?php if ($row != 0) { ?>
        <script type="text/javascript">
            var members = [];
        </script>
        <?php
        // Questions loop

        while ($stmt->fetch()) {
            $submitted_time_stamp = strtotime($submitted_time) * 1000;   // second to milisecond
        ?>
            <script type="text/javascript">
                var time_stamp = parseInt('<?php echo $submitted_time_stamp; ?>') + time_difference;
                var date = new Date(time_stamp);
                report_date = (date.getMonth() + 1).toString() + "-" + date.getDate().toString() + "-" + date.getFullYear().toString().substr(2, 2);

                history_date = ('<?php echo $submitted_time; ?>').slice(5, 7) + "-" + ('<?php echo $submitted_time; ?>').slice(8, 10) + "-" + ('<?php echo $submitted_time; ?>').slice(2, 4);
                // history date default format 01-02-2020
                // this checks the 4th spot for a 0.. if so it takes the first 2 chars, then all the chars after the 0...
                // if (history_date.charAt(3) == '0') {
                //     history_date = ((history_date.slice(1, 2)) + "-" + (history_date.slice(4, 10)));
                // }
                // this checks the array at 0 for a 0.. if found it slices it off and includes everything from 1-10
                // if (history_date.charAt(0) == '0') {
                //     history_date = history_date.slice(1, 10);
                // }
            </script>

            <?php
            $stmt_1->execute();
            $stmt_1->store_result();
            $question_length = $stmt_1->num_rows();
            $stmt_1->free_result();

            $stmt_2->execute();
            $stmt_2->store_result();
            $row_2 = $stmt_2->num_rows();
            $stmt_2->bind_result($pre_test, $post_test);
            $stmt_2->fetch();
            $stmt_2->free_result();

            $test_mode = "pre_test";
            //            $stmt_3->execute();
            //            $stmt_3->store_result();
            //            $correct_pre_test = $stmt_3->num_rows();
            //            $stmt_3->free_result();

            //            $stmt_4->execute();
            //            $stmt_4->store_result();
            //            $stmt_4->bind_result($pre_complete_time);
            //            $stmt_4->fetch();
            //            $stmt_4->free_result();
            //            if(isset($pre_complete_time)){
            //                $pre_complete_time = strtotime($pre_complete_time) * 1000;
            //            }

            $test_mode = "post_test";
            //            $stmt_3->execute();
            //            $stmt_3->store_result();
            //            $correct_post_test = $stmt_3->num_rows();
            //            $stmt_3->free_result();

            //            $stmt_4->execute();
            //            $stmt_4->store_result();
            //            $stmt_4->bind_result($post_complete_time);
            //            $stmt_4->fetch();
            //            $stmt_4->free_result();

            if (isset($post_complete_time)) {
                $post_complete_time = strtotime($post_complete_time) * 1000;
            }

            // assign above values to javascript variable
            ?>
            <script type="text/javascript">
                var info = '';
            </script>

            <?php
            if ($post_test >= $question_length) {
            ?>
                <script type="text/javascript">
                    var temp_time = parseInt('<?php echo $post_complete_time; ?>') + time_difference;
                    temp_time = new Date(temp_time);
                    temp_time = (temp_time.getMonth() + 1).toString() + "-" + temp_time.getDate().toString() + "-" + temp_time.getFullYear().toString().substr(2, 2);
                    info = 'Post-Test (' + temp_time + ') ' + '<?php echo $correct_post_test; ?>' + '/' + '<?php echo $question_length; ?>' + ' ' + Math.round(100 * parseInt('<?php echo $correct_post_test; ?>') / parseInt('<?php echo $question_length; ?>')) + '%';
                </script>
            <?php
            } else if ($pre_test >= $question_length) {
            ?>
                <script type="text/javascript">
                    var temp_time = parseInt('<?php echo $pre_complete_time; ?>') + time_difference;
                    temp_time = new Date(temp_time);
                    temp_time = (temp_time.getMonth() + 1).toString() + "-" + temp_time.getDate().toString() + "-" + temp_time.getFullYear().toString().substr(2, 2);
                    info = 'Pre-Test (' + temp_time + ') ' + '<?php echo $correct_pre_test; ?>' + '/' + '<?php echo $question_length; ?>' + ' ' + Math.round(100 * parseInt('<?php echo $correct_pre_test; ?>') / parseInt('<?php echo $question_length; ?>')) + '%';
                </script>
            <?php
            }

            if ($gradeless == 1) {
                $grade_subject = $subject_name;
            } else {
                $grade_subject = $grade_name . " - " . $subject_name;
            }
            ?>
            <script type="text/javascript">
                members.unshift(['<?php echo $test_id; ?>', history_date, '<?php echo str_replace("'", "&#39;", $test_name); ?>', '<?php echo $attachment; ?>', info, '<?php echo $grade_subject; ?>']);
            </script>

        <?php
        }
    } else { ?>
        <script type="text/javascript">
            var members = [
                ['none', 'No history in this duration', '', '', '', '']
            ];
        </script>
    <?php
    }

    $stmt->free_result();
    $stmt->close();

    $stmt_1->close();
    $stmt_2->close();
    //    $stmt_3->close();
    //    $stmt_4->close();

    ?>
    <style>
         @media screen and (max-width: 575px) {
            .remove-border-sm {
                border: none !important;
            }
        }
    </style>
    
@endpush

@push('foot')
<script src="/assets/js/page/history.js?v=<?php echo time(); ?>"></script>
        <script type="text/javascript">
            function changeDate(d) {
                if (d == 365) {
                    $("#history-date .dd-selected").html('1 Year');
                } else if (d == 720) {
                    $("#history-date .dd-selected").html('All Time');
                } else {
                    $("#history-date .dd-selected").html(d + ' Days');
                }
                $("#history-date .dd-selected-value").val(d);
                getOptionsFromForm();
            }
            $(document).ready(function() {
                <?php
                if ($track_history == "365") {
                ?>
                    $("#history-date .dd-selected").html('1 Year');
                <?php
                } elseif ($track_history == "720") {
                ?>
                    $("#history-date .dd-selected").html('All Time');
                <?php
                } else {
                ?>
                    $("#history-date .dd-selected-value").val('<?php echo $track_history; ?>');
                    $("#history-date .dd-selected").html('<?php echo $track_history . " Days"; ?>');
                <?php
                }
                ?>
            });
        </script>
@endpush

@section('content')
<div class="container" style="font-family: 'Raleway', sans-serif;"> 
                
            <div style="font-size: 14px;" class="row justify-content-md-center">    
                <div class="col-10">
                    <div class="row pt-2">
                        <div class="col-3 text-start  border-bottom border-2">Date</div>
                        <div class="col-9 text-start  border-bottom border-2">Test</div>
                    </div>    
                    
                    <div class="col-md-auto pt-3 pt-sm-0" id="choose-tests-table"></div>
                    <div class="bg-secondary mt-4 pt-3 pb-2 ps-2 pe-2">
                        <div id="Pagination" class="pagination" onclick="start_drag()"></div>
                        <div class="view-per-page pull-left d-none">
                            <span class="pull-left mr5 normal">View</span>
                            <select name="view-test" class="select select-whitetxt" id="view-test" data-desc="20">
                                <option>10</option>
                                <option>20</option>
                                <option>50</option>
                                <option>100</option>
                            </select>
                        </div>
                     
                    </div>
                </div>
            </div>
        </div>       
@endsection
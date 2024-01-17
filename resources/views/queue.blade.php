@extends('layouts.app')

<?php //QUEUE PAGE ?>

<?php
// Page specific DB vars - easier to access in the loops below
$sql = "SELECT que, subscription, user_role, watch_history FROM users WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $id);

$id = $loggedUser->id;
$stmt->execute();
$stmt->store_result();
$row = $stmt->num_rows();
$stmt->bind_result($que, $subscription, $user_role, $watch_history);
$stmt->fetch();
$stmt->free_result();
$stmt->close();
?>

@section('title', 'Queue - GameSmartz')
@section('description', 'Organize your tests in the order you want to take them')

@push('head')
<link rel="stylesheet" href="/assets/css/jquery-ui.css">
@endpush

@push('foot')
<script src="/assets/js/page/queue.js"></script>
<script src="/assets/js/libs/lazysizes.min.js"></script>
@endpush

@section('content')
<!-- Modal -->
<div class="modal fade" id="reset-progress-modal" tabindex="-1" aria-labelledby="reset-progress-modal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body">
        <div class="container">
          <div class="row">
            <div class="col-md-12 text-center py-3">
              <h5>This will reset the test progress</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary btn-app-primary" onclick="close_resetModal()">OK</button>
      </div>
    </div>
  </div>
</div>
  <div class="container-fluid" style="margin-top: 10px; min-height: 1000px;">
    <div class="row">
     
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="pb-3 f-size-14">The game queue. These are the tests that you will take while playing games, from the top down.</div>
                </div>
            </div>
          <div style="font-family: 'Raleway', sans-serif;" class="row justify-content-md-center">         
            <div class="col-md-auto pb-3">
              <input type="hidden" id="test_id" value="">
              <div class="container-fluid" id="queue-tests-table">
                <?php
                // start 1st loop
                if ($row != 0 && $que != "") {
                  /* get memorization and problem mastery value from config table */
                  $sql = "SELECT option_value FROM config WHERE option_name = ?";
                  $stmt = $db->prepare($sql);
                  $stmt->bind_param("s", $option_name);

                  $option_name = "memorization_mastery";
                  $stmt->execute();
                  $stmt->store_result();
                  $stmt->bind_result($memorization_mastery);
                  $stmt->fetch();
                  $stmt->free_result();

                  $option_name = "problem_mastery";
                  $stmt->execute();
                  $stmt->store_result();
                  $stmt->bind_result($problem_mastery);
                  $stmt->fetch();
                  $stmt->free_result();
                  $stmt->close();

                  /* unserialize que to get test id arrays  */
                  $que_array = unserialize($que);
                  $que_array_copy = $que_array;

                  /* get test data and related to subject, grade through join and sub query with test id in test array loop  */
                  $sql = "
                  SELECT
                      test_subject.test_name, test_subject.attachment, test_subject.mastery_type, test_subject.mastery_number, test_subject.gradeless, test_subject.subject_name, grade.grade_name
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
                  $stmt = $db->prepare($sql);
                  $stmt->bind_param("s", $test_id);

                  // get info from test sbuject with test id
                  $sql = "SELECT s.grade_id FROM test t JOIN subject s ON t.subject_id = s.subject_id WHERE t.test_id = ?";
                  // stmt Start
                  $stmt_0 = $db->prepare($sql);
                  $stmt_0->bind_param("s", $test_id);
                  $stmt_0->execute();
                  $stmt_0->store_result();
                  $stmt_0->bind_result($grade_id);
                  $stmt_0->fetch();
                  $stmt_0->free_result();
                  $stmt_0->close();                 

                  /* get one row - pre test and post test from user test relation table with user id and test id */
                  $sql = "SELECT test_info, pre_test, post_test FROM user_test WHERE id = ? AND test_id = ?";
                  $stmt_1 = $db->prepare($sql);
                  $stmt_1->bind_param("ss", $id, $test_id);

                  /* get one test's all questions with test id from question table not ready for execute inside que array */
                  $sql = "SELECT * FROM question WHERE test_id = ?";
                  $stmt_2 = $db->prepare($sql);
                  $stmt_2->bind_param("s", $test_id);

                  /* get answers from answer summary table by test id and correct is 1 and test mode not ready for execute inside que array */
                  $sql = "SELECT * FROM answer_summary WHERE id = ? AND test_id = ? AND correct = 1 AND test_mode = ?";
                  $stmt_3 = $db->prepare($sql);
                  $stmt_3->bind_param("sss", $id, $test_id, $test_mode);

                  /* get submit time from answer summary table by test id and test mode and user id not ready for execute inside que array */
                  $sql = "SELECT submitted_time FROM answer_summary WHERE id = ? AND test_id = ? AND test_mode = ? ORDER BY submitted_time DESC ";
                  $stmt_4 = $db->prepare($sql);
                  $stmt_4->bind_param("sss", $id, $test_id, $test_mode);

                  /* get videos by test id and public is 1 not ready for execute inside que array */
                  $sql = "SELECT * FROM video WHERE test_id = ? AND public = 1";
                  $stmt_5 = $db->prepare($sql);
                  $stmt_5->bind_param("s", $test_id);

                  /* Que array loop */
                  for (
                    $i = 0;
                    $i < count($que_array);
                    $i++
                  ) {
                    $test_id = $que_array[$i];
                    $stmt->execute();
                    $stmt->store_result();
                    $row = $stmt->num_rows();
                    $stmt->bind_result($test_name, $attachment, $mastery_type, $mastery_number, $gradeless, $subject_name, $grade_name);
                    $stmt->fetch();
                    $stmt->free_result();

                    // start 2nd loop
                    if ($row != 0) {

                      if ($mastery_number == "") {
                        if ($mastery_type == "memorization") {
                          $mastery_number = $memorization_mastery;
                        } else {
                          $mastery_number = $problem_mastery;
                        }
                      }

                      /* execute stmt_1 */
                      $stmt_1->execute();
                      $stmt_1->store_result();
                      $row_1 = $stmt_1->num_rows();
                      $stmt_1->bind_result($test_info, $pre_test, $post_test);
                      $stmt_1->fetch();
                      $stmt_1->free_result();

                      /* execute stmt_2 */
                      $stmt_2->execute();
                      $stmt_2->store_result();
                      $question_length = $stmt_2->num_rows();
                      $stmt_2->free_result();

                      /* execute stmt_3 */
                      $test_mode = "pre_test";
                      $stmt_3->execute();
                      $stmt_3->store_result();
                      $correct_pre_test = $stmt_3->num_rows();
                      $stmt_3->free_result();

                      /* execute stmt_4 */
                      $stmt_4->execute();
                      $stmt_4->store_result();
                      $stmt_4->bind_result($pre_complete_time);
                      $stmt_4->fetch();
                      $stmt_4->free_result();

                      if (isset($pre_complete_time)) {
                        $pre_complete_time = strtotime($pre_complete_time) * 1000;
                      }

                      /* execute stmt_3 by another condition */
                      $test_mode = "post_test";
                      $stmt_3->execute();
                      $stmt_3->store_result();
                      $correct_post_test = $stmt_3->num_rows();
                      $stmt_3->free_result();

                      /* execute stmt_4 by another condition */
                      $stmt_4->execute();
                      $stmt_4->store_result();
                      $stmt_4->bind_result($post_complete_time);
                      $stmt_4->fetch();
                      $stmt_4->free_result();
                      if (isset($post_complete_time)) {
                        $post_complete_time = strtotime($post_complete_time) * 1000;
                      }

                      /* execute stmt_5 by another condition */
                      $stmt_5->execute();
                      $stmt_5->store_result();
                      $videos_number = $stmt_5->num_rows();
                      $stmt_5->free_result();

                      if ($test_info == "Mastered") {
                        $key = array_search($test_id, $que_array_copy);
                        array_splice($que_array_copy, $key, 1);
                        if (count($que_array_copy) == 0) {
                          $serial_que = "";
                        } else {
                          $serial_que = serialize($que_array_copy);
                        }
                        $sql = "UPDATE users SET que = ? WHERE id = ?";
                        $stmt_que = $db->prepare($sql);
                        $stmt_que->bind_param("ss", $serial_que, $id);
                        $stmt_que->execute();
                        $stmt_que->close();

                        continue;
                      }

                      if ($gradeless == 1) {
                        $grade_subject = $subject_name;
                      } else {
                        $grade_subject = $grade_name . " - " . $subject_name;
                      }

                  // get info from test subject with test id
                  $sql = "SELECT s.grade_id FROM test t JOIN subject s ON t.subject_id = s.subject_id WHERE t.test_id = ?";
                  // stmt Start
                  $stmt_0 = $db->prepare($sql);
                  $stmt_0->bind_param("s", $test_id);
                  $stmt_0->execute();
                  $stmt_0->store_result();
                  $stmt_0->bind_result($grade_id);
                  $stmt_0->fetch();
                  $stmt_0->free_result();
                  $stmt_0->close(); 

                ?>

                      <div class="row queue-rows py-1 border-bottom" id="<?php echo $test_id; ?>">
                        <div class="col-2 text-start">
                          <a href="/chapter/<?php echo $test_id; ?>">

                            <?php  $chapter_image = 'upload/subjects/' . strtolower($subject_name)  . '/chapter-names/' . $grade_id . '/' . removeColonSpacesToDashes($test_name) . '/' . removeColonSpacesToDashes($test_name) . '.png'; ?>
                            <img style="max-height: 50px;" src="{{ $chapter_image }}">

                          </a>
                        </div>
                        <div style="font-size: 15px;" class="col-7 d-flex justify-content-center text-center">
                          <a role="button" class="nav-link text-dark" href="/chapter/<?php echo $test_id; ?>">
                            Chapter - <?php echo testNameAfterColon($test_name)?> - <?php echo $grade_subject; ?> 
                          </a>
                        </div>                      
                        <div style="font-size: 15px;" class="col-3 dropdown text-center cancel-drag-drop">
                          <a style="font-family: 'Raleway', sans-serif;" class="nav-link text-muted dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false">Actions</a>
                          <ul class="dropdown-menu">
                            <li><a class="dropdown-item" role="button" onclick="remove_que(this,'<?php echo $test_id; ?>')">remove</a></li>
                            <li><a class="dropdown-item" role="button" onclick="move_to_top(<?php echo $test_id; ?>)">move to top</a></li>
                            <li><a class="dropdown-item" role="button" onclick="reset_progress(this,<?php echo $test_id; ?>)">reset test progress</a></li>
                          </ul>
                        </div>
                      </div>
                  <?php
                    }
                  }
                  $stmt->close();
                  $stmt_1->close();
                  $stmt_2->close();
                  $stmt_3->close();
                  $stmt_4->close();
                  $stmt_5->close();
                } else { ?>
                  <div class="row">
                    <div class="col-md-12 text-center border-0 py-4">
                      <a href="/progress" class="nav-link text-dark">
                          Your queue is empty. Add a chapter before using the overlay software.
                      </a>
                    </div>
                  </div>
                <?php } ?>                
              </div>
            </div>
          </div>
        </div>
     
    </div>
  </div>
@endsection
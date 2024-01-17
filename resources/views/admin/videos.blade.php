@extends('layouts.admin')

@section('title', "Manage Videos - GameSmartz")
@section('description', "Manage Videos")

@push('head')
<script src="{{ asset('assets/js/page/admin-videos.js') }}"></script>
<script type="text/javascript">
    // This code loads the Player API code asynchronously.
    var tag = document.createElement('script');
    tag.src = "https://www.youtube.com/player_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
</script>

<script type="text/javascript">
    var user_role = parseInt(<?php echo $loggedUser->user_role; ?>);
</script>
<?php
use Illuminate\Support\Facades\DB;
$user_id = $loggedUser->user_id;
/* get video search history from users table by user id (primary id) */
$sql = "SELECT video_history FROM users WHERE user_id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($video_history);
$stmt->fetch();
$stmt->free_result();
$stmt->close();

if ($video_history != "") {
    /**
     * if video history is not empty, adds filter type condition to sql query
     * if empty, get all videos and its related information using sub query
     */
    $history_array = unserialize($video_history);
    $filter_type = $history_array['filter'];
    $subject_id = $history_array['subject_id'];

    if ($subject_id == "all") {
        /**
         * if subject id is not specified(all), get from video with this linked test info first: this is sub query(can think as abstract table)
         * and this sub query join with subject table by subject id on each table
         * and adds condition grade = filter type
         * and group by video id to remove duplicates and order by video test created DESC
         */
        $sql = "
               SELECT
                  video_subject.*, grade.grade_name
               FROM
                  (
                      SELECT
                      video_test.*, subject.subject_name, subject.grade_id
                      FROM
                          (    SELECT video.*, test.subject_id, test.more_subject_id
                               FROM video
                               JOIN test
                               ON video.test_id = test.test_id
                          ) video_test
                      JOIN subject
                      ON video_test.subject_id = subject.subject_id OR CONCAT(',', video_test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                      WHERE subject.grade_id = " . $filter_type . "
                      GROUP BY video_test.video_id
                      ORDER BY video_test.submitted_time DESC
                  ) video_subject
               JOIN grade
               ON video_subject.grade_id = grade.grade_id
            ";
    } else {
        /**
         * if subject id is specified, adds one more condition subject id = $subject id, but since subject id is given
         * by grade, remove filter_type condition and others are same as above query
         */
        $sql = "
               SELECT
                  video_subject.*, grade.grade_name
               FROM
                  (
                      SELECT
                      video_test.*, subject.subject_name, subject.grade_id
                      FROM
                          (    SELECT video.*, test.subject_id, test.more_subject_id
                               FROM video
                               JOIN test
                               ON video.test_id = test.test_id
                          ) video_test
                      JOIN subject
                      ON video_test.subject_id = subject.subject_id OR CONCAT(',', video_test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                      WHERE subject.subject_id = $subject_id
					  GROUP BY video_test.video_id
					  ORDER BY video_test.submitted_time DESC
                  ) video_subject
               JOIN grade
               ON video_subject.grade_id = grade.grade_id
            ";
    }
} else {
    /**
     * if admin history is empty, get all videos so remove all where conditions from above query.
     * and others are same as above query
     */
    $sql = "
            SELECT
                video_subject.*, grade.grade_name
            FROM
                (
                   SELECT
                      video_test.*, subject.subject_name, subject.grade_id
                   FROM
                      (    SELECT video.*, test.subject_id
                           FROM video
                           JOIN test
                           ON video.test_id = test.test_id
                           ORDER BY video.submitted_time DESC
                      ) video_test
                   JOIN subject
                   ON video_test.subject_id = subject.subject_id
                ) video_subject
            JOIN grade
            ON video_subject.grade_id = grade.grade_id
        ";
}

$video = DB::select($sql);
if ($video) :
    $video = json_decode(json_encode($video), true);
endif;
if ($video) { ?>
    <script type="text/javascript">
        var members = [];
    </script>
    <?php

    /* videos list loop */
    for ($i = 0; $i < count($video); $i++) {

        $video[$i]['submitted_time'] = date("m-d-y", strtotime($video[$i]['submitted_time']));
        if ($video[$i]['video_link'] != "") {
            $pattern = '%(?:youtube\.com/(?:user/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';
            preg_match($pattern, $video[$i]['video_link'], $matches);
            $video[$i]['video_link'] = isset($matches[1]) ? $matches[1] : "";
        }

    ?>
        <script type="text/javascript">
            /* push video arrays to members javascript array to show on table using jquery */
            members.push(['<?php echo $video[$i]['video_id']; ?>', '<?php echo str_replace("'", "&#39;", $video[$i]['video_title']); ?>', '<?php echo $video[$i]['video_link']; ?>', '<?php echo $video[$i]['public']; ?>', '<?php echo $video[$i]['submitted_time']; ?>', '<?php echo $video[$i]['grade_name'] . " - " . $video[$i]['subject_name']; ?>']);
        </script>
    <?php
    }
} else { ?>
    <script type="text/javascript">
        var members = [
            ['none', 'No videos in this category.', '', '', '', '']
        ];
    </script>
<?php
} ?>
@endpush

@section('content')
<input type="hidden" id="video_id" value="">

<!-- YouTube Modal - Start -->
    <div class="modal-wrapper modal-youtube-video test-video-modal">
      <div class="b-close"></div>
      <div id="video_player"></div>
    </div>
<!-- YouTube Modal - End -->

<!-- Modal Add Video - Start -->
    <div class="modal-wrapper edit-modal">
        <div class="b-close"></div>
        <h4>Best Video</h4>
        <hr>
        <div class="title-error">Please enter a title</div>
        <div class="ml15 mb15 mr15 pull-left">
            <label for="video_title" class="video-lead mr15">Title: </label>
            <input type="text" class="video-field textbox" id="video_title" placeholder="Video Title">
        </div>
        <div class="clearfix"></div>
        <div class="link-error">Please enter a link</div>
        <div class="ml15 mb15 mr15 pull-left">
            <label for="video_link" class="video-lead mr15">Link: </label>
            <input type="text" class="video-field textbox" id="video_link"
                   placeholder="https://www.youtube.com/watch?v=9c9LQWTwPoo">
        </div>
        <div class="clearfix"></div>
        <div class="mt-3">
            <button type="submit" class="btn btn-success" onclick="close_editModal()">ok</button>
        </div>
    </div>
<!-- Modal Add Video - End -->

<!-- Modal Make Public - Start -->
    <div class="modal-wrapper public-modal">
        <div class="b-close"></div>
        <h4>Video Public</h4>
        <hr>
        <div class="public-modal-wrapper">
            <select name="public_value" id="public_value" data-desc="no" class="public_value">
                <option value="yes">yes</option>
                <option value="no">no</option>
            </select>
        </div>
        <div class="mt25">
            <button type="submit" class="btn btn-success" onclick="save_public()">ok</button>
        </div>
    </div>
<!-- Modal Make Public - End -->

<!-- Modal Video Length - Start -->
    <div class="modal-wrapper length-modal">
        <div class="b-close"></div>
        <h4>Video Length</h4>
        <hr>
        <div class="text-center">
          <div>
            <label for="video_title" class="video-lead">Time: </label>
          </div>
          <input type="text" style="width: 130px; display: inline-block;" class="video-field textbox form-control" id="length_minutes" placeholder="Minutes">
        </div>
        <div>
          <div>
            <label for="video_link" class="video-lead mr10">Seconds: </label>
          </div>
          <input type="text" style="width: 130px; display: inline-block;" class="video-field textbox form-control" id="length_seconds" placeholder="Seconds">
        </div>
        <div class="mt-3">
          <button type="submit" class="btn btn-success" onclick="save_length()">ok</button>
        </div>
      </div>
    </div>
<!-- Modal Video Length - End -->

<!-- Modal Video Delete - Start -->
    <div class="modal-wrapper delete-modal-wrapper">
        <div class="b-close"></div>
        <h4>Are you sure you want to delete the video?</h4>
        <hr>
        <p class="mt25" style="font-size: 17px;">
            This is not undoable!
        </p>
        <div class="mt-3">
            <br>
            <button type="submit" class="btn btn-success" onclick="delete_confirm()">Yes, Delete the Video, I Will Never
                Need it Again
            </button>
        </div>
    </div>
<!-- Modal Video Delete - End -->

<!-- Modal Lock Account - Start -->
    <div class="modal-wrapper user-lock-modal">
        <div class="b-close"></div>
        <h4>Lock User's Account?</h4>
        <hr>
        <div class="public-modal-wrapper">
            <select name="lock_value" id="lock_value" data-desc="no" class="lock_value">
                <option value="0">yes</option>
                <option value="1">no</option>
            </select>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-success" onclick="save_lock()">ok</button>
        </div>
    </div>
<!-- Modal Lock Account - End -->


<!-- Container 1 - Start -->
<div class="container-fluid">
  <div class="row justify-content-center" style="background-color: #ebe7e5;"><!-- Row 1 - Start -->
    <div class="col-12"><!-- Col 1 - Start -->
      <div class="lead page-name">Admin Videos</div>

                <?php
                if ($video_history != "") {
                    /**
                     * get subject and grade from subjects and grades table by subject id and grade id
                     * since video history value is not empty, load selected value to drop down
                     * */
                    if ($subject_id == "all") {
                        $subject_name = "All";
                    } else {
                        $sql = "SELECT * FROM subject WHERE subject_id = '$subject_id'";
                        $choose_subject = DB::select($sql);
                        $choose_subject = json_decode(json_encode($choose_subject), true);
                        $subject_name = $choose_subject[0]['subject_name'];
                    }

                    $sql = "SELECT * FROM subject WHERE grade_id = '" . $filter_type . "'";
                    $subjects = DB::select($sql);
                    $subjects = json_decode(json_encode($subjects), true);
                    $sql = "SELECT * FROM grade WHERE grade_id = '" . $filter_type . "'";
                    $choose_grade = DB::select($sql);
                    $choose_grade = json_decode(json_encode($choose_grade), true);
                    $grade_name = $choose_grade[0]['grade_name'];
                }
                ?>
                <select name="grade" id="choose-grade-1" data-desc="<?php if ($video_history == "") {
                    echo 'choose grade';
                } else {
                    echo $grade_name;
                } ?>" class="choose-test-grade">
                    <?php
                    $sql = "SELECT * FROM grade";
                    $grades = DB::select($sql);
                    $grades = json_decode(json_encode($grades), true);
                    if ($grades) {
                        foreach ($grades as $grade) {
                            /* get all grades and show as drop down */
                            echo '<option value="' . $grade['grade_id'] . '">' . $grade['grade_name'] . '</option>';
                        }
                    }
                    ?>
                </select>
                <div id="subject-1-wrapper">
                    <select name="subject" id="choose-subject-1" data-desc="<?php if ($video_history == "") {
                        echo 'choose subject';
                    } else {
                        echo $subject_name;
                    } ?>">
                        <?php
                        if ($video_history != "") {
                            echo '<option value="all">All</option>';
                            foreach ($subjects as $subject) {
                                /* subjects loop to show as drop down */
                                echo '<option value="' . $subject['subject_id'] . '">' . $subject['subject_name'] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <button class="btn btn-success list-tests" onclick="filter_category(1)">List</button>
                <button class="btn btn-success" onclick="filter_category(0)" style="margin-left: 50px;">All Videos</button>


                <div class="float-right" style="float: right;">
                  <p>
                    <select name="admin-nav" id="admin-nav" data-desc="admin nav" class="admin-nav action-dropdown">
                      <option value="/admin/tests">admin-tests</option>
                      <?php
                      if ($loggedUser->user_role == 1) {
                        ?>
                        <option value="/admin/users">admin-users</option>
                        <option value="/admin/videos">admin-videos</option>
                        <option value="/admin/options">admin-options</option>
                        <?php
                      }
                      ?>
                    </select>
                  </p>
                </div>

    </div><!-- Col 1 - End -->
  </div><!-- Row 1 - End -->
</div><!-- Container 1 - End -->



        <table class="table table-striped table-tests gs-small-table-fontsize table-tests"><!-- Table Start -->
            <thead>
            <tr>
                <th class="left-align">
                    <select class="select" id="sort-admin-videos" data-desc="Sort">
                        <option>date submitted</option>
                        <option>grade - subject</option>
                        <option>name a-z</option>
                    </select>
                </th>
                <th>Grade - Subject</th>
                <th class="hidden-sm-down">Date Submitted</th>
                <th>Public</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody id="choose-tests-table"></tbody><!-- Everything in Table Body in js -->

        </table><!-- Table End -->

        <div class="table-footer">
          <div id="Pagination" class="pagination" onclick="start_drag()"></div>
          <div class="view-per-page pull-left">
            <span class="pull-left mr5 normal">View</span>
            <select name="view-test" class="select select-whitetxt" id="view-test" data-desc="20">
              <option>10</option>
              <option>20</option>
              <option>50</option>
              <option>100</option>
            </select>
          </div>
          <div class="clear"></div>
        </div>

        </div><!-- Col 2 - End -->
      </div><!-- Row 2 - End -->

    </div> <!-- Container 2- End -->

<script type="text/javascript">
    $(document).ready(function () {
        <?php
        if($video_history != ""){
        ?>
        $("#choose-subject-1 .dd-selected-value").val('<?php echo $subject_id; ?>');
        $("#choose-subject-1 .dd-selected").text('<?php echo $subject_name; ?>');

        $("#choose-grade-1 .dd-selected-value").val('<?php echo $filter_type; ?>');
        $("#choose-grade-1 .dd-selected").text('<?php echo $grade_name; ?>');
        <?php
        }
        ?>
        grade_value = $("#choose-grade-1").find(".dd-selected-value").val();
    });
</script>
@endsection
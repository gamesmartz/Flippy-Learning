@extends('layouts.admin')

@section('title', "Manage Tests - GameSmartz")
@section('description', "Manage Tests")

@push('head')
<script src="{{ asset('assets/js/page/admin-tests.js') }}"></script>
<script type="text/javascript">
    var user_role = parseInt(<?php echo $loggedUser->user_role; ?>);
</script>
<?php

use Illuminate\Support\Facades\DB;
/* get admin history from users table by primary id - user id */

$sql = "SELECT admin_history FROM users WHERE user_id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $user_id);
$user_id = $loggedUser->user_id;
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($admin_history);
$stmt->fetch();
$stmt->free_result();
$stmt->close();

if ($admin_history != "") {
    $history_array = unserialize($admin_history);
    $filter_type = $history_array['filter'];
    $subject_id = $history_array['subject_id'];

    if ($subject_id == "all") {
        /** get test with related info(subject, grade) from tests table using sub query
         * by grade id = filter type and group by test id(in order to remove duplicate) if admin history is not empty
         */
        $sql = "
               SELECT
                  test_subject.*, grade.grade_name
               FROM
                  (    SELECT test.*, subject.subject_name, subject.grade_id
                       FROM test
                       JOIN subject
                       ON test.subject_id = subject.subject_id OR CONCAT(',', test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                       WHERE test.public IN (1,2) AND subject.grade_id = " . $filter_type . "
                       GROUP BY test.test_id
                       ORDER BY test.created_time DESC 
                  ) test_subject
               JOIN grade
               ON test_subject.grade_id = grade.grade_id
            ";
    } else {
        /**
         * if subject id is specified, adds one more where condition subject id = subject
         * and others are same as above query
         * but in this case filter type condition is not needed, because subject id is given by grade id
         */
        $sql = "
               SELECT
                  test_subject.*, grade.grade_name
               FROM
                  (    SELECT test.test_id, test.test_name, test.video_link, test.test_type, test.attachment, test.author_id, test.popularity, test.public, test.school_year, test.created_time, test.gradeless, subject.subject_id, subject.subject_name, subject.grade_id
                       FROM test
                       JOIN subject
                       ON test.subject_id = subject.subject_id OR CONCAT(',', test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                       WHERE test.public IN (1,2)
                  ) test_subject
               JOIN grade
               ON test_subject.grade_id = grade.grade_id
               WHERE test_subject.subject_id = $subject_id
               GROUP BY test_subject.test_id
               ORDER BY test_subject.created_time DESC 
            ";
    }
} else {
    /**
     * if admin_history is empty, get all info
     * just removed where condition from above query
     * */
    $sql = "
           SELECT
              test_subject.*, grade.grade_name
           FROM
              (    SELECT test.*, subject.subject_name, subject.grade_id
                   FROM test
                   JOIN subject
                   ON test.subject_id = subject.subject_id
                   WHERE test.public IN (1,2)
                   ORDER BY test.created_time DESC
              ) test_subject
           JOIN grade
           ON test_subject.grade_id = grade.grade_id
        ";
}

/* get test array from above query */
$test = DB::select($sql);
if ($test) :
    $test = json_decode(json_encode($test), true);
endif;
if ($test) { ?>
    <script type="text/javascript">
        var members = [];
    </script>
    <?php
    /* get user email by user id, just ready for execute. */
    $sql = "SELECT email FROM users WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $user_id);

    /* get questions from question table by test id just ready for execute. */
    $sql = "SELECT * FROM question WHERE test_id = ?";
    $stmt_1 = $db->prepare($sql);
    $stmt_1->bind_param("s", $test_id);

    /* test array loop */
    for ($i = 0; $i < count($test); $i++) {
        /* execute above stmt query */
        $user_id = $test[$i]['author_id'];
        $stmt->execute();
        $stmt->store_result();
        $row = $stmt->num_rows();
        $stmt->bind_result($user_email);
        $stmt->fetch();
        $stmt->free_result();

        /* execute above stmt1 query */
        $test_id = $test[$i]['test_id'];
        $stmt_1->execute();
        $stmt_1->store_result();
        $row_1 = $stmt_1->num_rows();
        $stmt_1->free_result();

        $test[$i]['created_time'] = date("m-d-y", strtotime($test[$i]['created_time']));

        if ($test[$i]['gradeless'] == 1) {
            $grade_subject = $test[$i]['subject_name'];
        } else {
            $grade_subject = $test[$i]['grade_name'] . " - " . $test[$i]['subject_name'];
        }
    ?>
        <!-- push test array to javascript members array -->
        <script type="text/javascript">
            members.push(['<?php echo $test[$i]['test_id']; ?>', '<?php echo str_replace("'", "&#39;", $test[$i]['test_name']); ?>', '<?php echo $user_email; ?>', '<?php echo $test[$i]['popularity']; ?>', '<?php echo $test[$i]['public']; ?>', '<?php echo $test[$i]['test_type']; ?>', '<?php echo $test[$i]['attachment']; ?>', '<?php echo $test[$i]['created_time']; ?>', '<?php echo $grade_subject; ?>', '<?php echo $row_1; ?>', '<?php echo $test[$i]['school_year']; ?>', '<?php echo $test[$i]['video_link'] ?>']);
        </script>
    <?php
    }

    $stmt->close();
    $stmt_1->close();
} else { ?>
    <script type="text/javascript">
        var members = [
            ['none', 'No tests in this category.', '', '', '', '', '', '', '', '', '', '']
        ];
    </script>
<?php
} ?>
@endpush

@section('content')
<input type="hidden" id="test_id" value="">
<!-- Modal Popularity - Start -->
<div class="modal-wrapper popularity-modal">
    <div class="b-close"></div>
    <p class="mt-3" style="font-size: 18px;">
        popularity score:
    </p>
    <input type="text" class="textbox" id="popularity_score" value="" style="width: 100px;">
    <div class="mt-3">
        <button type="submit" class="btn btn-success" onclick="save_popularity()">Ok</button>
    </div>
</div>
<!-- Modal Popularity - End -->

<!-- Modal Public - Start -->
<div class="modal-wrapper public-modal">
    <div class="b-close"></div>
    <div class="public-modal-wrapper">
        <select name="public_value" id="public_value" data-desc="public" class="public_value">
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select>
    </div>
    <div class="mt-3">
        <button type="submit" class="btn btn-success" onclick="save_public()">Ok</button>
    </div>
</div>
<!-- Modal Public - Start -->

<!-- Modal School Year - Start -->
<div class="modal-wrapper school-year-modal">
    <div class="b-close"></div>
    <p class="mt-3" style="font-size: 18px;">
        order #:
    </p>
    <input type="text" class="textbox" id="school_year" value="" style="width: 100px;">
    <div class="mt-3">
        <button type="submit" class="btn btn-success" onclick="save_school_year()">ok</button>
    </div>
</div>
<!-- Modal School Year - End -->

<!-- Modal Delete Test - Start -->
<div class="modal-wrapper delete-modal-wrapper">
    <div class="b-close"></div>
    <h4>Are you sure you want to delete the test?</h4>
    <hr>
    <p class="mt-3" style="font-size: 17px;">
        This is not undoable!<br><br>Are you sure you wish to delete the test?
    </p>
    <div class="mt-3">
        <br>
        <button type="submit" class="btn btn-success" onclick="delete_confirm()">Yes, Delete the Test, I Will Never
            Need it Again
        </button>
    </div>
</div>
<!-- Modal Delete Test - End -->

<!-- Modal More Subject IDs - Start -->
<div class="modal-wrapper more-subject-modal">
    <div class="b-close"></div>
    <p class="mt-3" style="font-size: 18px;">
        More Subject IDs - Format:<br>
        10,11,12,13
    </p>
    <input type="text" class="textbox" id="more_subject" value="" style="width: 100px;">
    <div class="mt-3">
        <button type="submit" class="btn btn-success" onclick="save_more_subject()">ok</button>
    </div>
</div>
<!-- Modal More Subject IDs - End -->

<!-- Modal Reset Progress - Start -->
<div class="modal-wrapper reset-progress-modal">
    <div class="b-close"></div>
    <p style="font-size: 1.1rem;">This will reset the test progress<br>to 0 questions completed. - This can not be undone.</p>
    <div class="mt-3">
        <br>
        <button type="submit" class="btn btn-success" onclick="close_resetModal()">ok</button>
    </div>
</div>
<!-- Modal Reset Progress - End -->

<!-- Modal Set Max Height - Start -->
<div class="modal-wrapper max-height-modal">
    <div class="b-close"></div>
    <p class="mt-3" style="font-size: 18px;">
        max height:
    </p>
    <input type="text" class="textbox" id="max_height" value="" style="width: 100px;">
    <div class="mt-3">
        <button type="submit" class="btn btn-success" onclick="save_max_height()">ok</button>
    </div>
</div>
<!-- Modal Set Max Height - End -->

<!-- Modal Make Gradeless - Start -->
<div class="modal-wrapper gradeless-modal">
    <div class="b-close"></div>
    <div class="public-modal-wrapper">
        <select name="gradeless_value" id="gradeless_value" data-desc="no" class="gradeless_value">
            <option value="yes">yes</option>
            <option value="no">no</option>
        </select>
    </div>
    <div class="mt-3">
        <button type="submit" class="btn btn-success" onclick="save_gradeless()">ok</button>
    </div>
</div>
<!-- Modal Make Gradeless - End -->

<!-- Modal Attach Video - Start -->
<div class="modal-wrapper video-link-modal">
    <div class="b-close"></div>
    <p class="mt-3" style="font-size: 18px;">
        video:
    </p>
    <input type="text" class="textbox" id="video_link" value="" style="width: 100%;" placeholder="https://www.youtube.com/embed/sNHlZvk09tI">
    <div class="mt-3">
        <button type="submit" class="btn btn-success" onclick="save_video()">ok</button>
    </div>
</div>
<!-- Modal Attach Video - End -->


<!-- Modal Attach Thumbnail Image - Start -->
<div class="modal-wrapper graphics-file-size-error">
    <div class="b-close"></div>
    <p class="h4">image size too large</p>
    <p>images are restricted to sizes to under 300kb<br>
        They also need to be in the jpg or png format.
    </p>

    <button class="btn-primary btn-file graphics-file-size-error-close" type="button">ok</button>
    </p>
</div>
<form class="input-file" id="attach_image_form" method="post" enctype="multipart/form-data" action="/ajax/async-tests/edit" style="margin-bottom:0px;">
    <input type="hidden" name="type" value="attach_test_image">
    <input type="hidden" name="test_id" value="">
    <input type="file" name="imageUpload" id="attach_image" class="input-file-graphics" onchange="upload_attach()">
</form>
<!-- Modal Attach Thumbnail Image - End -->


<!-- Container 1 - Start -->
<div class="container-fluid">
    <div class="row justify-content-center" style="background-color: #ebe7e5;">
        <!-- Row 1 - Start -->
        <div class="col-12">
            <!-- Col 1 - Start -->
            <div class="lead page-name">Admin Tests</div>

            <?php
            if ($admin_history != "") {
                /**
                 * get subjects list and grades list from subjects and grades table
                 * if admin history value is not empty, load previous selected value to dropdown list
                 */
                if ($subject_id == "all") {
                    $subject_name = "All";
                } else {
                    $sql = "SELECT * FROM subject WHERE subject_id = '$subject_id'";
                    $choose_subject = DB::select($sql);
                    if ($choose_subject) :
                        $choose_subject = json_decode(json_encode($choose_subject), true);
                    endif;
                    $subject_name = $choose_subject[0]['subject_name'];
                }
                if ($loggedUser->user_role == 1) {
                    $sql = "SELECT * FROM subject WHERE grade_id = '" . $filter_type . "'";
                } else {
                    $sql = "SELECT * FROM subject WHERE grade_id = '" . $filter_type . "' AND view_option = 1";
                }
                $subjects = DB::select($sql);
                $subjects = json_decode(json_encode($subjects), true);
                $sql = "SELECT * FROM grade WHERE grade_id = '" . $filter_type . "'";
                $choose_grade = DB::select($sql);
                $choose_grade = json_decode(json_encode($choose_grade), true);
                $grade_name = $choose_grade[0]['grade_name'];
            }
            ?>
            <select name="grade" id="choose-grade-1" data-desc="<?php if ($admin_history == "") {
                                                                    echo 'choose grade';
                                                                } else {
                                                                    echo $grade_name;
                                                                } ?>" class="choose-test-grade">
                <?php
                if ($loggedUser->user_role == 1) {
                    $sql = "SELECT * FROM grade";
                } else {
                    $sql = "SELECT * FROM grade WHERE view_option = 1";
                }
                $grades = DB::select($sql);
                /* drop down grades list */
                if ($grades) {
                    $grades = json_decode(json_encode($grades), true);
                    foreach ($grades as $grade) {
                        echo '<option value="' . $grade['grade_id'] . '">' . $grade['grade_name'] . '</option>';
                    }
                }
                ?>
            </select>
            <div id="subject-1-wrapper">
                <select name="subject" id="choose-subject-1" data-desc="<?php if ($admin_history == "") {
                                                                            echo 'choose subject';
                                                                        } else {
                                                                            echo $subject_name;
                                                                        } ?>">
                    <?php
                    if ($admin_history != "") {
                        /* drop down grades list if admin history is not empty */
                        echo '<option value="all">All</option>';
                        foreach ($subjects as $subject) {
                            echo '<option value="' . $subject['subject_id'] . '">' . $subject['subject_name'] . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <button class="btn btn-success gs-dropdown-match-btn" onclick="filter_category(1)">List</button>
            <button class="btn btn-success gs-dropdown-match-btn" onclick="filter_category(0)" style="margin-left: 50px;">All Tests</button>
            <div class="pull-right">
                <p>
                    <select name="admin-nav" id="admin-nav" data-desc="admin nav" class="admin-nav action-dropdown">
                        <option value="/admin/tests">admin-tests</option>
                        <?php
                        if ($loggedUser->user_role == 1) {
                        ?>
                            <option value="/admin/users">admin-users</option>
                            <option value="/admin/videos">admin-videos</option>
                            <option value="/admin/options">admin-options</option>
                            <option value="/admin/create-test-multiple-choice">create-test-multiple-choice</option>
                        <?php
                        }
                        ?>
                    </select>
                </p>
            </div>

        </div><!-- Col 1 - End -->
    </div><!-- Row 1 - End -->
</div><!-- Container 1 - End -->


<!-- Container 2- Start -->
<div class="container-fluid">
    <div class="row justify-content-center">
        <!-- Row 2 - Start -->
        <div class="col-12 gs-small-12-col">
            <!-- Col 2 - Start -->

            <table class="table table-striped table-tests gs-small-table-fontsize">
                <!-- Table Start -->
                <thead>
                    <tr>
                        <th width="" class="left-align" style="font-weight: inherit;">
                            <select class="select" id="sort-admin-tests" data-desc="Sort">
                                <option>date created</option>
                                <option>grade - subject</option>
                                <option>popularity</option>
                                <option>email a-z</option>
                                <option>name a-z</option>
                            </select>
                        </th>
                        <th>Grade - Subject</th>
                        <th class="hidden-sm-down">Author Email</th>
                        <th class="hidden-sm-down">Date Created</th>
                        <th class="hidden-sm-down">#</th>
                        <th class="hidden-sm-down">Order</th>
                        <th class="hidden-sm-down">Pop</th>
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
      if($admin_history != ""){
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
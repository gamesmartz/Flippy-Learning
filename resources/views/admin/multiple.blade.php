@extends('layouts.admin')

@section('title', "Create Multiple Choice Test - GameSmartz")
@section('description', "Create your own custom multiple choice test and learn.")

@push('head')
<script src="{{ asset('assets/js/page/create-multiple.js') }}"></script>
<style>
    .extra-answer {
        display: none !important;
    }
</style>
<?php
    use Illuminate\Support\Facades\DB;
    if (isset($_GET['id'])) {
        $test_id = $_GET['id'];

// get author id from test table by test id
        $sql = "SELECT author_id FROM test WHERE test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $test_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($author_id);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

//used to check if the author_id = the current user, meaning only the person who created the test can edit it
//        if (isset($test_id) && $_SESSION['RB_USER_ID'] != $author_id) {
//            header('Location: index.php');
//            exit;
//        }
    }
    ?>
    <script type="text/javascript">
        var mastery_number = '';
    </script>

    <?php
// get memorization mastery and problem mastery value from db -MN
    $sql = "SELECT option_value FROM config WHERE option_name = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $option_name);

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
    $stmt->close();
    $problem_mastery = $option_value;

// get the default time award value from db, the one set in admin options -MN
    $admin_time_award = getConfigValue($db, 'time_award');
// puts a default value in from global, so its not NULL -MN
    $award_time = $admin_time_award;
    ?>

    <?php
    if (isset($_GET['id'])) {
// if get param is given, get questions list as array by test id = get param value
        $sql = "SELECT question_id, question_title, attach_image, attach_audio, show_image, show_button, correct_note FROM question WHERE test_id = ? ORDER BY question_id";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $test_id);
        $stmt->execute();
        $stmt->store_result();
        $row = $stmt->num_rows();
        $stmt->bind_result($question_id, $question_title, $attach_image, $attach_audio, $show_image, $show_button, $correct_note);

//  get answers texts from answers table by question id just ready for execute, it will be executed on question loop
        $sql = "SELECT text, attachment, correct FROM answer WHERE question_id = ?";
        $stmt_1 = $db->prepare($sql);
        $stmt_1->bind_param("s", $question_id);

    if ($row != 0) {
       
// if there is an actual test loaded (not just blank create test, load these elements into HTMLs -MN        
        $sql = "SELECT * FROM test WHERE test_id = '$test_id'";
        $test = DB::select($sql);
        $test = json_decode(json_encode($test), true);
        $subject_id = $test[0]['subject_id'];
        $sql = "SELECT * FROM subject WHERE subject_id = '$subject_id'";
        $subject = DB::select($sql);
        $subject = json_decode(json_encode($subject), true);
        $grade_id = $subject[0]['grade_id'];
        $sql = "SELECT * FROM grade WHERE grade_id = '$grade_id'";
        $grade = DB::select($sql);
        $grade = json_decode(json_encode($grade), true);

// if the DB is not blank, use this value for award time
        $award_time = ($test[0]['award_time'] == '') ? $admin_time_award : $test[0]['award_time'];

        ?>
        
        <script type="text/javascript">
            var members = [];
            // test initialize
            $(document).ready(function () {
                $("#test_id").val('<?php echo $test_id; ?>');
                $("#choose-grade-1 .dd-selected-value").val('<?php echo $grade_id; ?>');
                $("#choose-grade-1 .dd-selected").text('<?php echo $grade[0]['grade_name']; ?>');
                $("#choose-subject-1 .dd-selected-value").val('<?php echo $subject_id; ?>');
                $("#choose-subject-1 .dd-selected").text('<?php echo $subject[0]['subject_name']; ?>');
                $("#test_name").val('<?php echo str_replace("'", "\'", $test[0]['test_name']); ?>');
                $("#choose-mastery-type .dd-selected-value").val('<?php echo $test[0]['mastery_type']; ?>');
                $("#choose-mastery-type .dd-selected").text('<?php echo $test[0]['mastery_type']; ?>');

// this was used to set default mastery number per test, not being used at the moment -MN
//                var mastery_number = '<?php //echo $test[0]['mastery_number']; ?>//';
//                $("#choose-mastery-number .dd-selected-value").val('<?php //echo ($test[0]['mastery_number'] == '') ? 'Use Global' : $test[0]['mastery_number']; ?>//');
//                $("#choose-mastery-number .dd-selected").text(mastery_number);                

                <?php
                if($test[0]['form_options'] != ""){
                $form_options = unserialize($test[0]['form_options']);
                ?>
                $("#choose-font-size .dd-selected-value").val('<?php echo $form_options['font'] ?>');
                $("#choose-font-size .dd-selected").text('<?php echo $form_options['font'] ?>');
                $("#choose-question-width .dd-selected-value").val('<?php echo $form_options['width'] ?>');
                $("#choose-question-width .dd-selected").text('<?php echo $form_options['width'] ?>');
                <?php
                }
                $optional_note = $test[0]['optional_note'];
                $optional_note = str_replace(array("\r\n", "\r", "\n"), "&#10;", $optional_note);
                $optional_note = str_replace("'", "&#39;", $optional_note);
                ?>
                $("#optional_note").html('<?php echo $optional_note; ?>');
            });
        </script>

    <?php
    /* Question Loop */
    while ($stmt->fetch()){
    /* execute answer question inside this question loop */
    $stmt_1->execute();
    $stmt_1->store_result();
    $stmt_1->bind_result($text, $answer_attach, $correct);
    $i = 0;
    while ($stmt_1->fetch()) {
        $answer[$i]['text'] = $text;
        $answer[$i]['attach'] = $answer_attach;
        $answer[$i]['correct'] = $correct;
        $i++;
    }
    $stmt_1->free_result();

    for ($j = $i; $j < 5; $j++) {
        $answer[$j]['text'] = "";
        $answer[$j]['attach'] = "";
        $answer[$j]['correct'] = 0;
    }

    $question_title = str_replace(array("\r\n", "\r", "\n"), "<br>", $question_title);
    $question_title = str_replace("'", "&#39;", $question_title);
    $correct_note = str_replace(array("\r\n", "\r", "\n"), "<br>", $correct_note);
    $correct_note = str_replace("'", "&#39;", $correct_note);

    ?>
        <script type="text/javascript">
            /* push question array to javascript members to show on table using jQuery */
            members.push([
                '<?php echo $question_id; ?>',
                '<?php echo str_replace("'", "&#39;", $question_title); ?>'.replace("&#39;", "'"),
                '<?php echo str_replace("'", "&#39;", $attach_image); ?>'.replace("&#39;", "'"),
                '<?php echo str_replace("'", "&#39;", $attach_audio); ?>'.replace("&#39;", "'"),
                parseInt('<?php echo $show_image; ?>'),
                parseInt('<?php echo $show_button; ?>'),
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
            ]);
        </script>
    <?php
    }

    }else{ ?>
        <script type="text/javascript">
            var members = [
                ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '']
            ];
        </script>
    <?php
    }

    $stmt->free_result();
    $stmt->close();
    $stmt_1->close();

    }else{ ?>
        <script type="text/javascript">
            var members = [
                ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '']
            ];
        </script>
        <?php
    } ?>

        <script type="text/javascript">

<?php // JS setting award time from PHP Equivalent. If blank set the value from admin options. Sets value of selected -MN ?>   
    var award_time = '<?php echo $award_time ?>';
    $("#choose-award-time .dd-selected-value").val(award_time);
    $("#choose-award-time .dd-selected").text(award_time);
    // console.log(award_time);

<?php // JS if values are set above from database, transfer value to JS equivalent -MN ?>
    var memorization_mastery = '<?php echo $memorization_mastery; ?>';
    var problem_mastery = '<?php echo $problem_mastery; ?>';

</script>
@endpush

@section('content')
<div class="modal-wrapper graphics-file-size-error">
  <div class="b-close"></div>
  <p class="h4">image size too large</p>
  <p>images are restricted to sizes:<br>
    100 x 200 and under 300 Kb.<br>
    They also need to be in the jpg or png format. </p>
  <p> For more information on how to create the correct<br>
    size <a href="javascript:;" class="correct-size-image">click here</a> </p>
  <p>
    <button class="btn btn-success btn-file graphics-file-size-error-close" type="button">ok</button>
  </p>
</div>
<div class="modal-wrapper modal-youtube-video correct-size-image-video">
  <div class="b-close"></div>
  <iframe width="854" height="509" src="//www.youtube.com/embed/D1YdXYYamkE" frameborder="0" allowfullscreen></iframe>
</div>
<div class="modal-wrapper require-modal-wrapper">
  <div class="b-close"></div>
  <h4>Required Fields:</h4>
  <hr>
  <p class="mt25" style="font-size: 17px;">
    <span class="grade-hint">Grade & Subject</span><br>
    <span class="test-hint">Test Name</span><br>
    <span class="question-hint">Question</span><br>
    <span class="answer-1-hint">Answer 1</span><br>
    <span class="answer-2-hint">Answer 2</span><br>
    <span class="correct-hint">Correct Answer</span>
  </p>
  <div class="mt15">
    <br>
    <button type="submit" class="btn btn-success" onclick="close_requieModal()">ok</button>
  </div>
</div>
<div class="modal-wrapper save-modal-wrapper">
  <div class="b-close"></div>
  <h4>Test Saved!</h4>
  <hr>
  <!--<p class="mt25" style="font-size: 17px;">
    Remember, to find the tests you created:<br>Click '<strong>Choose Tests</strong>'<br>Then click '<strong>Personal Tests</strong>'
  </p>-->
  <div class="mt15">
    <br>
    <button type="submit" class="btn btn-success" onclick="close_saveModal()">ok</button>
  </div>
</div>
<div class="modal-wrapper option-modal-wrapper">
  <div class="b-close"></div>
  <p class="lead">Time award per correct question:</p>
  <div>
  <?php // data-desc displays the $award_time in the drop down selector -MN ?>
    <select name="award-time" id="choose-award-time" data-desc="<?php echo $award_time ?>" class="award-time">
      <option value="1:30">1:30</option>
      <option value="1:40">1:40</option>
      <option value="1:50">1:50</option>
      <option value="2:00">2:00</option>
      <option value="2:10">2:10</option>
      <option value="2:20">2:20</option>
      <option value="2:30">2:30</option>
      <option value="2:40">2:40</option>
      <option value="2:50">2:50</option>
      <option value="3:00">3:00</option>
    </select>
    <p class="lead-in-row">min/sec</p>
  </div>
<!--  <p class="lead" id="mastery-description">Memorization:<br>Number of times a question needs to be answered<br>correct in a row before the question is mastered:</p>-->
  <div id="mastery-number-wrapper" hidden>
    <select name="mastery-number" id="choose-mastery-number" data-desc="" class="mastery-number">
      <option value="3">3</option>
      <option value="4">4</option>
      <option value="5">5</option>
      <option value="6">6</option>
      <option value="7">7</option>
    </select>
  </div>
  <p class="lead">font size:</p>
  <div>
    <select name="choose-font-size" id="choose-font-size" data-desc="1.2em" class="choose-font-size">
      <option value="1em">1em</option>
      <option value="1.1em">1.1em</option>
      <option value="1.2em">1.2em</option>
      <option value="1.3em">1.3em</option>
      <option value="1.4em">1.4em</option>
      <option value="1.5em">1.5em</option>
      <option value="1.6em">1.6em</option>
      <option value="1.7em">1.7em</option>
      <option value="1.8em">1.8em</option>
      <option value="1.9em">1.9em</option>
      <option value="2em">2em</option>
    </select>
  </div>
  <p class="lead">width:</p>
  <div>
    <select name="question-width" id="choose-question-width" data-desc="260" class="question-width">
      <option value="160">160</option>
      <option value="180">180</option>
      <option value="200">200</option>
      <option value="220">220</option>
      <option value="240">240</option>
      <option value="260">260</option>
      <option value="280">280</option>
      <option value="300">300</option>
    </select>
  </div>
  <div class="mt15" style="margin-right: 10px;">
    <button class="btn btn-success" onclick="close_optionModal()">ok</button>
  </div>
</div>

<div class="modal-wrapper audio-modal-wrapper">
  <div class="b-close"></div>
  <h4>Please save test before playing audio</h4>
  <div class="mt25">
    <br>
    <button type="submit" class="btn btn-success" onclick="close_audioModal()">ok</button>
  </div>
</div>
<div class="modal-wrapper delete-question-modal">
  <div class="b-close"></div>
  <h4>Do you want to delete this question?</h4>
  <div class="mt15">
    <br>
    <button type="submit" class="btn btn-success" onclick="close_delModal()">yes</button>
  </div>
</div>
<div class="modal youtube-modal">
  <div class="modal-back"></div>
  <div class="modal-center">
    <div class="modal-content">
      <div class="modal-close"></div>
      <iframe width="854" height="509" src="" frameborder="0" allowfullscreen></iframe>
    </div>
  </div>
</div>


<script type="text/javascript">
    var user_role = '<?php echo $loggedUser->user_role; ?>';
</script>


<div class="container-fluid"> <?php // Container 1 Start ?>
  <div class="row justify-content-center" style="background-color: #29a4ed;"> <?php // Row 1 Start ?>
    <div class="col-12 text-left lead mb-2">Multiple Choice Test</div>
    <div class="col-12 text-left"> <?php // Col 1 Start ?>
          <div>
            <select name="grade" id="choose-grade-1" data-desc="choose grade">
              <?php
              $sql = "SELECT * FROM grade";
              $grades = DB::select($sql);
              if ($grades) {
                $grades = json_decode(json_encode($grades), true);
                /* grades loop to show as drop down */
                foreach ($grades as $grade) {
                  echo '<option value="' . $grade['grade_id'] . '">' . $grade['grade_name'] . '</option>';
                }
              }
              ?>
            </select>
          </div>
            <div id="subject-1-wrapper">
              <select name="subject" id="choose-subject-1" data-desc="choose subject"></select>
            </div>
            <div class="clearfix"></div>
            <div class="form-group">
              <div class="text-left lead mb-1">Test Name</div>
              <input type="text" name="test_name" id="test_name" class="form-control" style="width: 280px;" placeholder="Test Name" >
            </div>
            <div class="clearfix"></div>

            <div class="float-left"
                 style="<?php echo $loggedUser->user_role == 2 ? 'display: none;' : ''; ?>">
              <p class="lead mb-2">Mastery Type</p>
              <select name="mastery-number" id="choose-mastery-type" data-desc="memorization"
                      class="mastery-type">
                <option>memorization</option>
                <!--<option>problem solving</option>-->
              </select>
            </div>
            <div class="float-left mr-3"
                 style="<?php echo $loggedUser->user_role == 2 ? 'display: none;' : ''; ?>" >
              <p class="lead mb-2">Test Options</p>
              <button class="btn btn-success" onclick="open_options()">test options</button>
            </div>
            <div class="float-left mt5"
                 style="<?php echo $loggedUser->user_role == 2 ? 'display: none;' : ''; ?>">
              <p class="lead mb-2">Optional Notes</p>
              <textarea name="test notes" id="optional_note" placeholder="Notes about the test."
                        class="form-control mb-3"
                        style="min-width: 150px; min-height: 70px;"></textarea>
            </div>
            <input type="hidden" id="test_id" value="">

    </div> <?php // Col 1 End ?>
  </div> <?php // Row 1 End ?>
</div> <?php // Container 1 End ?>


<div class="container-fluid gs-create-tests-bg" > <?php // Container 2 Start ?>
  <div class="row justify-content-center" style="background-color: #29a4ed;"> <?php // Row 2 Start ?>
    <div class="col-12 text-left"> <?php // Col 2 Start ?>

        <table class="table gs-small-table-fontsize form-group">
          <tbody>
            <tr class="no-hover-tr testinfo-wrapper">
              <td class="create-test">

                    <div id="question_form"></div>

                    <div id="player" class="mt-4"></div><!-- Entire submit button pulled from js -->

                    <div>
                    <?php // onclick pass in award_time which, if null has the value from 'config' in db, if not has the value from 'test' in db, so we can set the default on new test creation -MN ?>
                      <button class="btn btn-success" type="submit" onclick="save_test('save_next', award_time)">save
                        &amp; next question<span class="icon-next"></span></button>
                      <button class="btn btn-success ml-1" type="submit" onclick="save_test('save', award_time)">save</button>
                    </div>
                <div class="mb-3 d-flex align-items-center" style="<?php echo $loggedUser->user_role == 2 ? 'display: none;' : ''; ?>">
                  <div class="white-trash btn btn-success my-3"  onclick="delete_question()"></div><span class="ml-2">Delete Question</span>
                </div>

              </td>
            </tr>
          <tr>
            <td class="table-tests-pagination-background">
              <div id="Pagination" class="pagination"></div>
            </td>
          </tr>
          </tbody>
        </table>

    </div>  <?php // Col 2 End ?>
  </div> <?php // Row 2 End ?>
</div> <?php // Container 2 End ?>
@endsection
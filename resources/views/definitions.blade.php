@extends('layouts.app')

@php
// DEFINITIONS PAGE
@endphp

<?php
$subject_id_from_url = null;

$question_id = $question->question_id;
$test_id = $question->test_id;
$subject_id = $test->subject_id;

// get test_ids from the test table (more than one match), for every subject there are multiple test ids that match
$sql = "SELECT test_id FROM test WHERE subject_id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $subject_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($test_id_from_test_table);

// make array to get ready for while loop
$matching_test_ids_from_url = array();
$matching_test_ids_from_url_i = 0;

// for every item found in questions, loop through, generating an array of objects with all data per test id, found in answers,
// so for example an array with 20 items, so this is the raw data
while ($stmt->fetch()) {

    $matching_test_ids_from_url[$matching_test_ids_from_url_i]['test_id'] = $test_id_from_test_table;

    $matching_test_ids_from_url_i++;
}

$stmt->free_result();
$stmt->close();

// and array was created above, so loop through checking to see if there is a match each time through
$test_id_from_question_table_match = 'no-match';

foreach ($matching_test_ids_from_url as $matching_test_ids_from_url_i => $matching_test_ids_from_url_item) {

    if ($test_id == $matching_test_ids_from_url_item['test_id']) {

        $test_id_from_question_table_match = 'matched';
    }
}
// compare the two to see if they match, if they dont, then the url info is bogus, redirect
if ($test_id_from_question_table_match == 'matched') {

    // get test_id with definition id
    $sql = "SELECT test_id FROM question WHERE question_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $question_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($test_id);
    $stmt->fetch();
    $stmt->free_result();
    $stmt->close();
}

// get info from test table with test id
$sql = "SELECT test_type, subject_id, test_name, subject_extra_name FROM test WHERE test_id = ?";
// stmt Start
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $test_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($test_type, $subject_id, $test_name, $subject_extra_name);
$stmt->fetch();
$stmt->free_result();
$stmt->close();

// get info from subject table with subject id
$sql = "SELECT subject_name, grade_id FROM subject WHERE subject_id = ?";
// stmt Start
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $subject_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($subject_name, $grade_id);
$stmt->fetch();
$stmt->free_result();
$stmt->close();


// get info from grade table with grade id
$sql = "SELECT grade_name FROM grade WHERE grade_id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $grade_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($grade_name);
$stmt->fetch();
$stmt->free_result();
$stmt->close();


// get info from config table on gs_350_default
$option_name = "gs_350_default";
$sql = "SELECT option_value FROM config WHERE option_name = ?";
// stmt Start
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $option_name);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($gs_350_default);
$stmt->fetch();
$stmt->free_result();
$stmt->close();

// get info from config table on gs_800_default
$sql = "SELECT option_value FROM config WHERE option_name = ?";
$option_name = "gs_800_default";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $option_name);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($gs_800_default);
$stmt->fetch();
$stmt->free_result();
$stmt->close();


// this is used for to show ALL definitions for an entire grade and subject
if (!empty($subject_id)) {


    // get all data from questions from test id. an array of '20' objects with all question data
    // loop through this and return only the array with that matches the $question_id
    // result: $correct_keyword_array
    // get questions info from question table on test id
    $sql = "
SELECT

question_title, attach_image, attach_audio, show_button, correct_note, question_answer, image_800, image_350, wiki_link, khan_link, question_id, canonical, full_audio, title_tag, description_front, canonical_all_subject

FROM
test
INNER JOIN question ON question.test_id = test.test_id
WHERE
test.subject_id = ?
GROUP BY question.question_answer
ORDER BY question.question_answer ASC
";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $subject_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($question_title, $attach_image, $attach_audio, $show_button, $correct_note, $question_answer, $image_800, $image_350, $wiki_link, $khan_link, $question_id_from_table, $canonical, $full_audio, $title_tag, $description_front, $canonical_all_subject);
    // dont fetch and close yet

    // also get attachment and correct from answer from question id, make stmt_1 for later execute
    $sql = "SELECT text, attachment, correct FROM answer WHERE question_id = ?";
    $stmt_1 = $db->prepare($sql);
    $stmt_1->bind_param("s", $question_id);
} else {

    // get all data from questions from test id. an array of '20' objects with all question data
    // loop through this and return only the array with that matches the $question_id
    // result: $correct_keyword_array
    // get questions info from question table on test id
    $sql = "
SELECT question_title, attach_image, attach_audio, show_button, correct_note, question_answer, image_800, image_350, wiki_link, khan_link, question_id, canonical, full_audio, title_tag, description_front, canonical_all_subject
FROM question WHERE test_id = ?
GROUP BY question.question_answer
ORDER BY question.question_answer ASC
";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $test_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($question_title, $attach_image, $attach_audio, $show_button, $correct_note, $question_answer, $image_800, $image_350, $wiki_link, $khan_link, $question_id_from_table, $canonical, $full_audio, $title_tag, $description_front, $canonical_all_subject);
    // dont fetch and close yet

    // also get attachment and correct from answer from question id, make stmt_1 for later execute
    $sql = "SELECT text, attachment, correct FROM answer WHERE question_id = ?";
    $stmt_1 = $db->prepare($sql);
    $stmt_1->bind_param("s", $question_id);
}


// check
if ($test_type == "multiple") {
    // php array
    $question_results = array();
    $question_i = 0;

    // for every item found in questions, loop through, generating an array of objects with all data per test id, found in answers,
    // so for example an array with 20 items, so this is the raw data
    while ($stmt->fetch()) {
        $question_results[$question_i]['question_title'] = $question_title;
        $question_results[$question_i]['attach_image'] = $attach_image;
        $question_results[$question_i]['attach_audio'] = $attach_audio;
        $question_results[$question_i]['show_button'] = $show_button;
        $question_results[$question_i]['correct_note'] = $correct_note;
        $question_results[$question_i]['question_answer'] = $question_answer;
        $question_results[$question_i]['image_800'] = $image_800;
        $question_results[$question_i]['image_350'] = $image_350;
        $question_results[$question_i]['wiki_link'] = $wiki_link;
        $question_results[$question_i]['khan_link'] = $khan_link;
        $question_results[$question_i]['question_id'] = $question_id_from_table;
        $question_results[$question_i]['canonical'] = $canonical;
        $question_results[$question_i]['full_audio'] = $full_audio;
        $question_results[$question_i]['question_answer'] = $question_answer;
        $question_results[$question_i]['title_tag'] = $title_tag;
        $question_results[$question_i]['description_front'] = $description_front;
        $question_results[$question_i]['canonical_all_subject'] = $canonical_all_subject;
        $question_i++;
    }
}

// array of objects
$correct_keyword_array = array();
// loop through the $question_results array, on every index there is an object, so put object into $question_results_array, and check the value at
// ['question_id'] for a match from $question_id from get
foreach ($question_results as $question_results_i => $question_results_object) {

    // put the page specific data into an array
    if ($question_results_object['question_id'] == $question_id) {

        $correct_keyword_array['question_title'] = $question_results_object['question_title'];
        $correct_keyword_array['attach_image'] = $question_results_object['attach_image'];
        $correct_keyword_array['attach_audio'] = $question_results_object['attach_audio'];
        $correct_keyword_array['show_button'] = $question_results_object['show_button'];
        $correct_keyword_array['correct_note'] = $question_results_object['correct_note'];
        $correct_keyword_array['question_answer'] = $question_results_object['question_answer'];
        $correct_keyword_array['image_800'] = $question_results_object['image_800'];
        $correct_keyword_array['image_350'] = $question_results_object['image_350'];
        $correct_keyword_array['wiki_link'] = $question_results_object['wiki_link'];
        $correct_keyword_array['khan_link'] = $question_results_object['khan_link'];
        $correct_keyword_array['question_id_from_table'] = $question_results_object['question_id'];
        $correct_keyword_array['canonical'] = $question_results_object['canonical'];
        $correct_keyword_array['full_audio'] = $question_results_object['full_audio'];
        $correct_keyword_array['title_tag'] = $question_results_object['title_tag'];
        $correct_keyword_array['description_front'] = $question_results_object['description_front'];
        $correct_keyword_array['canonical_all_subject'] = $question_results_object['canonical_all_subject'];

        // the increment number in the array where the result was found, this is used below to see if there is a question after (button) or before (button) or nothing
        // based on the $question_results length
        $correct_keyword_array['correct_answer_key'] = $question_results_i;
    }
}

// get the length of how many items were returned from test id, from questions, check to make sure that the next question or previous question are not outside these bounds
$question_results_length = count($question_results);

// this error check is needed when adding what is canonical in the DB, if there are multiple of the same keyword in the subject, you need to pick as canonical the lowest one alphabetically, or this error will show
if (!isset($correct_keyword_array['correct_answer_key'])) {
    exit('When making the canonical in the database and matching it to the canonical_all_subject and there are multiple of the same keyterm, make sure its alphabetically the lowest. Or the canonical does not match the canonical_all_subject. Either way this question_id is not in the pulled array (so it can not display anything). instead this error is shown');
}

$index_in_array = $correct_keyword_array['correct_answer_key'];

// if the index in array +1 is not longer than the length, fill in the next keyword
if (($index_in_array + 1) < $question_results_length) {

    $index_in_array++;

    $next_question_keyword = $question_results[$index_in_array]['question_answer'];

    $next_question_keyword_question_id = $question_results[$index_in_array]['question_id'];
} else {

    $next_question_keyword = null;
}

// reset index in array
$index_in_array = $correct_keyword_array['correct_answer_key'];

// if -1 is less than 0 (bottom of array) then set to null, if not fill in
if (($index_in_array - 1) >= 0) {

    $index_in_array--;

    $previous_question_keyword = $question_results[$index_in_array]['question_answer'];

    $previous_question_keyword_question_id  = $question_results[$index_in_array]['question_id'];
} else {

    $previous_question_keyword = null;
}

// Set Correct Answer
if (!empty($correct_keyword_array['question_answer'])) {
    $correctAnswerName = ($correct_keyword_array['question_answer']);
}

// Set Question Title
if (!empty($correct_keyword_array['question_title'])) {
    $questionTitle = ucfirst($correct_keyword_array['question_title']);
}

// Set Title Fronts. Make an exception for the front of all titles from US History.
if (!empty($subject_extra_name) && ($subject_extra_name == 'US History')) {

    if (!empty($correct_keyword_array['description_front'])) {
        $descriptionFront = ucfirst($correct_keyword_array['description_front']);
    } else {
        $descriptionFront = 'What was the';
    }
} else {

    if (!empty($correct_keyword_array['description_front'])) {
        $descriptionFront = ucfirst($correct_keyword_array['description_front']);
    } else {
        $descriptionFront = 'What is a';
    }
}

// make PNG and WEBP paths
if (!empty($correct_keyword_array['image_800']) && !empty($correct_keyword_array['image_350'])) {

    $png_800_file_path = $correct_keyword_array['image_800'];
    $webp_800_file_path = str_replace("png", "webp", $png_800_file_path);
    $webp_800_file_path = str_replace("800", "800-webp", $webp_800_file_path);


    $png_375_file_path = $correct_keyword_array['image_350'];
    $webp_375_file_path = str_replace("png", "webp", $png_375_file_path);
    $webp_375_file_path = str_replace("375", "375-webp", $webp_375_file_path);
} elseif (!empty($gs_800_default)) {

    $png_800_file_path = $gs_800_default;
    $webp_800_file_path = $gs_800_default;
}

////////////////// BEFORE ANYTHING REDIRECT TO NEW URL STRUCTURE //////////////////

// orginal
// definitions?definition=4591&bacteria
if (!empty($correct_keyword_array['canonical'])) {

    // make canonical. if an all_subject is inluded, then use all subject link
    if (!empty($correct_keyword_array['canonical_all_subject'])) {

        $canonical = 'https://' . $_SERVER['HTTP_HOST'] . '/definitions/' . $correct_keyword_array['canonical'] . '/' . spacestoDashes($correct_keyword_array['question_answer']);
    }    
}

// if uses either of these temporary URL structure experiements, redirect back to normal:
//definitions?d=4591&v=bacteria
//definitions?d=4591&a=on&v=bacteria

// used for new url structure experiement
// $new_url_used = 'https://' . $_SERVER['HTTP_HOST'] . '/definitions?definition=' . $question_id . '&' . spacestoDashes($correct_keyword_array['question_answer']);

if (!empty($correctAnswerName)) :
    $title = ucwords($correctAnswerName) . ' Definition and Image | Flippy';
endif;
if (!empty($correctAnswerName) && !empty($questionTitle) && !empty($test_name)) :
    $description = ucwords($correctAnswerName) . ' Definition: ' . ucfirst($questionTitle) . '.';
endif;

?>

@section('title', $title)
@section('description', $description)
@section('canonical', $canonical)

@push('head')

<?php if (!empty($canonical)) : ?>
    <script type="text/javascript">
        let canonicalURL = '<?php echo $canonical; ?>';
    </script>
<?php endif; ?>

<?php if (!empty($png_800_file_path)) : ?>
    <script type="text/javascript">
        let png_800_file_path = '<?php echo $png_800_file_path; ?>';
    </script>
<?php endif; ?>
<?php if (!empty($new_url_used)) : ?>
    <script type="text/javascript">
        let new_URL = '<?php echo $new_url_used; ?>';
        let new_URL_used_without_answer = '<?php echo $new_url_used . '&a=off'; ?>';
    </script>
<?php endif; ?>
<link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" />
<link href="https://fonts.googleapis.com/css?family=Raleway:Regular 400,Black 900" rel="stylesheet">
@endpush

@push('foot')
<script src="{{ asset('assets/js/page/definitions.js') }}"></script>
<script type="application/ld+json">
    {
        "@context": "http://schema.org/",
        "@type": "CreativeWork",
        "name": "<?php echo ucfirst($correctAnswerName); ?> image",
        "description": "<?php echo ucfirst($correctAnswerName); ?> image",
        "learningResourceType": [
            "learning activity"
        ],
        "audience": {
            "@type": "EducationalAudience",
            "educationalRole": "student"
        },
        "educationalAlignment": {
            "@type": "AlignmentObject",
            "alignmentType": "educationalLevel",
            "educationalFramework": "US Grade Levels",
            "targetName": "Pre-K to 12",
            "targetUrl": {
                "@id": "http://purl.org/ASN/scheme/ASNEducationLevel/PreKto12"
            }
        },
        "url": "<?php if (!isset($canonical)) echo $canonical; ?>"
    }
</script>
@endpush

@section('content')
<div class="container-fluid text-center" style="background-color: #fff;">


    <div class="row" style="display: initial; margin: 0;">


        <div style="display: flex; justify-content: center; font-size: 20px; font-family: 'Raleway', sans-serif; padding-top: 30px;">
            <div>
                <div style="display: flex; justify-content: center; font-size: 20px; font-family: 'Raleway', sans-serif; padding-top: 20px; ">
                    <div>
                        <span style="text-decoration: underline;">                                   
                            <a style="color: #000;" href="/grade/{{ $subject_id }}/{{ spacestoDashes($subject_extra_name) }}">{{ ucwords($subject_extra_name)}}</a>
                        </span> / <span style="text-decoration: underline;">
                            <a style="color: #000;" href="/chapter/{{ $test_id }}/{{spacestoDashes(testNameAfterColon($test_name))}}">{{testNameAfterColon($test_name)}}</a></span> /
                        <span style="color: #000">{{ ucwords($correctAnswerName) }}</span>
                    </div>
                </div>    
            </div>
        </div>


        

        <div class="definitions-container text-center" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding-top: 5px; padding-bottom: 45px;">

            <?php $webp_800_file_path_no_text = str_replace("800", "800-no-text", $webp_800_file_path); ?>

            <div class="definitions-image-min-height" style="display: flex; align-items: center; justify-content: space-between; padding: 0; margin-top: 7px;">
                
                <?php if (!empty($webp_800_file_path) && !empty($png_800_file_path) && !empty($correctAnswerName) && !empty($questionTitle) && !empty($descriptionFront) && !empty($correct_keyword_array['question_answer'])) : ?>

                    <div class="definitions-contain-max-width" style="width: 100%; display: flex; justify-content: center;">

                        <div class="definitions-image-min-height chapter-flip-card" style="width: 100%; ">

                            <?php // if flip var is set, then show flashcard already flipped or not 
                            ?>
                            <?php if (!empty($answer_shown_flag_url)) : ?>

                                <?php if ($answer_shown_flag_url == 'off') : ?>

                                    <div class="chapter-flip-card-inner chapter-transform-rotate">

                                    <?php else : ?>

                                        <div class="chapter-flip-card-inner">

                                        <?php endif; ?>

                                    <?php else : ?>

                                        <div class="chapter-flip-card-inner">

                                        <?php endif; ?>

                                        <?php $webp_800_file_path_no_text = str_replace("800", "800-no-text", $webp_800_file_path); ?>
                                        <?php $png_800_file_path_no_text = str_replace("800", "800-no-text", $png_800_file_path); ?>
                                        <?php $webp_375_file_path_no_text = str_replace("375", "375-no-text", $webp_375_file_path); ?>
                                        <?php $png_375_file_path_no_text = str_replace("375", "375-no-text", $png_375_file_path); ?>

                                        <?php if (!empty($png_800_file_path) && file_exists($png_800_file_path)) : ?>

                                            <div class="" style="width: 100%; margin-top: 20px; ">

                                                <picture>

                                                    <source media="(max-width: 420px)" srcset="/<?php echo  $webp_375_file_path . ' 375w '; ?>" type="image/webp" sizes="90vw">
                                                    <source srcset="/<?php echo  $webp_800_file_path . ' 800w '; ?>" type="image/webp" sizes="90vw">

                                                    <source media="(max-width: 420px)" srcset="/<?php echo  $png_375_file_path . ' 375w '; ?>" type="image/png" sizes="90vw">
                                                    <source srcset="/<?php echo $png_800_file_path . ' 800w '; ?>" type="image/png" sizes="90vw">

                                                    <img style="max-height: 70vh; max-width: 100%; height: auto; border-radius: 10px; box-shadow: 0px 0px 0px 7px #41464b; background-color: #41464b;" src="/<?php echo $png_800_file_path ?>" width="800" height="600" title="<?php echo  ucwords($correctAnswerName) . ' Definition'; ?>" alt="<?php echo  ucwords($correctAnswerName) . ' Definition'; ?>">

                                                </picture>
                                            </div>

                                            <div class="" style="width: 100%; margin-top: 20px; ">

                                                <picture>

                                                    <source media="(max-width: 420px)" srcset="/<?php echo  $webp_375_file_path_no_text . ' 375w '; ?>" type="image/webp" sizes="90vw">
                                                    <source srcset="/<?php echo $webp_800_file_path_no_text . ' 800w '; ?>" type="image/webp" sizes="90vw">

                                                    <source media="(max-width: 420px)" srcset="/<?php echo  $png_375_file_path_no_text . ' 375w '; ?>" type="image/png" sizes="90vw">
                                                    <source srcset="/<?php echo $png_800_file_path_no_text . ' 800w '; ?>" type="image/png" sizes="90vw">

                                                    <img style="max-height: 70vh; max-width: 100%; height: auto; border-radius: 10px; box-shadow: 0px 0px 0px 7px #00acc1; background-color: #41464b;" src="/<?php echo $png_800_file_path_no_text ?>" width="800" height="600" title="Back of Flashcard" alt="Back of Flashcard">

                                                </picture>
                                            </div>

                                        <?php else : ?>

                                            <img style="width: 100%; border-radius: 10px;" src="/<?php echo $gs_800_default; ?>" title="<?php echo  $png_800_file_path . ' Not Found'; ?>">

                                        <?php endif; ?>

                                        </div>
                                        </div>
                                    </div>

                                <?php endif; ?>                                

                        </div>                       

                        <div style="display: flex; justify-content: center; align-items: center;">
                            <div style="text-align: center; margin-top: 25px; font-family: 'Raleway', sans-serif; font-size: 1.2rem !important; color: #fff; background-color: #41464b; padding: 15px 20px; border-radius: 5px;"><?php echo ucwords($correctAnswerName) . ' Definition: ' . ucfirst($questionTitle) . '.'; ?></div>
                        </div>  

                    </div>

            </div>

        </div>

        <div style="background-color: #293036; display: flex; flex-direction: column; align-items: center;">


            <div style="padding-top: 50px;">

                <div style="display: flex; justify-content: center; font-family: 'Raleway', sans-serif; padding: 20px;">
                    <div>
                        <div style="display: flex; justify-content: center; font-size: 20px; font-family: 'Raleway', sans-serif; padding-top: 20px;">
                            <div style="color: #fff;">
                                <span style="text-decoration: underline;">                                   
                                    <a style="color: #fff;" href="/grade/{{ $subject_id }}/{{ spacestoDashes($subject_extra_name) }}">{{ ucwords($subject_extra_name)}}</a>
                                </span> / <span style="text-decoration: underline;">
                                    <a style="color: #fff;" href="/chapter/{{ $test_id }}/{{spacestoDashes(testNameAfterColon($test_name))}}">{{testNameAfterColon($test_name)}}</a></span> /
                                <span style="color: #fff;">{{ ucwords($correctAnswerName) }}</span>
                            </div>
                        </div>

                    </div>
                </div> 

                <div style="padding: 20px; padding-top: 35px; color: #afafaf; font-family: 'Raleway', sans-serif;">You are welcome to use these flashcard images on <?php echo ucwords($correctAnswerName); ?> in any educational context, including in class, for schoolwork, or in academic articles, with no restrictions.</div>

                <div style="display: flex; justify-content: center; align-items: center; padding-top: 30px;">
                    <div style="text-align: center; font-family: 'Raleway', sans-serif;  background-color: #41464b; padding: 10px 20px; border-radius: 5px;">

                        <div style="padding: 15px;">
                            <a style="font-size: 20px; color: #fff;" href="/" onclick="track_video_click('How to Study While Playing Video Games - Definition')">
                                <div style="background-color: #36a981; padding: 10px 15px; border-radius: 5px;;">
                                    What is Flippy?
                                </div>
                            </a>
                        </div>

                    </div>
                </div>

                <?php if ( !empty($correct_keyword_array['wiki_link']) && !empty($correctAnswerName) ) : ?>

                    <div style="text-align: center; padding-top: 20px; font-family: 'Raleway', sans-serif; font-size: 16px;"><span class=""><a class="grey-with-orange-hover" href="<?php echo $correct_keyword_array['wiki_link']; ?>">Learn more: <?php echo ucwords($correctAnswerName) ; ?> on Wikipedia.</a></span></div>

                <?php endif; ?>

                <?php if ( !empty($correct_keyword_array['khan_link']) && !empty($correctAnswerName) ) : ?>

                    <div style="text-align: center; padding-top: 20px;  font-family: 'Raleway', sans-serif; font-size: 16px;"><span><a class="grey-with-orange-hover" href="<?php echo $correct_keyword_array['khan_link']; ?>">Learn more: <?php echo ucwords($correctAnswerName); ?> on Khan Academy.</a></span></div>

                <?php endif; ?>


            </div>

            <div style="width: 100%; max-width: 935px; padding-top: 30px; padding-bottom: 20px; display: flex; justify-content: space-around; flex-wrap: wrap;">
                <div style="padding: 5px; display: flex; justify-content: center; align-items: center;">
                    <div>
                        <a href="https://www.pinterest.com/gamesmartz/">
                            <img src="/assets/images/pinterest-stem.png" alt="Pinterest">
                        </a>
                    </div>
                </div>
                    <div style="padding: 5px; display: flex; justify-content: center; align-items: center;">
                        <div>
                            <a href="https://www.google.com/search?q=GameSmartz Definition&tbm=isch">
                                <img src="/assets/images/google-images-stem.png" alt="Goolge Images">
                            </a>
                        </div>
                    </div>
                    <div style="padding: 5px; display: flex; justify-content: center; align-items: center;">
                        <div>
                            <a href="https://seedfund.nsf.gov/topics/learning-cognition-technologies/">
                                <img src="/assets/images/nsf-consideration.png" alt="National Science Foundation Consideration">
                            </a>
                        </div>
                    </div>
            </div>

            <div style="padding: 10px; color: #a0a0a0; font-size: 13px; font-family: 'Raleway', sans-serif;">
                For school sales contact: matt@flippy-ai.com
            </div>

        </div>
        @endsection
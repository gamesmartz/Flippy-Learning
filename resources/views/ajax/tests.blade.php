<?php

$result = "success";
$data = array();


$type = trimAndClean($_POST['type']);

//dd($action);

//
// most of these blocks are similar, with different inputs, and are very verbose because of the prepared statements
// this is why they are seperated by spaces
//


if ($action == 'add') {


        // if type multiple (only type we are using)
        if ($type == "multiple") {
            /* add multiple questions to one test with inserting one test */
            $test_id = trimAndClean($_POST['test_id']);
            $subject_id = trimAndClean($_POST['subject_id']);
            $test_name = trimAndClean($_POST['test_name']);
            $mastery_type = trimAndClean($_POST['mastery_type']);
            $mastery_number = trimAndClean($_POST['mastery_number']);
            $award_time = trimAndClean($_POST['award_time']);
            // arrays cant be cleaned this way, but since this is a single, its OK -MN
            $form_options['font'] = trimAndClean($_POST['form_options'][0]);
            $form_options['width'] = trimAndClean($_POST['form_options'][1]);
            $form_options = serialize($form_options);

            $optional_note = trimAndClean($_POST['optional_note']);

            // added default setting to 1 (non public) - MN 1-12-18
            $default_non_public = 1;

            $sql = "SELECT * FROM test WHERE test_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $test_id);
            $stmt->execute();
            $stmt->store_result();
            $row = $stmt->num_rows();
            $stmt->free_result();
            $stmt->close();

            /* update/insert test info to test table with posted data */
            if ($row != 0) {
                $sql = "UPDATE test SET subject_id = ?, test_name = ?, mastery_type = ?, mastery_number = ?, award_time = ?, form_options = ?, optional_note = ? WHERE test_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("ssssssss", $subject_id, $test_name, $mastery_type, $mastery_number, $award_time, $form_options, $optional_note, $test_id);
                $stmt->execute();
                $stmt->close();
            } else {
                $sql = "INSERT INTO test (subject_id, test_name, test_type, mastery_type, mastery_number, award_time, form_options, optional_note, author_id, public, created_time) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now() )";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("ssssssssss", $subject_id, $test_name, $type, $mastery_type, $mastery_number, $award_time, $form_options, $optional_note, $user_id, $default_non_public);
                $user_id = $loggedUser->user_id;
                $stmt->execute();
                $test_id = $stmt->insert_id;
                $stmt->close();
            }
            $data['test_id'] = $test_id;

            $question_id = trimAndClean($_POST['question_id']);
            $originQuestionId = $question_id;
            $question_title = trimAndClean($_POST['question_title']);

            // cant clean arrays in this way -MN
            $attach_question = $_POST['attach_question'];
            // cant clean arrays in this way -MN
            $show_button = $_POST['show_button'];

            $answer_text = array();
            $correct = array();
            $attach_graphic = array();

            $answer_text[0] = trimAndClean($_POST['answer_text_1']);
            $correct[0] = trimAndClean($_POST['correct_1']);
            $attach_graphic[0] = trimAndClean($_POST['attach_graphic_1']);

            /*
            $answer_text[1] = trimAndClean($_POST['answer_text_2']);
            $correct[1] = trimAndClean($_POST['correct_2']);
            $attach_graphic[1] = trimAndClean($_POST['attach_graphic_2']);
            $answer_text[2] = trimAndClean($_POST['answer_text_3']);
            $correct[2] = trimAndClean($_POST['correct_3']);
            $attach_graphic[2] = trimAndClean($_POST['attach_graphic_3']);
            $answer_text[3] = trimAndClean($_POST['answer_text_4']);
            $correct[3] = trimAndClean($_POST['correct_4']);
            $attach_graphic[3] = trimAndClean($_POST['attach_graphic_4']);
            $answer_text[4] = trimAndClean($_POST['answer_text_5']);
            $correct[4] = trimAndClean($_POST['correct_5']);
            $attach_graphic[4] = trimAndClean($_POST['attach_graphic_5']);
            */

            if (isset($_POST['correct_answer_notes'])) {
                $correct_note = trimAndClean($_POST['correct_answer_notes']);
            } else {
                $correct_note = "";
            }

            /**
             * start copy attach files to actual directory
             * first make directory with test id if not exists and copy uploaded images from temp sub directory(under test_id) to parent directory
             * then this image will be stored exactly in test id directory
             * same as audio file
             */
            $question_number = trimAndClean($_POST['question_number']);
            $test_path = "../public/upload/img/" . $test_id . "/";
            if ($attach_question[0] != "") {
                if (!file_exists($test_path)) {
                    mkdir($test_path, 0777, true);
                    chmod($test_path, 0777);
                }
                if (strpos($attach_question[0], "upload/img/temp/") !== false) {
                    $actual_image_name = str_replace("upload/img/temp/", "", $attach_question[0]);
                    if (copy("../" . $attach_question[0], $test_path . $actual_image_name)) {
                        unlink("../" . $attach_question[0]);
                        $attach_question[0] = "upload/img/" . $test_id . "/" . $actual_image_name;
                    }
                }
            }
            /* only loop 5 times, cause limit as 5 attach images*/
            // There was originally 5 inputs that had to be looped through, now there is just 1, so changed the loop to 1
            for ($i = 0; $i < 1; $i++) {
                if ( !empty($attach_graphic[$i])) {
                    if (!file_exists($test_path)) {
                        mkdir($test_path, 0777, true);
                        chmod($test_path, 0777);
                    }
                    if (strpos($attach_graphic[$i], "upload/img/temp/") !== false) {
                        $actual_image_name = str_replace("upload/img/temp/", "", $attach_graphic[$i]);
                        if (copy("../" . $attach_graphic[$i], $test_path . $actual_image_name)) {
                            unlink("../" . $attach_graphic[$i]);
                            $attach_graphic[$i] = "upload/img/" . $test_id . "/" . $actual_image_name;
                        }
                    }
                }
            }

            /* this part is for audio */
            $test_path = "../public/upload/audio/" . $test_id . "/";
            if ($attach_question[1] != "") {
                if (!file_exists($test_path)) {
                    mkdir($test_path, 0777, true);
                    chmod($test_path, 0777);
                }
                if (strpos($attach_question[1], "upload/audio/temp/") !== false) {
                    $actual_name = str_replace("upload/audio/temp/", "", $attach_question[1]);
                    if (copy("../" . $attach_question[1], $test_path . $actual_name)) {
                        unlink("../" . $attach_question[1]);
                        $attach_question[1] = "upload/audio/" . $test_id . "/" . $actual_name;
                    }
                }
            }
            if ($correct_note != "") {
                if (!file_exists($test_path)) {
                    mkdir($test_path, 0777, true);
                    chmod($test_path, 0777);
                }
                if (strpos($correct_note, "upload/audio/temp/") !== false) {
                    $actual_name = str_replace("upload/audio/temp/", "", $correct_note);
                    if (copy("../" . $correct_note, $test_path . $actual_name)) {
                        unlink("../" . $correct_note);
                        $correct_note = "upload/audio/" . $test_id . "/" . $actual_name;
                    }
                }
            }
            /* attached image/audio copy is end */

            /* update/insert question info to question table  */
            $sql = "SELECT * FROM question WHERE question_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $question_id);
            $stmt->execute();
            $stmt->store_result();
            $row = $stmt->num_rows();
            $stmt->free_result();
            $stmt->close();

            // get subject name to use for static paths
            $subject_name = null;
            $sql = "SELECT subject_name FROM subject WHERE subject_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $subject_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($subject_name);
            $stmt->fetch();
            $stmt->free_result();
            $stmt->close();

            if (!empty($subject_name)) :
                $subject_name = strtolower($subject_name);
            endif;

            // create static variables to be put in on each new question creation

            $answer_test_insert = spacestoDashes(strtolower($answer_text[0]));

            $attach_image_insert = "upload/subjects/" . $subject_name . "/375-no-text/" . $answer_test_insert . ".png";            
            $correct_note_insert = "upload/subjects/" . $subject_name . "/audio/" . $answer_test_insert . ".mp3";
            $image_800_insert = "upload/subjects/" . $subject_name . "/800/" . $answer_test_insert . ".png";
            $image_350_insert = "upload/subjects/" . $subject_name . "/375/" . $answer_test_insert . ".png";
            $full_audio_insert = "upload/subjects/" . $subject_name . "/audio-question/" . $answer_test_insert . ".mp3";

            if ($row != 0) {

                $attach_question[0] = $attach_image_insert;
                $correct_note = $correct_note_insert;

                $sql = "UPDATE question SET question_title = ?, attach_image = ?, attach_audio = ?, show_image = ?, show_button = ?, correct_note = ?, test_id = ?, question_answer = ?, image_800 = ?, image_350 = ?, full_audio = ?  WHERE question_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("ssssssssssss", $question_title, $attach_question[0], $attach_question[1], $show_button[0], $show_button[1], $correct_note, $test_id, $answer_text[0], $image_800_insert, $image_350_insert, $full_audio_insert, $question_id);
                $stmt->execute();
                $stmt->close();
            } else { 
                $show_image_insert = 1;
                $sql = "INSERT INTO question (question_title, attach_audio, show_button, test_id, question_answer, attach_image, show_image, correct_note, image_800, image_350, full_audio) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("sssssssssss", $question_title, $attach_question[1], $show_button[1], $test_id, $answer_text[0], $attach_image_insert, $show_image_insert, $correct_note_insert, $image_800_insert, $image_350_insert, $full_audio_insert);
                $stmt->execute();
                $question_id = $stmt->insert_id;
                $stmt->close();
            }

            /**
             * insert answers to answers table with question id
             * get all answers by question id from answers table and
             * update answer table if input data exists and if not, delete, by primary id (answer id )
             * then insert answer data to answers table by question id for new answer
             */
            $sql = "SELECT id FROM answer WHERE question_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $question_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($answer_id);

            $i = 0;

            $sql = "UPDATE answer SET text = ?, attachment = ?, correct = ? WHERE id = ?";
            $stmt_1 = $db->prepare($sql);
            $stmt_1->bind_param("ssss", $tmp_text, $tmp_attach, $tmp_correct, $answer_id);

            $sql = "DELETE FROM answer WHERE id = ?";
            $stmt_2 = $db->prepare($sql);
            $stmt_2->bind_param("s", $answer_id);

            while ($stmt->fetch()) {
                if ($answer_text[$i] != "" || $attach_graphic[$i] != "") {
                    $tmp_text = $answer_text[$i];
                    $tmp_attach = $attach_graphic[$i];
                    $tmp_correct = $correct[$i];
                    $stmt_1->execute();
                } else {
                    $stmt_2->execute();
                }
                $i++;
            }
            $stmt->free_result();
            $stmt->close();

            $stmt_1->close();
            $stmt_2->close();

            $sql = "INSERT INTO answer (question_id, text, attachment, correct) VALUES(?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ssss", $question_id, $tmp_text, $tmp_attach, $tmp_correct);

            // There was originally 5 inputs that had to be looped through, now there is just 1, so changed the loop to 1
            for ($j = $i; $j < 1; $j++) {
                if ( !empty($answer_text[$j])  || !empty($attach_graphic[$j])) {
                    $tmp_text = $answer_text[$j];
                    $tmp_attach = $attach_graphic[$j];
                    $tmp_correct = $correct[$j];
                    $stmt->execute();
                }
            }
            $stmt->close();

            /* get question and answer by question id for response data */
            $sql = "SELECT * FROM question WHERE test_id = '$test_id' ORDER BY question_id";
            $question = $db->queryArray($sql);

            $sql = "SELECT text, attachment, correct FROM answer WHERE question_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $question_id);

            for ($i = 0; $i < count($question); $i++) {

                $question_id = $question[$i]['question_id'];
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($text, $answer_attach, $correct);
                $j = 0;
                $answer_array = array();
                while ($stmt->fetch()) {
                    $answer_array[$j]['text'] = $text;
                    $answer_array[$j]['attach'] = $answer_attach;
                    $answer_array[$j]['correct'] = $correct;
                    $j++;
                }
                $stmt->free_result();

                for ($k = $j; $k < 5; $k++) {
                    $answer_array[$k]['text'] = "";
                    $answer_array[$k]['attach'] = "";
                    $answer_array[$k]['correct'] = 0;
                }
                $question[$i]['answer'] = $answer_array;

            }

            $stmt->close();

            if (empty($originQuestionId)) {
                $sql = "SELECT * FROM question WHERE test_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("s", $test_id);
                $stmt->execute();
                $stmt->store_result();
                $count = $stmt->num_rows;
                $stmt->free_result();
                $stmt->close();

                $sql1 = "SELECT id, question_history FROM user_test WHERE test_id = ?";
                $stmt = $db->prepare($sql1);
                $stmt->bind_param("s", $test_id);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($id, $question_history);
                $qQueCount = $stmt->num_rows;
                if ($qQueCount > 0) {
                    while ($stmt->fetch()) {
                        $question_history = unserialize($question_history);
                        $question_history[($count - 1)][] = '';
                        $question_history[($count - 1)][] = '';
                        $question_history = serialize($question_history);
                        $sql_1 = "UPDATE user_test SET question_history = '" . $question_history . "' WHERE id = " . $id;
                        $stmt_1 = $db->prepare($sql_1);
                        $stmt_1->execute();
                        $stmt_1->close();
                    }
                }
                $stmt->free_result();
                $stmt->close();
            }

            $data['question'] = $question;
    }


//    not using fill in the blank. commenting out for clarity

//    elseif ($type == "fill") {
//
//        $test_id = trimAndClean($_POST['test_id']);
//        $subject_id = trimAndClean($_POST['subject_id']);
//        $test_name = trimAndClean($_POST['test_name']);
//        $mastery_type = trimAndClean($_POST['mastery_type']);
//        $mastery_number = trimAndClean($_POST['mastery_number']);
//        $award_time = trimAndClean($_POST['award_time']);
//        $form_options['font'] = trimAndClean($_POST['form_options'][0]);
//        $form_options['width'] = trimAndClean($_POST['form_options'][1]);
//        $form_options = serialize($form_options);
//
//        /* get record from test by primary id(test id) if this record count is 0 then insert, not update */
//        $optional_note = trimAndClean($_POST['optional_note']);
//        $sql = "SELECT * FROM test WHERE test_id = ?";
//        $stmt = $db->prepare($sql);
//        $stmt->bind_param("s", $test_id);
//        $stmt->execute();
//        $stmt->store_result();
//        $row = $stmt->num_rows();
//        $stmt->free_result();
//        $stmt->close();
//
//        if ($row != 0) {
//            $sql = "UPDATE test SET subject_id = ?, test_name = ?, mastery_type = ?, mastery_number = ?, award_time = ?, form_options = ?, optional_note = ? WHERE test_id = ?";
//            $stmt = $db->prepare($sql);
//            $stmt->bind_param("ssssssss", $subject_id, $test_name, $mastery_type, $mastery_number, $award_time, $form_options, $optional_note, $test_id);
//            $stmt->execute();
//            $stmt->close();
//        } else {
//            $sql = "INSERT INTO test (subject_id, test_name, test_type, mastery_type, mastery_number, award_time, form_options, optional_note, author_id, created_time) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, now() )";
//            $stmt = $db->prepare($sql);
//            $stmt->bind_param("sssssssss", $subject_id, $test_name, $type, $mastery_type, $mastery_number, $award_time, $form_options, $optional_note, $user_id);
//            $user_id = $loggedUser->user_id;
//            $stmt->execute();
//            $test_id = $stmt->insert_id;
//            $stmt->close();
//        }
//        $data['test_id'] = $test_id;
//
//        $question_number = trimAndClean($_POST['question_number']);
//        $question_id = trimAndClean($_POST['question_id']);
//        $question_title = trimAndClean($_POST['question_title']);
//        $attach_question = trimAndClean($_POST['attach_question']);
//        $show_button = trimAndClean($_POST['show_button']);
//        $answer = trimAndClean($_POST['answer']);
//        if (isset($_POST['correct_answer_notes'])) {
//            $correct_note = trimAndClean($_POST['correct_answer_notes']);
//        } else {
//            $correct_note = "";
//        }
//
//        /* uploaded test audio file from temp to test id directory */
//
//        $test_path = "../upload/audio/" . $test_id . "/";
//        if ($correct_note != "") {
//            if (!file_exists($test_path)) {
//                mkdir($test_path, 0777, true);
//                chmod($test_path, 0777);
//            }
//            if (strpos($correct_note, "upload/audio/temp/") !== false) {
//                $actual_name = str_replace("upload/audio/temp/", "", $correct_note);
//                if (copy("../" . $correct_note, $test_path . $actual_name)) {
//                    unlink("../" . $correct_note);
//                    $correct_note = "upload/audio/" . $test_id . "/" . $actual_name;
//                }
//            }
//        }
//        if ($attach_question[1] != "") {
//            if (!file_exists($test_path)) {
//                mkdir($test_path, 0777, true);
//                chmod($test_path, 0777);
//            }
//            if (strpos($attach_question[1], "upload/audio/temp/") !== false) {
//                $actual_name = str_replace("upload/audio/temp/", "", $attach_question[1]);
//                if (copy("../" . $attach_question[1], $test_path . $actual_name)) {
//                    unlink("../" . $attach_question[1]);
//                    $attach_question[1] = "upload/audio/" . $test_id . "/" . $actual_name;
//                }
//            }
//        }
//
//        /* uploaded test image file from temp to test id directory */
//        $test_path = "../upload/img/" . $test_id . "/";
//        if ($attach_question[0] != "") {
//            if (!file_exists($test_path)) {
//                mkdir($test_path, 0777, true);
//                chmod($test_path, 0777);
//            }
//            if (strpos($attach_question[0], "upload/img/temp/") !== false) {
//                $actual_image_name = str_replace("upload/img/temp/", "", $attach_question[0]);
//                if (copy("../" . $attach_question[0], $test_path . $actual_image_name)) {
//                    unlink("../" . $attach_question[0]);
//                    $attach_question[0] = "upload/img/" . $test_id . "/" . $actual_image_name;
//                }
//            }
//        }
//
//        /* insert or update question table by question id(primary id) like above test table */
//        $sql = "SELECT * FROM question WHERE question_id = ?";
//        $stmt = $db->prepare($sql);
//        $stmt->bind_param("s", $question_id);
//        $stmt->execute();
//        $stmt->store_result();
//        $row = $stmt->num_rows();
//        $stmt->free_result();
//        $stmt->close();
//
//        if ($row != 0) {
//            $sql = "UPDATE question SET question_title = ?, attach_image = ?, attach_audio = ?, show_image = ?, show_button = ?, correct_note = ?, test_id = ? WHERE question_id = ?";
//            $stmt = $db->prepare($sql);
//            $stmt->bind_param("ssssssss", $question_title, $attach_question[0], $attach_question[1], $show_button[0], $show_button[1], $correct_note, $test_id, $question_id);
//            $stmt->execute();
//            $stmt->close();
//        } else {
//            $sql = "INSERT INTO question (question_title, attach_image, attach_audio, show_image, show_button, correct_note, test_id) VALUES(?, ?, ?, ?, ?, ?, ?)";
//            $stmt = $db->prepare($sql);
//            $stmt->bind_param("sssssss", $question_title, $attach_question[0], $attach_question[1], $show_button[0], $show_button[1], $correct_note, $test_id);
//            $stmt->execute();
//            $question_id = $stmt->insert_id;
//            $stmt->close();
//        }
//
//        /* insert or update answer table by question id(parent id) */
//        $sql = "SELECT id FROM answer WHERE question_id = ?";
//        $stmt = $db->prepare($sql);
//        $stmt->bind_param("s", $question_id);
//        $stmt->execute();
//        $stmt->store_result();
//        $row = $stmt->num_rows();
//        $stmt->bind_result($answer_id);
//        $stmt->fetch();
//        $stmt->free_result();
//        $stmt->close();
//
//        if ($row != 0) {
//            $sql = "UPDATE answer SET text = ? WHERE id = ?";
//            $stmt = $db->prepare($sql);
//            $stmt->bind_param("ss", $answer, $answer_id);
//            $stmt->execute();
//            $stmt->close();
//        } else {
//            $sql = "INSERT INTO answer (question_id, text, correct) VALUES(?, ?, 1)";
//            $stmt = $db->prepare($sql);
//            $stmt->bind_param("ss", $question_id, $answer);
//            $stmt->execute();
//            $stmt->close();
//        }
//
//        $sql = "SELECT * FROM question WHERE test_id = '" . $test_id ." ' ORDER BY question_id";
//        $question = $db->queryArray($sql);
//
//        $sql = "SELECT text FROM answer WHERE question_id = ?";
//        $stmt = $db->prepare($sql);
//        $stmt->bind_param("s", $question_id);
//
//        /* get questions based on inserted/updated to show updated data to page */
//        for ($i = 0; $i < count($question); $i++) {
//
//            $question_id = $question[$i]['question_id'];
//            $stmt->execute();
//            $stmt->store_result();
//            $stmt->bind_result($answer);
//            $stmt->fetch();
//            $stmt->free_result();
//
//            $question[$i]['answer'] = $answer;
//
//        }
//
//        $stmt->close();
//
//        $data['question'] = $question;
//
//    }






    elseif ($type == "que") {

        // add one test to que

        $test_id = trimAndClean($_POST['test_id']);
        $user_id = $loggedUser->user_id;

        /* get test_info, in_game_mastered value from user_test table to initial this test_info column */
        $sql = "SELECT test_info, in_game_mastered FROM user_test WHERE user_id = ? AND test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $user_id, $test_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($test_info, $in_game_mastered);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        $mastered_flag = "";

        // mastered not being used
        //if($in_game_mastered == 1){

        if ($test_info == "Mastered") {
            $mastered_flag = "success";

            $sql = "UPDATE user_test SET test_info = '', completion_rate = 0, question_history = '', question_start_point = 0 WHERE user_id = ? AND test_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ss", $user_id, $test_id);
            $stmt->execute();
            $stmt->close();
        }

        //mastered not used
        /*
        $sql = "update user_test set in_game_mastered = 0 where user_id = ? and test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $user_id, $test_id);
        $stmt->execute();
        $stmt->close();

        $sql = "update users set mastered_num = 0 where user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->close();
        */
        //}

        /* update que(array) column of users since one test is added to que */
        $sql = "SELECT que FROM users WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($que);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        // if que empty, just add the test id, if not check to see if the test is already there, is so then 'failed', if not then add the test id with a merge
        $que_array = array();
        if ($que == "") {
            $que_array[0] = $test_id;
        } else {
            $que_array = unserialize($que);
            if (in_array($test_id, $que_array)) {
                $result = "failed";
            } else {
                $que_array = array_merge(array(0 => $test_id), $que_array);
            }
        }
        // update DB with new info
        $serial_que = serialize($que_array);
        $sql = "UPDATE users SET que = ? WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $serial_que, $user_id);
        $stmt->execute();
        $stmt->close();

        // add to data array
        $data['que'] = count($que_array);
        $data['mastered_flag'] = $mastered_flag;

    }










//        not using code block
//        elseif ($type == "view_video") {
//        /* update/insert view_video column as 1 to user_test by test id and user id  */
//
//        $test_id = trimAndClean($_POST['test_id']) ;
//        $user_id = $loggedUser->user_id;
//        $sql = "SELECT * FROM user_test WHERE user_id = ? AND test_id = ?";
//        $stmt = $db->prepare($sql);
//        $stmt->bind_param("ss", $user_id, $test_id);
//        $stmt->execute();
//        $stmt->store_result();
//        $row = $stmt->num_rows();
//        $stmt->free_result();
//        $stmt->close();
//
//        if ($row != 0) {
//            $sql = "UPDATE user_test SET view_video = 1 WHERE user_id = ? AND test_id = ?";
//        } else {
//            $sql = "INSERT INTO user_test (user_id, test_id, view_video) VALUES(?, ?, '1')";
//        }
//        $stmt = $db->prepare($sql);
//        $stmt->bind_param("ss", $user_id, $test_id);
//        $stmt->execute();
//        $stmt->close();
//    }









    elseif ($type == "resort") {

        $user_id = $loggedUser->user_id;
        $que_array = $_POST['sortedIDs'];

        $serial_que = serialize($que_array);

        $sql = "UPDATE users SET que = ? WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $serial_que, $user_id);
        $stmt->execute();
        $stmt->close();

    }








    elseif ($type == "complete") {
        /* insert answer and with related info since user completed one question */
        $test_id = trimAndClean($_POST['test_id']);
        $user_id = $loggedUser->user_id;

        // update completion_rate
        $completion_rate = trimAndClean($_POST['completion_rate']);
        $sql = "UPDATE user_test SET test_info = 'Complete', completion_rate = ? WHERE user_id = ? AND test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sss", $completion_rate, $user_id, $test_id);
        $stmt->execute();
        $stmt->close();

        // calculate spent_time
        $sql = "SELECT submitted_time FROM answer_summary WHERE user_id = ? AND test_id = ? ORDER BY submitted_time DESC ";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $user_id, $test_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($submitted_time);
        $spent_time = 0;
        while ($stmt->fetch()) {
            $spent_time = time() - strtotime($submitted_time);
            break;
        }
        $stmt->free_result();
        $stmt->close();

        // insert answer_summary
        if (isset($_POST['new_mastered']) && $_POST['new_mastered'] == 1) {
            $sql = "INSERT INTO answer_summary (user_id, test_id, question_id, answer, correct, submitted_time, test_mode, new_mastered) VALUES(?, ?, ?, ?, 1, NOW(), ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ssssss", $user_id, $test_id, $_POST['question_id'], $_POST['answer'], $_POST['test_mode'], $_POST['new_mastered']);
            $stmt->execute();
            $stmt->close();
        } else {
            $sql = "INSERT INTO answer_summary (user_id, test_id, question_id, answer, correct, submitted_time, test_mode) VALUES(?, ?, ?, ?, 1, NOW(), ?)";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("sssss", $user_id, $test_id, $_POST['question_id'], $_POST['answer'], $_POST['test_mode']);
            $stmt->execute();
            $stmt->close();
        }

        // update user profile
        $sql = "SELECT total_answered, total_points, points, level, study_time FROM users WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($total_answered, $total_points, $points, $level_num, $study_time);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        $total_answered++;
        $total_points++;
        $points++;
        $sql = "SELECT option_value FROM config WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $option_name);
        $option_name = 'level_points';
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();

        $level_points = unserialize($option_value);
        $level = "level_" . $level_num;
        if ($points >= $level_points[$level]) {
            $level_num++;
            $points = 0;
        }
        // check if the spent time is greater than stop_limit.
        $option_name = 'study_stop_limit';
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        $stop_limit = (int)$option_value;
        if ($spent_time < $stop_limit * 60) {
            $study_time = strtotime($study_time) + $spent_time;
        } else {
            $study_time = strtotime($study_time);
        }
        $study_time = date("Y-m-d h:i:sa", $study_time);
        if (isset($_POST['new_mastered']) && $_POST['new_mastered'] == 1)
            $sql = "UPDATE users SET level = ?, points = ?, total_points = ?, total_answered = ?, study_time = ?, new_mastered = new_mastered + 1 WHERE user_id = ?";
        else
            $sql = "UPDATE users SET level = ?, points = ?, total_points = ?, total_answered = ?, study_time = ? WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssssss", $level_num, $points, $total_points, $total_answered, $study_time, $user_id);
        $stmt->execute();
        $stmt->close();

        // update professor points
        $sql = "SELECT author_id FROM test WHERE test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $test_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($author_id);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        $sql = "UPDATE users SET professor_points = professor_points + 1 WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $author_id);
        $stmt->execute();
        $stmt->close();
    }


    elseif ($type == "mastered") {
        /**
         * update user test test_info column as Mastered since user finished one test by user id and test id
         * one row will be given by user id and test id
         */
        $test_id = trimAndClean($_POST['test_id']);
        $user_id = $loggedUser->user_id;

        /* update user test table by user id and test id */
        $sql = "UPDATE user_test SET test_info = 'Mastered', mastery_date = now(), mode_number = ? WHERE user_id = ? AND test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sss", $_POST['mode_number'], $user_id, $test_id);
        $stmt->execute();
        $stmt->close();

        /* update popularity 1 increment since user finish this test */
        $sql = "UPDATE test SET popularity = popularity + 1 WHERE test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $test_id);
        $stmt->execute();
        $stmt->close();

        /* remove one test id from que since user finished one test */
        $sql = "SELECT que FROM users WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($que);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        if ($que != "") {
            $que_array = unserialize($que);
            if (in_array($test_id, $que_array)) {
                $key = array_search($test_id, $que_array);
                if ($key == 0) {
                    array_splice($que_array, $key, 1);
                    if (count($que_array) == 0) {
                        $que = "";
                    } else {
                        $que = serialize($que_array);
                    }
                    $sql = "UPDATE users SET que = ? WHERE user_id = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param("ss", $que, $user_id);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }

        if ($que == "") {
            $data['next_test'] = "";
        } else {
            $data['next_test'] = $que_array[0];
        }

    }











    elseif ($type == "not-complete") {
        /* Not sure yet */
        $test_id = trimAndClean($_POST['test_id']);
        $user_id = $loggedUser->user_id;

        // update completion_rate
        $completion_rate = trimAndClean($_POST['completion_rate']);
        $sql = "UPDATE user_test SET test_info = 'Complete', completion_rate = ? WHERE user_id = ? AND test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sss", $completion_rate, $user_id, $test_id);
        $stmt->execute();
        $stmt->close();

        // insert answer_summary
        $sql = "INSERT INTO answer_summary (user_id, test_id, question_id, answer, correct, submitted_time, test_mode) VALUES(?, ?, ?, ?, 0, NOW(), ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sssss", $user_id, $test_id, ($_POST['question_id']), ($_POST['answer']), ($_POST['test_mode']));
        $stmt->execute();
        $stmt->close();

        // update total_answered
        $sql = "UPDATE users SET total_answered = total_answered + 1 WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->close();

    }










    elseif ($type == "pre_test") {
        /* update pre test value to user_test table by user id and test id */
        $test_id = trimAndClean($_POST['test_id']);
        $user_id = $loggedUser->user_id;
        $pre_test = trimAndClean($_POST['pre_test']);
        $sql = "UPDATE user_test SET pre_test = ? WHERE user_id = ? AND test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sss", $pre_test, $user_id, $test_id);
        $stmt->execute();
        $stmt->close();
    }









    elseif ($type == "post_test") {
        /* update post test value to user_test table by user id and test id */
        $test_id = trimAndClean($_POST['test_id']);
        $user_id = $loggedUser->user_id;
        $post_test = trimAndClean($_POST['post_test']);
        $sql = "UPDATE user_test SET post_test = ? WHERE user_id = ? AND test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sss", $post_test, $user_id, $test_id);
        $stmt->execute();
        $stmt->close();
    }









    elseif ($type == "current_state") {
        /* update question info(history, start point) to user test table by user id and test id, will be used for during test...  */
        $test_id = trimAndClean($_POST['test_id']);
        $user_id = $loggedUser->user_id;
        $question_start_point = trimAndClean($_POST['insert_start_point']);
        $question_history = serialize($_POST['question_history']);
        $sql = "UPDATE user_test SET question_history = ?, question_start_point = ? WHERE user_id = ? AND test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssss", $question_history, $question_start_point, $user_id, $test_id);
        $stmt->execute();
        $stmt->close();
    }










    elseif ($type == "que_options") {
        /* Not sure */
        $test_id = trimAndClean($_POST['test_id']);
        $user_id = $loggedUser->user_id;
        $mastery_number = trimAndClean($_POST['mastery_number']);
        $award_time = trimAndClean($_POST['award_time']);
        $sql = "SELECT id FROM user_test WHERE user_id = ? AND test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $user_id, $test_id);
        $stmt->execute();
        $stmt->store_result();
        $row = $stmt->num_rows();
        $stmt->bind_result($user_test_id);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        if ($row != 0) {
            $sql = "UPDATE user_test SET award_time = ?, mastery_number = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("sss", $award_time, $mastery_number, $user_test_id);
            $stmt->execute();
            $stmt->close();
        } else {
            $sql = "INSERT INTO user_test (user_id, test_id, award_time, mastery_number) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ssss", $user_id, $test_id, $award_time, $mastery_number);
            $stmt->execute();
            $stmt->close();
        }
    }


    elseif ($type == "reset_progress") {
        /* Not sure */
        $test_id = trimAndClean($_POST['test_id']);
        $user_id = $loggedUser->user_id;
        $sql = "SELECT id FROM user_test WHERE user_id = ? AND test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $user_id, $test_id);
        $stmt->execute();
        $stmt->store_result();
        $row = $stmt->num_rows();
        $stmt->bind_result($user_test_id);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        if ($row != 0) {
            $sql = "UPDATE user_test SET test_info = '', completion_rate = 0, mode_number = mode_number + 1, question_history = '', question_start_point = 0 WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $user_test_id);
            $stmt->execute();
            $stmt->close();
        }

        $sql = "SELECT * FROM question WHERE test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $test_id);
        $stmt->execute();
        $stmt->store_result();
        $row = $stmt->num_rows();
        $stmt->free_result();
        $stmt->close();

        $data['progress'] = '0 / ' . $row;
    }




    elseif ($type == "test_options") {
        /* Not sure */
        $test_id = trimAndClean($_POST['test_id']);
        $mastery_number = trimAndClean($_POST['mastery_number']);
        $award_time = trimAndClean($_POST['award_time']);
        $form_options['font'] = trimAndClean($_POST['form_options'][0]);
        $form_options['width'] = trimAndClean($_POST['form_options'][1]);
        $form_options = serialize($form_options);

        $sql = "UPDATE test SET mastery_number = ?, award_time = ?, form_options = ? WHERE test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssss", $mastery_number, $award_time, $form_options, $test_id);
        $stmt->execute();
        $stmt->close();
    }











    elseif ($type == "retake_test") {
        $test_id = trimAndClean($_POST['test_id']);
        $user_id = $loggedUser->user_id;

        /* since retake update user_test table as initial value by user and test id  */
        $sql = "UPDATE user_test SET test_info = '', completion_rate = 0, question_history = '', question_start_point = 0 WHERE user_id = ? AND test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $user_id, $test_id);
        $stmt->execute();
        $stmt->close();

        /* get master bonus value from config table by option name */
        $sql = "SELECT option_value FROM config WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $option_name);
        $option_name = "mastery_bonus";
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($mastery_bonus);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        $sql = "UPDATE users SET total_points = total_points + $mastery_bonus WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->close();
    }


    elseif ($type == "mode_update") {
        /* update user test table set mode number with POST value by user and test id */
        $test_id = trimAndClean($_POST['test_id']);
        $user_id = $loggedUser->user_id;
        $mode_number = trimAndClean($_POST['mode_number']);
        $sql = "UPDATE user_test SET mode_number = ? WHERE user_id = ? AND test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sss", $mode_number, $user_id, $test_id);
        $stmt->execute();
        $stmt->close();
    }

    // called after test is mastered, to add update users level
    elseif ($type == "add_1_to_level") {
        $user_id = $loggedUser->user_id;
        $user_level = trimAndClean($_POST['user_level']);

        $sql = "
        UPDATE users
        SET user_level = $user_level
        WHERE user_id = ?
        ";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->close();
    }


}  //// end ADD



elseif ($action == "get") {
    // get test list by subject id and test type, used on choose-test for main search

    // not used
    // if (($_SERVER['REQUEST_METHOD'] === 'POST') && ($_POST['session_token'] == $_SESSION['token'])) {

    if ($type == "category") {
        $subject_id = trimAndClean($_POST['subject_id']);
        $filter_type = trimAndClean($_POST['filter_type']);

        //when this is called, take the filter type and subject id and put it in the choose history array and serialize it
        $choose_history = array('filter' => $filter_type, 'subject_id' => $subject_id);
        $choose_history = serialize($choose_history);

        // update the choose history in DB
        $user_id = $loggedUser->user_id;
        if ($subject_id != "") {
            $sql = "UPDATE users SET choose_history = ? WHERE user_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ss", $choose_history, $user_id);
            $stmt->execute();
            $stmt->close();
        }

        if ($filter_type == "history") {
            if ($subject_id == "all") {
                $sql = "
                        SELECT test.*
                        FROM test
                        JOIN user_test
                        ON test.test_id = user_test.test_id
                        WHERE user_test.user_id = '$user_id' AND user_test.test_info != ''
                        GROUP BY test.test_id
                        ORDER BY user_test.start_date DESC
                    ";
            } else {
                $sql = "
                        SELECT test.*
                        FROM test
                        JOIN user_test
                        ON test.test_id = user_test.test_id
                        WHERE test.subject_id = '$subject_id' AND user_test.user_id = '$user_id' AND user_test.test_info != ''
                        GROUP BY test.test_id
                        ORDER BY user_test.start_date DESC
                    ";
            }

        } elseif ($filter_type == "personal") {

            if ($subject_id == "all") {
                $sql = "SELECT * FROM test WHERE author_id = '$user_id' AND public = 0 ORDER BY created_time ASC ";
            } else {
                $sql = "SELECT * FROM test WHERE subject_id = '$subject_id' AND author_id = '$user_id' AND public = 0 ORDER BY created_time ASC ";
            }
        } else {
            if ($subject_id == "all") {
                $sql = "
                        SELECT test.test_id, test.test_name, test.video_link, test.mastery_type, test.mastery_number, test.test_type, test.attachment, test.school_year, test.gradeless, subject.subject_id
                        FROM test
                        JOIN subject
                        ON test.subject_id = subject.subject_id OR CONCAT(',', test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                        WHERE test.public = 2 AND subject.grade_id = '$filter_type'
                        GROUP BY test.test_id
                        ORDER BY test.school_year ASC
                    ";
            } else {
                $sql = "
                        SELECT
                              *
                        FROM
                              (    SELECT test.test_id, test.test_name, test.video_link, test.mastery_type, test.mastery_number, test.test_type, test.attachment, test.school_year, test.gradeless, subject.subject_id, subject.subject_name, subject.grade_id
                                   FROM test
                                   JOIN subject
                                   ON test.subject_id = subject.subject_id OR CONCAT(',', test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                                   WHERE test.public = 2
                              ) test_subject
                        WHERE test_subject.subject_id = '$subject_id'
                        GROUP BY test_subject.test_id
                        ORDER BY test_subject.school_year ASC
                    ";
            }
        }
        $test = $db->queryArray($sql);


    }







        elseif ($type == "sort") {
        /**
         * get test with related information by different sort option
         * every query order by clause is just different
         * and query is different according to filter type
         * this query is explained in detail on user-test php
         * this is repeat part
         */
        $subject_id = trimAndClean($_POST['subject_id']);
        $sort = trimAndClean($_POST['sort']);
        $filter_type = trimAndClean($_POST['filter_type']);
        $user_id = $loggedUser->user_id;
        if ($filter_type == "history") {
            if ($sort == "order") {
                if ($subject_id == "all") {
                    $sql = "
                            SELECT test.*
                            FROM test
                            JOIN user_test
                            ON test.test_id = user_test.test_id
                            WHERE user_test.user_id = '$user_id' AND user_test.test_info != ''
                            GROUP BY test.test_id
                            ORDER BY test.school_year ASC
                        ";
                } else {
                    $sql = "
                            SELECT test.*
                            FROM test
                            JOIN user_test
                            ON test.test_id = user_test.test_id
                            WHERE test.subject_id = '$subject_id' AND user_test.user_id = '$user_id' AND user_test.test_info != ''
                            GROUP BY test.test_id
                            ORDER BY test.school_year ASC
                        ";
                }
            } elseif ($sort == "popularity") {
                if ($subject_id == "all") {
                    $sql = "
                            SELECT test.*
                            FROM test
                            JOIN user_test
                            ON test.test_id = user_test.test_id
                            WHERE user_test.user_id = '$user_id' AND user_test.test_info != ''
                            GROUP BY test.test_id
                            ORDER BY test.popularity DESC
                        ";
                } else {
                    $sql = "
                            SELECT test.*
                            FROM test
                            JOIN user_test
                            ON test.test_id = user_test.test_id
                            WHERE test.subject_id = '$subject_id' AND user_test.user_id = '$user_id' AND user_test.test_info != ''
                            GROUP BY test.test_id
                            ORDER BY test.popularity DESC
                        ";
                }
            } elseif ($sort == "name a-z") {
                if ($subject_id == "all") {
                    $sql = "
                            SELECT test.*
                            FROM test
                            JOIN user_test
                            ON test.test_id = user_test.test_id
                            WHERE user_test.user_id = '$user_id' AND user_test.test_info != ''
                            GROUP BY test.test_id
                            ORDER BY test.test_name ASC
                        ";
                } else {
                    $sql = "
                            SELECT test.*
                            FROM test
                            JOIN user_test
                            ON test.test_id = user_test.test_id
                            WHERE test.subject_id = '$subject_id' AND user_test.user_id = '$user_id' AND user_test.test_info != ''
                            GROUP BY test.test_id
                            ORDER BY test.test_name ASC
                        ";
                }
            } elseif ($sort == "date taken") {
                if ($subject_id == "all") {
                    $sql = "
                            SELECT test.*
                            FROM test
                            JOIN user_test
                            ON test.test_id = user_test.test_id
                            WHERE user_test.user_id = '$user_id' AND user_test.test_info != ''
                            GROUP BY test.test_id
                            ORDER BY user_test.start_date DESC
                        ";
                } else {
                    $sql = "
                            SELECT test.*
                            FROM test
                            JOIN user_test
                            ON test.test_id = user_test.test_id
                            WHERE test.subject_id = '$subject_id' AND user_test.user_id = '$user_id' AND user_test.test_info != ''
                            GROUP BY test.test_id
                            ORDER BY user_test.start_date DESC
                        ";
                }
            }
        } elseif ($filter_type == "personal") {
            if ($sort == "order") {
                if ($subject_id == "all")
                    $sql = "SELECT * FROM test WHERE public = 0 AND author_id = '$user_id' ORDER BY school_year ASC ";
                else
                    $sql = "SELECT * FROM test WHERE public = 0 AND author_id = '$user_id' AND subject_id = '$subject_id' ORDER BY school_year ASC";
            } elseif ($sort == "popularity") {
                if ($subject_id == "all")
                    $sql = "SELECT * FROM test WHERE public = 0 AND author_id = '$user_id' ORDER BY popularity DESC ";
                else
                    $sql = "SELECT * FROM test WHERE public = 0 AND author_id = '$user_id' AND subject_id = '$subject_id' ORDER BY popularity DESC";
            } elseif ($sort == "name a-z") {
                if ($subject_id == "all")
                    $sql = "SELECT * FROM test WHERE public = 0 AND author_id = '$user_id' ORDER BY test_name ASC";
                else
                    $sql = "SELECT * FROM test WHERE public = 0 AND author_id = '$user_id' AND subject_id = '$subject_id' ORDER BY test_name ASC";
            } elseif ($sort == "date taken") {
                if ($subject_id == "all")
                    $sql = "SELECT * FROM test WHERE public = 0 AND author_id = '$user_id' ORDER BY created_time ASC";
                else
                    $sql = "SELECT * FROM test WHERE public = 0 AND author_id = '$user_id' AND subject_id = '$subject_id' ORDER BY created_time ASC";
            }
        } else {

            if ($sort == "order") {

                if ($subject_id == "all") {
                    $sql = "
                            SELECT test.test_id, test.test_name, test.video_link, test.mastery_type, test.mastery_number, test.test_type, test.attachment, test.gradeless, subject.subject_id
                            FROM test
                            JOIN subject
                            ON test.subject_id = subject.subject_id OR CONCAT(',', test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                            WHERE test.public = 2 AND subject.grade_id = '$filter_type'
                            GROUP BY test.test_id
                            ORDER BY test.school_year ASC
                        ";
                } else {
                    $sql = "
                            SELECT
                                  *
                            FROM
                                  (    SELECT test.test_id, test.test_name, test.video_link, test.mastery_type, test.mastery_number, test.test_type, test.attachment, test.school_year, test.gradeless, subject.subject_id, subject.subject_name, subject.grade_id
                                       FROM test
                                       JOIN subject
                                       ON test.subject_id = subject.subject_id OR CONCAT(',', test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                                       WHERE test.public = 2
                                  ) test_subject
                            WHERE test_subject.subject_id = '$subject_id'
                            GROUP BY test_subject.test_id
                            ORDER BY test_subject.school_year ASC 
                        ";
                }
            } elseif ($sort == "popularity") {
                if ($subject_id == "all") {
                    $sql = "
                            SELECT test.test_id, test.test_name, test.video_link, test.mastery_type, test.mastery_number, test.test_type, test.attachment, test.gradeless, subject.subject_id
                            FROM test
                            JOIN subject
                            ON test.subject_id = subject.subject_id OR CONCAT(',', test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                            WHERE test.public = 2 AND subject.grade_id = '$filter_type'
                            GROUP BY test.test_id
                            ORDER BY test.popularity DESC
                        ";
                } else {
                    $sql = "
                            SELECT
                                  *
                            FROM
                                  (    SELECT test.test_id, test.test_name, test.video_link, test.mastery_type, test.mastery_number, test.test_type, test.attachment, test.popularity, test.gradeless, subject.subject_id, subject.subject_name, subject.grade_id
                                       FROM test
                                       JOIN subject
                                       ON test.subject_id = subject.subject_id OR CONCAT(',', test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                                       WHERE test.public = 2
                                  ) test_subject
                            WHERE test_subject.subject_id = '$subject_id'
                            GROUP BY test_subject.test_id
                            ORDER BY test_subject.popularity DESC
                        ";
                }
            } elseif ($sort == "name a-z") {
                if ($subject_id == "all") {
                    $sql = "
                            SELECT test.test_id, test.test_name, test.video_link, test.mastery_type, test.mastery_number, test.test_type, test.attachment, test.gradeless, subject.subject_id
                            FROM test
                            JOIN subject
                            ON test.subject_id = subject.subject_id OR CONCAT(',', test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                            WHERE test.public = 2 AND subject.grade_id = '$filter_type'
                            GROUP BY test.test_id
                            ORDER BY test.test_name ASC
                        ";
                } else {
                    $sql = "
                            SELECT
                                  *
                            FROM
                                  (    SELECT test.test_id, test.test_name, test.video_link, test.mastery_type, test.mastery_number, test.test_type, test.attachment, test.gradeless, subject.subject_id, subject.subject_name, subject.grade_id
                                       FROM test
                                       JOIN subject
                                       ON test.subject_id = subject.subject_id OR CONCAT(',', test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                                       WHERE test.public = 2
                                  ) test_subject
                            WHERE test_subject.subject_id = '$subject_id'
                            GROUP BY test_subject.test_id
                            ORDER BY test_subject.test_name ASC
                        ";
                }
            } elseif ($sort == "date taken") {
                if ($subject_id == "all") {
                    $sql = "
                            SELECT test.test_id, test.test_name, test.video_link, test.mastery_type, test.mastery_number, test.test_type, test.attachment, test.gradeless, subject.subject_id
                            FROM test
                            JOIN subject
                            ON test.subject_id = subject.subject_id OR CONCAT(',', test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                            WHERE test.public = 2 AND subject.grade_id = '$filter_type'
                            GROUP BY test.test_id
                            ORDER BY test.created_time ASC
                        ";
                } else {
                    $sql = "
                            SELECT
                                  *
                            FROM
                                  (    SELECT test.test_id, test.test_name, test.video_link, test.mastery_type, test.mastery_number, test.test_type, test.attachment, test.created_time, test.gradeless, subject.subject_id, subject.subject_name, subject.grade_id
                                       FROM test
                                       JOIN subject
                                       ON test.subject_id = subject.subject_id OR CONCAT(',', test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                                       WHERE test.public = 2
                                  ) test_subject
                            WHERE test_subject.subject_id = '$subject_id'
                            GROUP BY test_subject.test_id
                            ORDER BY test_subject.created_time ASC
                        ";
                }
            }
        }

        $test = $db->queryArray($sql);

    }











    elseif ($type == "sort-personal") {

        /* personal tests sort by test id DESC */
        $sort = trimAndClean($_POST['sort']);
        $user_id = $loggedUser->user_id;

        if ($sort == "created date") {
            $sql = "SELECT * FROM test WHERE public = 0 AND author_id = '$user_id' ORDER BY test_id DESC ";
        } elseif ($sort == "name a-z") {
            $sql = "SELECT * FROM test WHERE public = 0 AND author_id = '$user_id' ORDER BY test_name ASC ";
        }

        $test = $db->queryArray($sql);

    }
        elseif ($type == "sort-level") {

        $sort = trimAndClean($_POST['sort']);
        $user_id = $loggedUser->user_id;
        if ($sort == "date") {
            $sql = "SELECT * FROM user_test WHERE user_id = '$user_id' AND test_info != '' ORDER BY start_date DESC";
            $user_test = $db->queryArray($sql);
            if (isset($user_test)) {
                $test = array();
                for ($i = 0; $i < count($user_test); $i++) {
                    $sql = "SELECT * FROM test WHERE test_id = '" . $user_test[$i]['test_id'] . "'";
                    $one_test = $db->queryArray($sql);
                    $test[$i] = $one_test[0];
                }
            }
        } elseif ($sort == "name a-z") {
            $sql = "SELECT * FROM user_test WHERE user_id = '$user_id' AND test_info != ''";
            $user_test = $db->queryArray($sql);
            if (isset($user_test)) {
                $id_array = array();
                for ($i = 0; $i < count($user_test); $i++) {
                    $id_array[$i] = $user_test[$i]['test_id'];
                }
                $ids = join(',', $id_array);
                $sql = "SELECT * FROM test WHERE test_id IN ($ids) ORDER BY test_name ASC";
                $test = $db->queryArray($sql);
            }
        }
    }

    if (isset($test)) {

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

        $user_id = $loggedUser->user_id;

        $sql = "SELECT que FROM users WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($que);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        $sql = "SELECT * FROM question WHERE test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $test_id);

        $sql = "SELECT view_video, pre_test, post_test, in_game_mastered FROM user_test WHERE user_id = ? AND test_id = ?";
        $stmt_1 = $db->prepare($sql);
        $stmt_1->bind_param("ss", $user_id, $test_id);

        $sql = "SELECT subject_name, grade_id FROM subject WHERE subject_id = ?";
        $stmt_2 = $db->prepare($sql);
        $stmt_2->bind_param("s", $subject_id);

        $sql = "SELECT grade_name FROM grade WHERE grade_id = ?";
        $stmt_3 = $db->prepare($sql);
        $stmt_3->bind_param("s", $grade_id);

        $sql = "SELECT * FROM video WHERE test_id = ? AND public = 1";
        $stmt_4 = $db->prepare($sql);
        $stmt_4->bind_param("s", $test_id);

//        $sql = "SELECT * FROM answer_summary WHERE user_id = ? AND test_id = ? AND correct = 1 AND test_mode = ?";
//        $stmt_5 = $db->prepare($sql);
//        $stmt_5->bind_param("sss", $user_id, $test_id, $test_mode);

//        $sql = "SELECT submitted_time FROM answer_summary WHERE user_id = ? AND test_id = ? AND test_mode = ? ORDER BY submitted_time DESC ";
//        $stmt_6 = $db->prepare($sql);
//        $stmt_6->bind_param("sss", $user_id, $test_id, $test_mode);

        for ($i = 0; $i < count($test); $i++) {

            if ($test[$i]['mastery_number'] == "") {
                if ($test[$i]['mastery_type'] == "memorization") {
                    $test[$i]['mastery_number'] = $memorization_mastery;
                } else {
                    $test[$i]['mastery_number'] = $problem_mastery;
                }
            }

            $test_id = $test[$i]['test_id'];
            $stmt->execute();
            $stmt->store_result();
            $question_length = $stmt->num_rows();
            $stmt->free_result();

            $stmt_1->execute();
            $stmt_1->store_result();
            $row_1 = $stmt_1->num_rows();
            $stmt_1->bind_result($view_video, $pre_test, $post_test, $in_game_mastered);
            $stmt_1->fetch();
            $stmt_1->free_result();

            $subject_id = $test[$i]['subject_id'];
            $stmt_2->execute();
            $stmt_2->store_result();
            $stmt_2->bind_result($subject_name, $grade_id);
            $stmt_2->fetch();
            $stmt_2->free_result();

            $stmt_3->execute();
            $stmt_3->store_result();
            $stmt_3->bind_result($grade_name);
            $stmt_3->fetch();
            $stmt_3->free_result();

            $stmt_4->execute();
            $stmt_4->store_result();
            $videos_number = $stmt_4->num_rows();
            $stmt_4->free_result();

            $test_mode = "pre_test";
//            $stmt_5->execute();
//            $stmt_5->store_result();
//            $correct_pre_test = $stmt_5->num_rows();
//            $stmt_5->free_result();

//            $stmt_6->execute();
//            $stmt_6->store_result();
//            $stmt_6->bind_result($pre_complete_time);
//            $stmt_6->fetch();
//            $stmt_6->free_result();
//            if (isset($pre_complete_time)) {
//                $pre_complete_time = strtotime($pre_complete_time) * 1000;
//            }

            $test_mode = "post_test";
//            $stmt_5->execute();
//            $stmt_5->store_result();
//            $correct_post_test = $stmt_5->num_rows();
//            $stmt_5->free_result();

//            $stmt_6->execute();
//            $stmt_6->store_result();
//            $stmt_6->bind_result($post_complete_time);
//            $stmt_6->fetch();
//            $stmt_6->free_result();
//            if (isset($post_complete_time)) {
//                $post_complete_time = strtotime($post_complete_time) * 1000;
//            }

            $in_que = "Add to Queue";
            if ($que != "") {
                $que_array = unserialize($que);
                if (in_array($test_id, $que_array)) {
                    $in_que = "In Que";
                }
            }

            $test[$i]['pre_test'] = $pre_test;
            $test[$i]['post_test'] = $post_test;
            $test[$i]['question_length'] = $question_length;
          //  $test[$i]['correct_pre_test'] = $correct_pre_test;
          //  $test[$i]['pre_complete_time'] = $pre_complete_time;
          //  $test[$i]['correct_post_test'] = $correct_post_test;
          //  $test[$i]['post_complete_time'] = $post_complete_time;

            if ($test[$i]['gradeless'] == 1) {
                $test[$i]['grade_subject'] = $subject_name;
            } else {
                $test[$i]['grade_subject'] = $grade_name . " - " . $subject_name;
            }

            $test[$i]['in_que'] = $in_que;

            $test[$i]['view_video'] = $view_video;

            if ($test[$i]['video_link'] != "") {
                $pattern = '%(?:youtube\.com/(?:user/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';
                preg_match($pattern, $test[$i]['video_link'], $matches);
                $test[$i]['video_link'] = isset($matches[1]) ? $matches[1] : "";
            }

            $test[$i]['in_game_mastered'] = $in_game_mastered;

            $test[$i]['result_exist'] = 0;
            if ($row_1 != 0) {
                $test[$i]['result_exist'] = 1;
            }

            $test[$i]['videos_number'] = $videos_number;
        }

        $stmt->close();
        $stmt_1->close();
        $stmt_2->close();
        $stmt_3->close();
        $stmt_4->close();
//        $stmt_5->close();
//        $stmt_6->close();

        $data['test'] = $test;
        unset($test);
    }

    // admin tests
    if ($type == "admin_category") {
        $filter_type = trimAndClean($_POST['filter_type']);
        $subject_id = trimAndClean($_POST['subject_id']);
        $user_role = $_SESSION['RB_USER_ROLE'];
        $admin_history = array('filter' => $filter_type, 'subject_id' => $subject_id);
        $admin_history = serialize($admin_history);
        $user_id = $loggedUser->user_id;
        if ($subject_id == "") {
            $sql = "UPDATE users SET admin_history = '' WHERE user_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $stmt->close();
        } else {
            $sql = "UPDATE users SET admin_history = ? WHERE user_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ss", $admin_history, $user_id);
            $stmt->execute();
            $stmt->close();
        }
        if ($subject_id == "") {
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
        } else {
            if ($subject_id == "all") {
                $sql = "
                       SELECT
                          test_subject.*, grade.grade_name
                       FROM
                          (    SELECT test.*, subject.subject_name, subject.grade_id
                               FROM test
                               JOIN subject
                               ON test.subject_id = subject.subject_id OR CONCAT(',', test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                               WHERE test.public IN (1,2) AND subject.grade_id = '$filter_type'
                               GROUP BY test.test_id
                               ORDER BY test.created_time DESC 
                          ) test_subject
                       JOIN grade
                       ON test_subject.grade_id = grade.grade_id
                    ";
            } else {
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
                       WHERE test_subject.subject_id = '$subject_id'
                       GROUP BY test_subject.test_id
                       ORDER BY test_subject.created_time DESC
                    ";
            }
        }
        $test = $db->queryArray($sql);
    } elseif ($type == "sort-admin") {
        $filter_type = trimAndClean($_POST['filter_type']);
        $subject_id = trimAndClean($_POST['subject_id']);
        $user_role = $_SESSION['RB_USER_ROLE'];
        $sort = trimAndClean($_POST['sort']);
        if ($sort == "date created") {
            if ($subject_id == "") {
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
            } else {
                if ($subject_id == "all") {
                    $sql = "
                           SELECT
                              test_subject.*, grade.grade_name
                           FROM
                              (    SELECT test.*, subject.subject_name, subject.grade_id
                                   FROM test
                                   JOIN subject
                                   ON test.subject_id = subject.subject_id OR CONCAT(',', test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                                   WHERE test.public IN (1,2) AND subject.grade_id = '$filter_type'
                                   GROUP BY test.test_id
                                   ORDER BY test.created_time DESC 
                              ) test_subject
                           JOIN grade
                           ON test_subject.grade_id = grade.grade_id
                        ";
                } else {
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
                           WHERE test_subject.subject_id = '$subject_id'
                           GROUP BY test_subject.test_id
                           ORDER BY test_subject.created_time DESC
                        ";
                }
            }
            $test = $db->queryArray($sql);
        } elseif ($sort == "grade - subject") {
            if ($subject_id == "") {
                $sql = "
                       SELECT
                          test_subject.*, grade.grade_name
                       FROM
                          (    SELECT test.*, subject.subject_name, subject.grade_id
                               FROM test
                               JOIN subject
                               ON test.subject_id = subject.subject_id
                               WHERE test.public IN (1,2)
                               ORDER BY subject.subject_name ASC
                          ) test_subject
                       JOIN grade
                       ON test_subject.grade_id = grade.grade_id
                       ORDER BY grade.grade_id ASC
                    ";
            } else {
                if ($subject_id == "all") {
                    $sql = "
                           SELECT
                              test_subject.*, grade.grade_name
                           FROM
                              (    SELECT test.*, subject.subject_name, subject.grade_id
                                   FROM test
                                   JOIN subject
                                   ON test.subject_id = subject.subject_id OR CONCAT(',', test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                                   WHERE test.public IN (1,2) AND subject.grade_id = '$filter_type'
                                   GROUP BY test.test_id
                                   ORDER BY subject.subject_name ASC
                              ) test_subject
                           JOIN grade
                           ON test_subject.grade_id = grade.grade_id
                           ORDER BY grade.grade_id ASC
                        ";
                } else {
                    $sql = "
                           SELECT
                              test_subject.*, grade.grade_name
                           FROM
                              (    SELECT test.test_id, test.test_name, test.video_link, test.test_type, test.attachment, test.author_id, test.popularity, test.public, test.school_year, test.created_time, test.gradeless, subject.subject_id, subject.subject_name, subject.grade_id
                                   FROM test
                                   JOIN subject
                                   ON test.subject_id = subject.subject_id OR CONCAT(',', test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                                   WHERE test.public IN (1,2)
                                   ORDER BY subject.subject_name ASC 
                              ) test_subject
                           JOIN grade
                           ON test_subject.grade_id = grade.grade_id
                           WHERE test_subject.subject_id = '$subject_id'
                           GROUP BY test_subject.test_id
                           ORDER BY grade.grade_id ASC
                        ";
                }
            }
            $test = $db->queryArray($sql);
        } elseif ($sort == "popularity") {
            if ($subject_id == "") {
                $sql = "
                       SELECT
                          test_subject.*, grade.grade_name
                       FROM
                          (    SELECT test.*, subject.subject_name, subject.grade_id
                               FROM test
                               JOIN subject
                               ON test.subject_id = subject.subject_id
                               WHERE test.public IN (1,2)
                               ORDER BY test.popularity DESC
                          ) test_subject
                       JOIN grade
                       ON test_subject.grade_id = grade.grade_id
                    ";
            } else {
                if ($subject_id == "all") {
                    $sql = "
                           SELECT
                              test_subject.*, grade.grade_name
                           FROM
                              (    SELECT test.*, subject.subject_name, subject.grade_id
                                   FROM test
                                   JOIN subject
                                   ON test.subject_id = subject.subject_id OR CONCAT(',', test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                                   WHERE test.public IN (1,2) AND subject.grade_id = '$filter_type'
                                   GROUP BY test.test_id
                                   ORDER BY test.popularity DESC 
                              ) test_subject
                           JOIN grade
                           ON test_subject.grade_id = grade.grade_id
                        ";
                } else {
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
                           WHERE test_subject.subject_id = '$subject_id'
                           GROUP BY test_subject.test_id
                           ORDER BY test_subject.popularity DESC
                        ";
                }
            }
            $test = $db->queryArray($sql);
        } elseif ($sort == "email a-z") {
            $sql = "SELECT * FROM users ORDER BY user_email ASC ";
            $users = $db->queryArray($sql);
            if (isset($users)) {
                $test = array();
                for ($i = 0; $i < count($users); $i++) {
                    if ($subject_id == "") {
                        $sql = "
                               SELECT
                                  test_subject.*, grade.grade_name
                               FROM
                                  (    SELECT test.*, subject.subject_name, subject.grade_id
                                       FROM test
                                       JOIN subject
                                       ON test.subject_id = subject.subject_id
                                       WHERE test.public IN (1,2) AND test.author_id = '" . $users[$i]['user_id'] . "'
                                  ) test_subject
                               JOIN grade
                               ON test_subject.grade_id = grade.grade_id
                            ";
                    } else {
                        if ($subject_id == "all") {
                            $sql = "
                                   SELECT
                                      test_subject.*, grade.grade_name
                                   FROM
                                      (    SELECT test.*, subject.subject_name, subject.grade_id
                                           FROM test
                                           JOIN subject
                                           ON test.subject_id = subject.subject_id OR CONCAT(',', test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                                           WHERE test.public IN (1,2) AND subject.grade_id = '$filter_type' AND test.author_id = '" . $users[$i]['user_id'] . "'
                                           GROUP BY test.test_id 
                                      ) test_subject
                                   JOIN grade
                                   ON test_subject.grade_id = grade.grade_id
                                ";
                        } else {
                            $sql = "
                                   SELECT
                                      test_subject.*, grade.grade_name
                                   FROM
                                      (    SELECT test.test_id, test.test_name, test.video_link, test.test_type, test.attachment, test.author_id, test.popularity, test.public, test.school_year, test.created_time, test.gradeless, subject.subject_id, subject.subject_name, subject.grade_id
                                           FROM test
                                           JOIN subject
                                           ON test.subject_id = subject.subject_id OR CONCAT(',', test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                                           WHERE test.public IN (1,2) AND test.author_id = '" . $users[$i]['user_id'] . "' 
                                      ) test_subject
                                   JOIN grade
                                   ON test_subject.grade_id = grade.grade_id
                                   WHERE test_subject.subject_id = '$subject_id'
                                   GROUP BY test_subject.test_id
                                ";
                        }
                    }
                    $test_part = $db->queryArray($sql);
                    if (isset($test_part)) {
                        $test = array_merge($test, $test_part);
                    }
                }
            }
        } elseif ($sort == "name a-z") {
            if ($subject_id == "") {
                $sql = "
                       SELECT
                          test_subject.*, grade.grade_name
                       FROM
                          (    SELECT test.*, subject.subject_name, subject.grade_id
                               FROM test
                               JOIN subject
                               ON test.subject_id = subject.subject_id
                               WHERE test.public IN (1,2)
                               ORDER BY test.test_name ASC
                          ) test_subject
                       JOIN grade
                       ON test_subject.grade_id = grade.grade_id
                    ";
            } else {
                if ($subject_id == "all") {
                    $sql = "
                           SELECT
                              test_subject.*, grade.grade_name
                           FROM
                              (    SELECT test.*, subject.subject_name, subject.grade_id
                                   FROM test
                                   JOIN subject
                                   ON test.subject_id = subject.subject_id OR CONCAT(',', test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                                   WHERE test.public IN (1,2) AND subject.grade_id = '$filter_type'
                                   GROUP BY test.test_id
                                   ORDER BY test.test_name ASC 
                              ) test_subject
                           JOIN grade
                           ON test_subject.grade_id = grade.grade_id
                        ";
                } else {
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
                           WHERE test_subject.subject_id = '$subject_id'
                           GROUP BY test_subject.test_id
                           ORDER BY test_subject.test_name ASC
                        ";
                }
            }
            $test = $db->queryArray($sql);
        }
    }

    if (isset($test)) {

        $sql = "SELECT user_email FROM users WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $user_id);

        $sql = "SELECT * FROM question WHERE test_id = ?";
        $stmt_1 = $db->prepare($sql);
        $stmt_1->bind_param("s", $test_id);

        for ($i = 0; $i < count($test); $i++) {
            $test[$i]['created_time'] = date("m-d-y", strtotime($test[$i]['created_time']));

            $user_id = $test[$i]['author_id'];
            $stmt->execute();
            $stmt->store_result();
            $row = $stmt->num_rows();
            $stmt->bind_result($user_email);
            $stmt->fetch();
            $stmt->free_result();

            $test_id = $test[$i]['test_id'];
            $stmt_1->execute();
            $stmt_1->store_result();
            $row_1 = $stmt_1->num_rows();
            $stmt_1->free_result();

            if ($row != 0) {
                $test[$i]['user_email'] = $user_email;
            } else {
                $test[$i]['user_email'] = "";
            }

            if ($test[$i]['gradeless'] == 1) {
                $test[$i]['grade_subject'] = $test[$i]['subject_name'];
            } else {
                $test[$i]['grade_subject'] = $test[$i]['grade_name'] . " - " . $test[$i]['subject_name'];
            }

            $test[$i]['question_num'] = $row_1;
        }

        $stmt->close();
        $stmt_1->close();

        $data['test'] = $test;
        unset($test);
    }

    if ($type == "track_history") {
        $track_history = trimAndClean($_POST['track_history']);
        $user_id = $loggedUser->user_id;
        $sql = "UPDATE users SET track_history = ? WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $track_history, $user_id);
        $stmt->execute();
        $stmt->close();

        if ($track_history == "all") {
            $sql = "
                   SELECT t.test_id, CONVERT( a.`submitted_time`, DATE) AS submit_time, t.`test_name`, t.`attachment`, s.`subject_id`, t.`gradeless`, s.`subject_name`, g.`grade_id`, g.grade_name
                    FROM answer_summary a
                        JOIN test t ON a.test_id=t.test_id
                        JOIN subject s ON t.subject_id=s.subject_id
                        JOIN grade g ON s.grade_id=g.grade_id
                    WHERE a.user_id = ?
                    GROUP BY submit_time
                ";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $user_id);
        } else {
            $sql = "
                    SELECT t.test_id, CONVERT( a.`submitted_time`, DATE) AS submit_time, t.`test_name`, t.`attachment`, s.`subject_id`, t.`gradeless`, s.`subject_name`, g.`grade_id`, g.grade_name
                    FROM answer_summary a
                        JOIN test t ON a.test_id=t.test_id
                        JOIN subject s ON t.subject_id=s.subject_id
                        JOIN grade g ON s.grade_id=g.grade_id
                    WHERE a.user_id = ? AND a.`submitted_time` > DATE_SUB(NOW(), INTERVAL ? DAY)
                    GROUP BY submit_time
                ";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ss", $user_id, $track_history);
        }

        $stmt->execute();
        $stmt->store_result();
        $row = $stmt->num_rows();
        $stmt->bind_result($test_id, $submitted_time, $test_name, $attachment, $subject_id, $gradeless, $subject_name, $grade_id, $grade_name);

        $sql = "SELECT * FROM question WHERE test_id = ?";
        $stmt_1 = $db->prepare($sql);
        $stmt_1->bind_param("s", $test_id);

        $sql = "SELECT pre_test, post_test FROM user_test WHERE user_id = ? AND test_id = ?";
        $stmt_2 = $db->prepare($sql);
        $stmt_2->bind_param("ss", $user_id, $test_id);

//        $sql = "SELECT * FROM answer_summary WHERE user_id = ? AND test_id = ? AND correct = 1 AND test_mode = ?";
//        $stmt_3 = $db->prepare($sql);
//        $stmt_3->bind_param("sss", $user_id, $test_id, $test_mode);

//        $sql = "SELECT submitted_time FROM answer_summary WHERE user_id = ? AND test_id = ? AND test_mode = ? ORDER BY submitted_time DESC ";
//        $stmt_4 = $db->prepare($sql);
//        $stmt_4->bind_param("sss", $user_id, $test_id, $test_mode);

        $i = 0;

        while ($stmt->fetch()) {

            $submitted_time_stamp = strtotime($submitted_time) * 1000;   // second to milisecond

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
//            if (isset($pre_complete_time)) {
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
//            if (isset($post_complete_time)) {
//                $post_complete_time = strtotime($post_complete_time) * 1000;
//            }

            $test[$i]['pre_test'] = $pre_test;
            $test[$i]['post_test'] = $post_test;
            $test[$i]['question_length'] = $question_length;
//            $test[$i]['correct_pre_test'] = $correct_pre_test;
//            $test[$i]['pre_complete_time'] = $pre_complete_time;
//            $test[$i]['correct_post_test'] = $correct_post_test;
//            $test[$i]['post_complete_time'] = $post_complete_time;

            if ($gradeless == 1) {
                $grade_subject = $subject_name;
            } else {
                $grade_subject = $grade_name . " - " . $subject_name;
            }

            $test[$i]['test_id'] = $test_id;
            $test[$i]['submitted_time'] = $submitted_time_stamp;
            $test[$i]['test_name'] = $test_name;
            $test[$i]['attachment'] = $attachment;
            $test[$i]['grade_subject'] = $grade_subject;

            $i++;
        }

        $stmt->free_result();
        $stmt->close();

        $stmt_1->close();
        $stmt_2->close();
//        $stmt_3->close();
//        $stmt_4->close();

        $data['test'] = $test;
        unset($test);
    }

    // popularity, public, school year
    if ($type == "popularity") {
        /* get popularity value from test table by test id - primary id  */
        $test_id = trimAndClean($_POST['test_id']);
        $sql = "SELECT * FROM test WHERE test_id = '$test_id'";
        $test = $db->queryArray($sql);
        $data['popularity'] = $test[0]['popularity'];
    } elseif ($type == "public") {
        /* get public value from test table by test id - primary id */
        $test_id = trimAndClean($_POST['test_id']);
        $sql = "SELECT * FROM test WHERE test_id = '$test_id'";
        $test = $db->queryArray($sql);
        $data['public'] = $test[0]['public'];
    } elseif ($type == "school_year") {
        /* get school year value from test table by test id - primary id */
        $test_id = trimAndClean($_POST['test_id']);
        $sql = "SELECT * FROM test WHERE test_id = '$test_id'";
        $test = $db->queryArray($sql);
        $data['school_year'] = $test[0]['school_year'];
    } elseif ($type == "more_subject") {
        /* get more subject value from test table by test id -primary id */
        $test_id = trimAndClean($_POST['test_id']);
        $sql = "SELECT * FROM test WHERE test_id = '$test_id'";
        $test = $db->queryArray($sql);
        $data['more_subject'] = $test[0]['more_subject_id'];
    } elseif ($type == "max_height") {
        $test_id = trimAndClean($_POST['test_id']);
        $sql = "SELECT * FROM test WHERE test_id = '$test_id'";
        $test = $db->queryArray($sql);
        $data['max_height'] = $test[0]['max_height'];
    } elseif ($type == "gradeless") {
        $test_id = trimAndClean($_POST['test_id']);
        $sql = "SELECT * FROM test WHERE test_id = '$test_id'";
        $test = $db->queryArray($sql);
        $data['gradeless'] = $test[0]['gradeless'];
    }

    if ($type == "que_options") {
        $test_id = trimAndClean($_POST['test_id']);
        $user_id = $loggedUser->user_id;
        $sql = "SELECT * FROM user_test WHERE user_id = '$user_id' AND test_id = '$test_id'";
        $user_test = $db->queryArray($sql);
        $data['mastery_number'] = "";
        $data['award_time'] = "";
        if (isset($user_test)) {
            $data['mastery_number'] = $user_test[0]['mastery_number'];
            $data['award_time'] = $user_test[0]['award_time'];
            }
        }

}









elseif ($action == "edit") {
    /* update several values to test table by test id */
    if ($type == "popularity") {
        $test_id = trimAndClean($_POST['test_id']);
        $score = trimAndClean($_POST['score']);
        $sql = "UPDATE test SET popularity = ? WHERE test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $score, $test_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($type == "public") {
        $test_id = trimAndClean($_POST['test_id']);
        $score = trimAndClean($_POST['score']);
        $sql = "UPDATE test SET public = ? WHERE test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $score, $test_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($type == "school_year") {
        $test_id = trimAndClean($_POST['test_id']);
        $score = trimAndClean($_POST['score']);
        $sql = "UPDATE test SET school_year = ? WHERE test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $score, $test_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($type == "attach_test_image") {
        $test_id = trimAndClean($_POST['test_id']);
        $test_path = "../public/upload/img/" . $test_id . "/";
        if (!file_exists($test_path)) {
            mkdir($test_path, 0777, true);
            chmod($test_path, 0777);
        }

        $name = $_FILES['imageUpload']['name'];
        $size = $_FILES['imageUpload']['size'];
        if (strlen($name)) {
            if (!file_exists($test_path . $name)) {
                $tmp = $_FILES['imageUpload']['tmp_name'];
                if (move_uploaded_file($tmp, $test_path . $name)) {
                    $attachment = "upload/img/" . $test_id . "/" . $name;
                    $sql = "UPDATE test SET attachment = ? WHERE test_id = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param("ss", $attachment, $test_id);
                    $stmt->execute();
                    $stmt->close();
                    $data['attachment'] = $attachment;
                } else {
                    $result = "failed";
                }
            } else {
                $attachment = "upload/img/" . $test_id . "/" . $name;
                $sql = "UPDATE test SET attachment = ? WHERE test_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("ss", $attachment, $test_id);
                $stmt->execute();
                $stmt->close();
                $data['attachment'] = $attachment;
            }
        } else {
            $result = "failed";
        }

    } elseif ($type == "public_review") {
        $test_id = trimAndClean($_POST['test_id']);
        $sql = "UPDATE test SET public = 1 WHERE test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $test_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($type == "more_subject") {
        $test_id = trimAndClean($_POST['test_id']);
        $more_subject = $_POST['more_subject'];
        $sql = "UPDATE test SET more_subject_id = ? WHERE test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $more_subject, $test_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($type == "move_to_top") {
        $test_id = trimAndClean($_POST['test_id']);
        $user_id = $loggedUser->user_id;

        $sql = "SELECT que FROM users WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($que);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        $que_array = unserialize($que);

        $key = array_search($test_id, $que_array);
        array_splice($que_array, $key, 1);
        $que_array = array_merge(array(0 => $test_id), $que_array);

        $serial_que = serialize($que_array);
        $sql = "UPDATE users SET que = ? WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $serial_que, $user_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($type == "max_height") {
        $test_id = trimAndClean($_POST['test_id']);
        $max_height = trimAndClean($_POST['max_height']);
        $sql = "UPDATE test SET max_height = ? WHERE test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $max_height, $test_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($type == "gradeless") {
        $test_id = trimAndClean($_POST['test_id']);
        $score = trimAndClean($_POST['score']);
        $sql = "UPDATE test SET gradeless = ? WHERE test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $score, $test_id);
        $stmt->execute();
        $stmt->close();

        $sql = "
               SELECT
                  test_subject.subject_name, grade.grade_name
               FROM
                  (    SELECT subject.subject_name, subject.grade_id
                   FROM test
                   JOIN subject
                   ON test.subject_id = subject.subject_id OR CONCAT(',', test.more_subject_id, ',') LIKE CONCAT('%,', subject.subject_id, ',%')
                   WHERE test.test_id = ?
                   GROUP BY test.test_id 
                  ) test_subject
               JOIN grade
               ON test_subject.grade_id = grade.grade_id
            ";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $test_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($subject_name, $grade_name);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        if ($score == 1) {
            $data['gradeless'] = $subject_name;
        } else {
            $data['gradeless'] = $grade_name . " - " . $subject_name;
        }
    } elseif ($type == "video_link") {
        $test_id = trimAndClean($_POST['test_id']);
        $video_link = trimAndClean($_POST['video_link']);
        $sql = "UPDATE test SET video_link = ? WHERE test_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $video_link, $test_id);
        $stmt->execute();
        $stmt->close();
    }

}







elseif ($action == "delete") {
    // CSRF check - MN (will implement later if a priority)
    // if (($_SERVER['REQUEST_METHOD'] === 'POST') && ($_POST['session_token'] == $_SESSION['token']))

        if ($type == "que") {

            /* update que value to users table by user id since one que is deleted */
            $test_id = trimAndClean($_POST['test_id']);
            $user_id = $loggedUser->user_id;
            $sql = "SELECT que FROM users WHERE user_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($que);
            $stmt->fetch();
            $stmt->free_result();
            $stmt->close();

            $serial_que = "";
            $que_array = array();
            if ($que != "") {
                $que_array = unserialize($que);
                $key = array_search($test_id, $que_array);
                if ($key == 0) {
                    $que_array = array_splice($que_array, 1);
                } else {
                    array_splice($que_array, $key, 1);
                }
                if (count($que_array) != 0) {
                    $serial_que = serialize($que_array);
                }
            }
            $sql = "UPDATE users SET que = ? WHERE user_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ss", $serial_que, $user_id);
            $stmt->execute();
            $stmt->close();

            $data['que'] = count($que_array);
        } elseif ($type == "test") {
            /* delete one test from test table by test id and delete its related info like questions, answers, user_test, que... */
            $test_id = trimAndClean($_POST['test_id']);

            $arr_files = array(
                "../public/upload/audio/" . $test_id,
                "../public/upload/img/" . $test_id
            );

            deleteFiles($arr_files);

            $sql = "DELETE FROM test WHERE test_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $test_id);
            $stmt->execute();
            $stmt->close();

            $sql = "DELETE FROM question WHERE test_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $test_id);
            $stmt->execute();
            $stmt->close();

            $sql = "DELETE FROM user_test WHERE test_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $test_id);
            $stmt->execute();
            $stmt->close();

            $sql = "UPDATE users SET que = ? WHERE user_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ss", $serial_que, $user_id);

            $sql = "SELECT * FROM users";
            $users = $db->queryArray($sql);
            foreach ($users as $user) {
                $que = $user['que'];
                $user_id = $user['user_id'];
                if ($que != "") {
                    $que_array = unserialize($que);
                    if (in_array($test_id, $que_array)) {
                        $key = array_search($test_id, $que_array);
                        if ($key == 0) {
                            $que_array = array_splice($que_array, 1);
                        } else {
                            array_splice($que_array, $key, 1);
                        }
                    }
                    if (count($que_array) == 0) {
                        $serial_que = "";
                    } else {
                        $serial_que = serialize($que_array);
                    }
                    $stmt->execute();
                }
            }

            $stmt->close();

        } elseif ($type == "question") {
            $test_id = trimAndClean($_POST['test_id']);
            $user_id = $loggedUser->user_id;

            $sql = "SELECT question_id FROM question WHERE test_id = ? ORDER BY question_id";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $test_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($question_id);
            $index = 0;
            while ($stmt->fetch()) {
                if ($question_id == $_POST['question_id']) {
                    break;
                } else {
                    $index++;
                }
            }
            $stmt->free_result();
            $stmt->close();

            $sql = "SELECT id, question_history FROM user_test WHERE test_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $test_id);
            $stmt->execute();
            $stmt->store_result();
            $row = $stmt->num_rows();
            $stmt->bind_result($user_test_id, $question_history);

            $sql = "UPDATE user_test SET question_history = ? WHERE id = ?";
            $stmt_1 = $db->prepare($sql);
            $stmt_1->bind_param("ss", $question_history, $user_test_id);

            if ($row != 0) {
                while ($stmt->fetch()) {
                    if ($question_history != "") {
                        $question_history = unserialize($question_history);
                        array_splice($question_history, $index, 1);
                        if (count($question_history) == 0) {
                            $question_history = "";
                        } else {
                            $question_history = serialize($question_history);
                        }
                    }
                    $stmt_1->execute();
                }
            }
            $stmt->free_result();
            $stmt->close();

            $stmt_1->close();

            $question_id = trimAndClean($_POST['question_id']);
            // delete answer_summary
            $sql = "DELETE FROM answer_summary WHERE question_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $question_id);
            $stmt->execute();
            $stmt->close();

            // delete question
            $sql = "DELETE FROM question WHERE question_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $question_id);
            $stmt->execute();
            $stmt->close();

            $sql = "UPDATE user_test SET question_start_point = 0 WHERE user_id = ? AND test_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ss", $user_id, $test_id);
            $stmt->execute();
            $stmt->close();
        }

}




//  $data['result'] is success or not
$data['result'] = $result;

// encode data which is an array and pass out
header('Content-Type: application/json');
echo json_encode($data);

exit();

?>
<?php

$result = "success";
$data = array();

$type = trimAndClean($post['type']);

if ($action == 'save') {
    if ($type == "question_intervals") {
        /* update different wait time configuration values to configs table from admin options js*/
        $miss_wait_time = trimAndClean($post['miss_wait_time']);
        $first_wait_time = trimAndClean($post['first_wait_time']);
        $second_wait_time = trimAndClean($post['second_wait_time']);
        $third_wait_time = trimAndClean($post['third_wait_time']);
        $fourth_wait_time = trimAndClean($post['fourth_wait_time']);
        $fifth_wait_time = trimAndClean($post['fifth_wait_time']);
        $sixth_wait_time = trimAndClean($post['sixth_wait_time']);

        $sql = "UPDATE config SET option_value = ? WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $option_value, $option_name);

        $option_value = $miss_wait_time;
        $option_name = 'miss_wait_time';
        $stmt->execute();

        $option_value = $first_wait_time;
        $option_name = 'first_wait_time';
        $stmt->execute();

        $option_value = $second_wait_time;
        $option_name = 'second_wait_time';
        $stmt->execute();

        $option_value = $third_wait_time;
        $option_name = 'third_wait_time';
        $stmt->execute();

        $option_value = $fourth_wait_time;
        $option_name = 'fourth_wait_time';
        $stmt->execute();

        $option_value = $fifth_wait_time;
        $option_name = 'fifth_wait_time';
        $stmt->execute();

        $option_value = $sixth_wait_time;
        $option_name = 'sixth_wait_time';
        $stmt->execute();

        $stmt->close();

    } elseif ($type == "problem_mastery") {
        /* update problem solving configuration value to configs table from admin options js*/
        $problem_mastery = trimAndClean($post['problem_mastery']);

        $sql = "UPDATE config SET option_value = ? WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $option_value, $option_name);

        $option_value = $problem_mastery;
        $option_name = 'problem_mastery';
        $stmt->execute();
        $stmt->close();
    } elseif ($type == "level_points") {
        /* update level points configuration value to configs table from admin options js */
        $level = "level_" . trimAndClean($post['level']);
        $value = trimAndClean($post['value']);
        $sql = "SELECT option_value FROM config WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $option_name);
        $option_name = 'level_points';
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        if ($option_value == "") {
            $level_array = array($level => $value);
        } else {
            $level_array = unserialize($option_value);
            $level_array = array_merge($level_array, array($level => $value));
        }
        $level_value = serialize($level_array);
        $sql = "UPDATE config SET option_value = ? WHERE option_name = 'level_points'";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $level_value);
        $stmt->execute();
        $stmt->close();

    } elseif ($type == "professor_points") {
        /** update Professor points configuration value to configs table from admin options js
         * but I don't think this function is used on admin options php because its commented
         */
        $level = "level_" . trimAndClean($post['level']);
        $value = trimAndClean($post['value']);
        $sql = "SELECT option_value FROM config WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $option_name);
        $option_name = 'professor_points';
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        if ($option_value == "") {
            $level_array = array($level => $value);
        } else {
            $level_array = unserialize($option_value);
            $level_array = array_merge($level_array, array($level => $value));
        }
        $level_value = serialize($level_array);
        $sql = "UPDATE config SET option_value = ? WHERE option_name = 'professor_points'";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $level_value);
        $stmt->execute();
        $stmt->close();

    } elseif ($type == "all_reports") {
        /* update reports email configuration value to configs table from admin options js*/
        $all_reports = trimAndClean($post['all_reports']);

        $sql = "UPDATE config SET option_value = ? WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $option_value, $option_name);

        $option_value = $all_reports;
        $option_name = 'all_reports';
        $stmt->execute();
        $stmt->close();
    } elseif ($type == "new_mastered_num") {
        /*
        $new_mastered_num = $post['new_mastered_num'];

        $sql = "UPDATE config SET option_value = ? WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $option_value, $option_name);

        $option_value = $new_mastered_num;
        $option_name = 'new_mastered_num';
        $stmt->execute();
        $stmt->close();
        */
    } elseif ($type == "report_check_interval") {
        /** update reports check interval configuration value to configs table from admin options js
         * but I don't think this function is used on admin options php because its commented
         */

        $report_check_interval = trimAndClean($post['report_check_interval']);

        $sql = "UPDATE config SET option_value = ? WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $option_value, $option_name);

        $option_value = $report_check_interval;
        $option_name = 'report_check_interval';
        $stmt->execute();
        $stmt->close();
    } elseif ($type == "memorization_mastery") {
        /* Update memorization mastery configuration value to configs table from admin options js */
        $memorization_mastery = trimAndClean($post['memorization_mastery']);

        $sql = "UPDATE config SET option_value = ? WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $option_value, $option_name);

        $option_value = $memorization_mastery;
        $option_name = 'memorization_mastery';
        $stmt->execute();
        $stmt->close();
    } elseif ($type == "time_award") {
        /* Update time_award configuration value to configs table from admin options js */
        $time_award_value = trimAndClean($post['time_award_value']);
        $option_value = $time_award_value;
        $option_name = 'time_award';

        $sql = "UPDATE config SET option_value = ? WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $option_value, $option_name);
        $stmt->execute();
        $stmt->close();

    } elseif ($type == "max_time") {
        /* Update time_award configuration value to configs table from admin options js */
        $max_time_value = trimAndClean($post['max_time_value']);
        $option_value = $max_time_value;
        $option_name = 'max_time';

        $sql = "UPDATE config SET option_value = ? WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $option_value, $option_name);
        $stmt->execute();
        $stmt->close();

    } elseif ($type == "free_days") {
        /* Update fee days configuration value to configs table from admin options js */
        $free_days = trimAndClean($post['free_days']);

        $sql = "UPDATE config SET option_value = ? WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $option_value, $option_name);

        $option_value = $free_days;
        $option_name = 'free_days';
        $stmt->execute();
        $stmt->close();
    } elseif ($type == "games") {
        /* update game configuration values to configs table from admin options js */
        $game_index = trimAndClean($post['game_index']);
        $game_name = trimAndClean($post['game_name']);
        $game_exe = trimAndClean($post['game_exe']);
        $game_link = trimAndClean($post['game_link']);
        $game_style = trimAndClean($post['game_style']);

        $sql = "SELECT option_value FROM config WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $option_name);
        $option_name = "games_defined";
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();
        $games_defined = array();
        if ($option_value != "") {
            $games_defined = unserialize($option_value);
        }

        if ($game_index == "") {
            $games_defined[count($games_defined)] = array('name' => $game_name, 'exe' => $game_exe, 'link' => $game_link, 'style' => $game_style);
        } else {
            $game_index = (int)$game_index;
            $games_defined[$game_index] = array('name' => $game_name, 'exe' => $game_exe, 'link' => $game_link, 'style' => $game_style);
        }

        $games_data = serialize($games_defined);
        $sql = "UPDATE config SET option_value = ? WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $games_data, $option_name);
        $stmt->execute();
        $stmt->close();

        $data['games'] = $games_defined;
    } elseif ($type == "compliment") {
        /* upload compliments image and audios to uploads/compliments directory from admin options js*/
        $dir_names = array();
        $dirname = "../upload/compliments";
        if (is_dir($dirname)) {
            $dir_handle = opendir($dirname);
        }
        if ($dir_handle) {
            while ($sub_dir = readdir($dir_handle)) {
                if ($sub_dir != "." && $sub_dir != ".." && is_dir($dirname . "/" . $sub_dir)) {
                    $dir_names[count($dir_names)] = $sub_dir;
                }
            }
            closedir($dir_handle);
        }
        for ($i = 1; $i < 50; $i++) {   // limit: 50
            if (!in_array($i, $dir_names)) {
                $new_dir = $i;
                mkdir($dirname . "/" . $new_dir, 0777, true);
                chmod($dirname . "/" . $new_dir, 0777);
                break;
            }
        }

        $data['new_dir'] = $new_dir;
    } elseif ($type == "audio-alert") {
        /* upload audio alerts times and audio to uploads/audio alerts directory and insert to audio alerts table from admin options js*/
        $dir_names = array();
        $dirname = "../upload/audio-alerts";
        $fileName = '';
        if (is_dir($dirname)) {
            $dir_handle = opendir($dirname);
        }

        $audioTime = 1;
        $sql = "INSERT INTO audio_alerts (audio_file, audio_time) VALUES(?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $fileName, $audioTime);
        $stmt->execute();
        $insertId = $stmt->insert_id;
        $stmt->close();

        mkdir($dirname . "/" . $insertId, 0777, true);
        chmod($dirname . "/" . $insertId, 0777);

        $data['new_dir'] = $insertId;
    } elseif ($type == "persons") {
        /* update Person Games  configuration values to configs table from admin options js */
        $person_index = trimAndClean($post['person_index']);
        $person_name = trimAndClean($post['person_name']);
        $person_exe = trimAndClean($post['person_exe']);

        $sql = "SELECT option_value FROM config WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $option_name);
        $option_name = "first_person_games";
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();
        $games_defined = array();
        if ($option_value != "") {
            $games_defined = unserialize($option_value);
        }

        if ($person_index == "") {
            $games_defined[count($games_defined)] = array('name' => $person_name, 'exe' => $person_exe);
        } else {
            $person_index = (int)$person_index;
            $games_defined[$person_index] = array('name' => $person_name, 'exe' => $person_exe);
        }

        $games_data = serialize($games_defined);
        $sql = "UPDATE config SET option_value = ? WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $games_data, $option_name);
        $stmt->execute();
        $stmt->close();

        $data['games'] = $games_defined;
    }

} elseif ($action == "get") {

    /* get level points and professor points configuration values from configs table */
    if ($type == "level_points") {
        $level = "level_" . trimAndClean($post['level']);
        $sql = "SELECT option_value FROM config WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $option_name);
        $option_name = 'level_points';
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        if ($option_value == "") {
            $result = "empty";
        } else {
            $level_array = unserialize($option_value);
            if (isset($level_array[$level])) {
                $data['value'] = $level_array[$level];
            } else {
                $result = "empty";
            }
        }
    } elseif ($type == "professor_points") {
        $level = "level_" . trimAndClean($post['level']);
        $sql = "SELECT option_value FROM config WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $option_name);
        $option_name = 'professor_points';
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        if ($option_value == "") {
            $result = "empty";
        } else {
            $level_array = unserialize($option_value);
            if (isset($level_array[$level])) {
                $data['value'] = $level_array[$level];
            } else {
                $result = "empty";
            }
        }
    }
} elseif ($action == "delete") {

    /**
     * delete different configuration values from config values
     * Actually its updating config table that means remove one value from array and save with updated value
     *  to configs table except compliments, its just removing directory itself from compliments directory
     *
     */
    if ($type == "games") {
        $game_index = trimAndClean($post['delete_index']);

        $sql = "SELECT option_value FROM config WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $option_name);
        $option_name = "games_defined";
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();
        $games_defined = array();
        if ($option_value != "") {
            $games_defined = unserialize($option_value);
        }

        $game_index = (int)$game_index;
        array_splice($games_defined, $game_index, 1);

        if (count($games_defined) == 0) {
            $games_data = "";
        } else {
            $games_data = serialize($games_defined);
        }

        $sql = "UPDATE config SET option_value = ? WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $games_data, $option_name);
        $stmt->execute();
        $stmt->close();

        $data['games'] = $games_defined;
    } elseif ($type == "compliment") {
        $dir = trimAndClean($post['delete_dir']);
        $dirname = "../upload/compliments/$dir";
        deleteDirectory($dirname);
    } elseif ($type == "persons") {
        $game_index = trimAndClean($post['delete_index']);

        $sql = "SELECT option_value FROM config WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $option_name);
        $option_name = "first_person_games";
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();
        $games_defined = array();
        if ($option_value != "") {
            $games_defined = unserialize($option_value);
        }

        $game_index = (int)$game_index;
        array_splice($games_defined, $game_index, 1);

        if (count($games_defined) == 0) {
            $games_data = "";
        } else {
            $games_data = serialize($games_defined);
        }

        $sql = "UPDATE config SET option_value = ? WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $games_data, $option_name);
        $stmt->execute();
        $stmt->close();

        $data['games'] = $games_defined;
    } else if ($type == 'audio-alert') {
        $objectId = trimAndClean($post['object_id']);
        $sql = "DELETE FROM audio_alerts WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $objectId);
        $stmt->execute();
        $stmt->close();
    }
}

$data['result'] = $result;

header('Content-Type: application/json');
echo json_encode($data);

exit();

?>
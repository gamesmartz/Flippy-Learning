<?php

$result = "success";
$data = array();

$test_id = trimAndClean($_POST['test_id']);

/* get user subscription by current logged in id */
$sql = "SELECT subscription, reports_time FROM users WHERE user_id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $user_id);

$user_id = $loggedUser->user_id;
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($subscription, $reports_time);
$stmt->fetch();
$stmt->free_result();
$stmt->close();

if ($subscription != 0) {

    //-------- make report_image ---------------
    $directory = dirname(dirname(__FILE__));

    // java import
    //define ('BATIK_PATH', $directory.'/batik/batik-rasterizer.jar');

    ini_set('magic_quotes_gpc', 'off');

    $type = trimAndClean($_POST['type']);
    $svg = (string)$_POST['question_correct_svg'];
    $filename = "question_chart_";

    // prepare variables
    if (!$filename or !preg_match('/^[A-Za-z0-9\-_ ]+$/', $filename)) {
        $filename = 'chart';
    }
    $svg = stripslashes($svg);
    
    // check for malicious attack in SVG
    if (strpos($svg, "<!ENTITY") !== false || strpos($svg, "<!DOCTYPE") !== false) {
        exit("Execution is topped, the posted SVG could contain code for a malicious attack");
    }

    $question_tempName = $filename . rbGenerateRandom(8);

    // allow no other than predefined types
    if ($type == 'image/png') {
        $typeString = '-m image/png';
        $ext = 'png';

    } elseif ($type == 'image/jpeg') {
        $typeString = '-m image/jpeg';
        $ext = 'jpg';

    } elseif ($type == 'application/pdf') {
        $typeString = '-m application/pdf';
        $ext = 'pdf';

    } elseif ($type == 'image/svg+xml') {
        $ext = 'svg';

    } else { // prevent fallthrough from global variables
        $ext = 'txt';
    }

    $test_path = "../public/upload/report/" . $user_id . "/";
    if (!file_exists($test_path)) {
        mkdir($test_path, 0777, true);
        chmod($test_path, 0777);
    }

    if (file_put_contents($test_path . $question_tempName . ".svg", $svg)) {
        $question_report = $question_tempName . ".svg";
    } else {
        die("Couldn't create file. Check that the permissions for the directory are set to 777.");
    }

    /*
    $outfile = $directory."/upload/report/".$user_id."/$question_tempName.$ext";

    if (isset($typeString)) {

        // size
        $width = '';
        if ($_POST['width']) {
            $width = (int)$_POST['width'];
            if ($width) $width = "-w $width";
        }

        // generate the temporary file
        if (!file_put_contents("../upload/report/$question_tempName.svg", $svg)) {
            die("Couldn't create temporary file. Check that the directory permissions for
                the /temp directory are set to 777.");
        }

        // do the conversion
        $output = shell_exec("java -jar ". BATIK_PATH ." $typeString -d $outfile $width ".$directory."/upload/report/$question_tempName.svg");

        // delete it
        unlink("../upload/report/$question_tempName.svg");

        $question_report = $question_tempName.".".$ext;

    }
    */

    /*
    if($_GET['action'] == "mastered"){

        $svg = (string) $_POST['pre_post_svg'];
        $filename = "pre_post_chart_";

        // prepare variables
        if (!$filename or !preg_match('/^[A-Za-z0-9\-_ ]+$/', $filename)) {
            $filename = 'chart';
        }
        if (get_magic_quotes_gpc()) {
            $svg = stripslashes($svg);
        }

        // check for malicious attack in SVG
        if(strpos($svg,"<!ENTITY") !== false || strpos($svg,"<!DOCTYPE") !== false){
            exit("Execution is topped, the posted SVG could contain code for a malicious attack");
        }

        $post_tempName = $filename. RB_generateRandom(8);

        $test_path = "../upload/report/".$user_id."/";
        if(!file_exists($test_path)){
            mkdir($test_path,0777,true);
            chmod($test_path, 0777);
        }

        $outfile = $directory."/upload/report/".$user_id."/$post_tempName.$ext";

        if (isset($typeString)) {

            // size
            $width = '';
            if ($_POST['width']) {
                $width = (int)$_POST['width'];
                if ($width) $width = "-w $width";
            }

            // generate the temporary file
            if (!file_put_contents("../upload/report/$post_tempName.svg", $svg)) {
                die("Couldn't create temporary file. Check that the directory permissions for
                    the /temp directory are set to 777.");
            }

            // do the conversion
            $output = shell_exec("java -jar ". BATIK_PATH ." $typeString -d $outfile $width ".$directory."/upload/report/$post_tempName.svg");

            // delete it
            unlink("../upload/report/$post_tempName.svg");

            $pre_post_report = $post_tempName.".".$ext;

        }
    }elseif($_GET['action'] == "stop"){

        $pre_post_report = "";

    }
    */
    $pre_post_report = "";

    // ---------------- make end ---------------------------

    $reports_time = unserialize($reports_time);

    $timezone = $reports_time[2];

//        switch ($reports_time[2]) {
//            case "PST":
//                $timezone = "Etc/GMT+8";
//                break;
//            case "MST":
//                $timezone = "Etc/GMT+7";
//                break;
//            case "CST":
//                $timezone = "Etc/GMT+6";
//                break;
//            case "EST":
//                $timezone = "Etc/GMT+5";
//                break;
//            case "UTC+0":
//                $timezone = "Etc/GMT-0";
//                break;
//            case "UTC+1":
//                $timezone = "Etc/GMT-1";
//                break;
//            case "UTC+2":
//                $timezone = "Etc/GMT-2";
//                break;
//            case "UTC+3":
//                $timezone = "Etc/GMT-3";
//                break;
//            case "UTC+4":
//                $timezone = "Etc/GMT-4";
//                break;
//            case "UTC+5":
//                $timezone = "Etc/GMT-5";
//                break;
//            case "UTC+6":
//                $timezone = "Etc/GMT-6";
//                break;
//            case "UTC+7":
//                $timezone = "Etc/GMT-7";
//                break;
//            case "UTC+8":
//                $timezone = "Etc/GMT-8";
//                break;
//            case "UTC+9":
//                $timezone = "Etc/GMT-9";
//                break;
//            case "UTC+10":
//                $timezone = "Etc/GMT-10";
//                break;
//            case "UTC+11":
//                $timezone = "Etc/GMT-11";
//                break;
//            case "UTC+12":
//                $timezone = "Etc/GMT-12";
//                break;
//            case "UTC-1":
//                $timezone = "Etc/GMT+1";
//                break;
//            case "UTC-2":
//                $timezone = "Etc/GMT+2";
//                break;
//            case "UTC-3":
//                $timezone = "Etc/GMT+3";
//                break;
//            case "UTC-4":
//                $timezone = "Etc/GMT+4";
//                break;
//            case "UTC-5":
//                $timezone = "Etc/GMT+5";
//                break;
//            case "UTC-6":
//                $timezone = "Etc/GMT+6";
//                break;
//            case "UTC-7":
//                $timezone = "Etc/GMT+7";
//                break;
//            case "UTC-8":
//                $timezone = "Etc/GMT+8";
//                break;
//            case "UTC-9":
//                $timezone = "Etc/GMT+9";
//                break;
//            case "UTC-10":
//                $timezone = "Etc/GMT+10";
//                break;
//            case "UTC-11":
//                $timezone = "Etc/GMT+11";
//                break;
//            default:
//                $timezone = "Etc/GMT+8";
//        }

    /* get user date */
    /*$date = new DateTime("now", new DateTimeZone($timezone));*/
    $date = new DateTime("now");
    $current_date = $date->format("n-j-y g:iA");
    $current_date_array = explode(" ", $current_date);

    /* get info from cron email by current logged in id */
    $sql = "SELECT id, question_report, pre_post_report, report_date FROM cron_email WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $user_id);

    $stmt->execute();
    $stmt->store_result();
    $row = $stmt->num_rows();
    $stmt->bind_result($cron_email_id, $cron_email_question_report, $cron_email_pre_post_report, $report_date);
    $stmt->fetch();
    $stmt->free_result();
    $stmt->close();

    /* insert/update cron email info by current logged in id to make report */
    if ($row != 0) {
        $report_date_array = explode(" ", $report_date);


        $sql = "UPDATE 
                        cron_email 
                    SET
                        test_id = ?, 
                        report_date = ?, 
                        correct_answer_num = ?,
                        total_answer_num = ?, 
                        question_report = ?, 
                        pre_post_report = ? 
                    WHERE 
                        id = ?
                    ";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sssssss", $test_id, $current_date, $correct_answer_num, $total_answer_num, $question_report, $pre_post_report, $cron_email_id);

        $correct_answer_num = trimAndClean($_POST['correct_answered']);
        $total_answer_num = trimAndClean($_POST['total_answered']);
        $stmt->execute();
        $stmt->close();

        if ($cron_email_question_report != "") {
            unlink("../public/upload/report/" . $user_id . "/" . $cron_email_question_report);
        }
        if ($cron_email_pre_post_report != "") {
            unlink("../public/upload/report/" . $user_id . "/" . $cron_email_pre_post_report);
        }
        // determines when the email gets sent - right now if the days are different (new day) it sends
        if ($report_date_array[0] != $current_date_array[0])
            require_once("../ajax/async-send-report-email.php");
    } else {
        $sql = "INSERT INTO 
                    cron_email (user_id,
                                test_id,
                                report_date,
                                correct_answer_num,
                                total_answer_num,
                                question_report,
                                pre_post_report
                                ) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                    ";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sssssss", $user_id, $test_id, $current_date, $correct_answer_num, $total_answer_num, $question_report, $pre_post_report);

        $correct_answer_num = trimAndClean($_POST['correct_answered']);
        $total_answer_num = trimAndClean($_POST['total_answered']);
        $stmt->execute();
        $stmt->close();

        require_once("../ajax/async-send-report-email.php");

    }

}

$data['result'] = $result;

header('Content-Type: application/json');
echo json_encode($data);

?>

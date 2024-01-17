<?php

/* check call method is POST */
if(isset($post)){
    
    $sub_directory = $post['audio_alert_id'];
    $old_file = $post['aa_old_file'];
    $type = $post['type'];
    if ($type == 'audio-time') {
        $audioTime = $post['audio_time'];
        $sql = "UPDATE audio_alerts SET audio_time = ? WHERE id = ?";

        $stmt = $db->prepare($sql);
        $stmt->bind_param("si", $audioTime, $sub_directory);
        $stmt->execute();
        $stmt->close();
        echo $audioTime;
    } else {
        $path = "../public/upload/audio-alerts/$sub_directory/";
        if (!file_exists($path)) {
            //error_reporting(-1); // ALL messages
            mkdir($path, 0777, true);
            chmod($path, 0777);
        }

        $valid_formats = array("mp3", "MP3");

        /* get posted file data and size */
        $name = $_FILES['audio_alert_upload']['name'];
        $size = $_FILES['audio_alert_upload']['size'];

        /* check upload file is mp3 validated and size  */
        if (strlen($name)) {
            $name_array = explode(".", $name);
            $ext = end($name_array);
            $size_byte = round($size / 1024);

            if (in_array($ext, $valid_formats) && $size_byte < 5000) {
                $tmp = $_FILES['audio_alert_upload']['tmp_name'];
                if (move_uploaded_file($tmp, $path . $name)) {
                    if ($name != $old_file && $old_file != "") {
                        unlink($path . $old_file);
                    }
                    if ($type == 'img') {
                        $sql = "UPDATE audio_alerts SET img_file = ? WHERE id = ?";
                    } else
                        $sql = "UPDATE audio_alerts SET audio_file = ? WHERE id = ?";

                    $stmt = $db->prepare($sql);
                    $stmt->bind_param("si", $name, $sub_directory);
                    $stmt->execute();
                    $stmt->close();

                    echo "/upload/audio-alerts/$sub_directory/$name";
                } else {
                    echo "failed";
                }
            } else
                echo "failed";
        } else
            echo "failed";
    }
    exit;
}
?>

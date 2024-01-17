<?php
use Illuminate\Support\Facades\DB;

$result = "success";
$data = array();

$type = $post['type'];

if ($action == 'add') {

    if ($type == "video") {
        /* add new video to video table with video link, author id(=user id which is logged in) */
        $user_id = $loggedUser->user_id;
        $test_id = $post['test_id'];
        $video_title = $post['video_title'];
        $video_link = $post['video_link'];

        $sql = "INSERT INTO video (test_id, author_id, video_title, video_link, submitted_time) VALUES(?, ?, ?, ?, NOW())";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssss", $test_id, $user_id, $video_title, $video_link);
        $stmt->execute();
        $video_id = $stmt->insert_id;
        $stmt->close();

        /* get current logged in user role  */
        $sql = "SELECT user_role FROM users WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_role);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        $data['user_role'] = $user_role;

    } elseif ($type == "vote") {
        /**
         * video likes + 1 by current logged in user id from watch learn js
         * if data with user id already exists in user_video, only update like + 1,
         * not insert new row with user id to user_video
        */
        $user_id = $loggedUser->user_id;
        $video_id = $post['video_id'];
        $option = $post['option'];

        $sql = "SELECT id FROM user_video WHERE video_id = ? AND user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $video_id, $user_id);
        $stmt->execute();
        $stmt->store_result();
        $row = $stmt->num_rows();
        $stmt->bind_result($user_video_id);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        /* check if records exists */
        if ($row != 0) {
            if ($option == "likes") {
                $sql = "UPDATE user_video SET likes = likes + 1 WHERE id = ?";
            } else if ($option == "dislikes") {
                $sql = "UPDATE user_video SET dislikes = dislikes + 1 WHERE id = ?";
            }
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $user_video_id);
            $stmt->execute();
            $stmt->close();
        } else {
            if ($option == "likes") {
                $sql = "INSERT INTO user_video (video_id, user_id, likes) VALUES (?, ?, 1)";
            } else if ($option == "dislikes") {
                $sql = "INSERT INTO user_video (video_id, user_id, dislikes) VALUES (?, ?, 1)";
            }
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ss", $video_id, $user_id);
            $stmt->execute();
            $stmt->close();
        }

        /* get current logged in user role  */
        $sql = "SELECT user_role FROM users WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_role);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        /* get total likes and dislikes from user_video by this video */
        $sql = "SELECT sum(likes), sum(dislikes) FROM user_video WHERE video_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $video_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($likes, $dislikes);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        $data['user_role'] = $user_role;
        $data['likes'] = $likes;
        $data['dislikes'] = $dislikes;

    } else if ($type == "view_video") {
        /* insert video play time when video ends */
        $video_id = $post['video_id'];
        $view_time = $post['view_time'];
        $user_id = $loggedUser->user_id;

        /* get video length by video id */
        $sql = "SELECT TIME_TO_SEC(length) FROM video WHERE video_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $video_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($length);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        /* get watch time from user video by user id and video id */
        $sql = "SELECT id, TIME_TO_SEC(watch_time) FROM user_video WHERE video_id = ? AND user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $video_id, $user_id);
        $stmt->execute();
        $stmt->store_result();
        $row = $stmt->num_rows();
        $stmt->bind_result($user_video_id, $watch_time);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        /* insert or update watch_time to user_video with video id and user id */
        if ($row == 0) {
            $watch_time = $view_time;
            if ($watch_time > $length) {
                $watch_time = $length;
            }
            $sql = "INSERT INTO user_video (video_id, user_id, watch_time) VALUES (?, ?, SEC_TO_TIME(?))";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("sss", $video_id, $user_id, $watch_time);
            $stmt->execute();
            $stmt->close();
        } else {
            $watch_time += $view_time;
            if ($watch_time > $length) {
                $watch_time = $length;
            }
            $sql = "UPDATE user_video SET watch_time = SEC_TO_TIME(?) WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ss", $watch_time, $user_video_id);
            $stmt->execute();
            $stmt->close();
        }

        $watch_time = date("i:s", $watch_time);
        $watch_time = (substr($watch_time, 0, 1) == "0" ? substr($watch_time, 1) : $watch_time);
        $data['watch_time'] = $watch_time;
    }

} elseif ($action == "get") {

    if ($type == "video") {
        /* get video info from video table by video id(primary id)  */
        $video_id = $post['video_id'];

        $sql = "SELECT video_title, video_link FROM video WHERE video_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $video_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($video_title, $video_link);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        $data['video_title'] = $video_title;
        $data['video_link'] = $video_link;
    } elseif ($type == "public") {

        /* get only title from video table by video id(primary id)  */
        $video_id = $post['video_id'];

        $sql = "SELECT public FROM video WHERE video_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $video_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($public);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        $data['public'] = $public;
    } elseif ($type == "length") {
        /* get only video length from video table by video id(primary id)  */
        $video_id = $post['video_id'];

        $sql = "SELECT length FROM video WHERE video_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $video_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($length);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        $length_arr = explode(":", $length);
        $data['minutes'] = intval($length_arr[1]);
        $data['seconds'] = intval($length_arr[2]);
    } elseif ($type == "admin_category") {
        /**
         * get videos list with all related information by grade filter type and subject id
         * here including save search history to users table
         */
        $filter_type = $post['filter_type'];
        $subject_id = $post['subject_id'];

        $video_history = array('filter' => $filter_type, 'subject_id' => $subject_id);
        $video_history = serialize($video_history);
        $user_id = $loggedUser->user_id;

        /* save search history to users table to video_history column */
        if ($subject_id == "") {
            $sql = "UPDATE users SET video_history = '' WHERE user_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $stmt->close();
        } else {
            $sql = "UPDATE users SET video_history = ? WHERE user_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ss", $video_history, $user_id);
            $stmt->execute();
            $stmt->close();
        }
        /* get video info with grade name by subject id */
        if ($subject_id == "") {
            /* get all videos exists in test and subject when subject id is empty*/
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
        } else {
            if ($subject_id == "all") {
                /* get videos exists in test and subject by grade id when subject id is all */
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
                /* get videos exists in test and subject by grade id subject id */
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
        }

        $video = DB::select($sql);
        if ($video) :
            $video = json_decode(json_encode($video), true);
        endif;

    } elseif ($type == "sort-admin-videos") {
        /* same like above case(admin_category)  */
        $filter_type = $post['filter_type'];
        $subject_id = $post['subject_id'];
        $sort = $post['sort'];

        if ($sort == "date submitted") {
            if ($subject_id == "") {
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
            } else {
                if ($subject_id == "all") {
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
            }

        } elseif ($sort == "grade - subject") {
            /* same like above case(admin_category) but difference is that order by subject name ASC */
            if ($subject_id == "") {
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
                              ) video_test
                           JOIN subject
                           ON video_test.subject_id = subject.subject_id
                        ) video_subject
                    JOIN grade
                    ON video_subject.grade_id = grade.grade_id
                    ORDER BY grade.grade_id, video_subject.subject_name ASC
                ";
            } else {
                if ($subject_id == "all") {
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
                          ) video_subject
                       JOIN grade
                       ON video_subject.grade_id = grade.grade_id
                       ORDER BY grade.grade_id, video_subject.subject_name ASC
                    ";
                } else {
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
                          ) video_subject
                       JOIN grade
                       ON video_subject.grade_id = grade.grade_id
                       ORDER BY grade.grade_id, video_subject.subject_name ASC
                    ";
                }
            }

        } elseif ($sort == "name a-z") {
            /* same like above case(admin_category) but difference is that order by video title ASC */
            if ($subject_id == "") {
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
                              ) video_test
                           JOIN subject
                           ON video_test.subject_id = subject.subject_id
                        ) video_subject
                    JOIN grade
                    ON video_subject.grade_id = grade.grade_id
                    ORDER BY video_subject.video_title ASC
                ";
            } else {
                if ($subject_id == "all") {
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
                          ) video_subject
                       JOIN grade
                       ON video_subject.grade_id = grade.grade_id
                       ORDER BY video_subject.video_title ASC
                    ";
                } else {
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
                          ) video_subject
                       JOIN grade
                       ON video_subject.grade_id = grade.grade_id
                       ORDER BY video_subject.video_title ASC
                    ";
                }
            }

        }

        $video = DB::select($sql);
        if ($video) :
            $video = json_decode(json_encode($video), true);
        endif;

    }

    /* redefine videos as array */
    if (isset($video)) {

        for ($i = 0; $i < count($video); $i++) {

            $video[$i]['submitted_time'] = date("m-d-y", strtotime($video[$i]['submitted_time']));

            if ($video[$i]['video_link'] != "") {
                $pattern = '%(?:youtube\.com/(?:user/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';
                preg_match($pattern, $video[$i]['video_link'], $matches);
                $video[$i]['video_link'] = isset($matches[1]) ? $matches[1] : "";
            }
            $video[$i]['grade_subject'] = $video[$i]['grade_name'] . " - " . $video[$i]['subject_name'];
        }

        $data['video'] = $video;
    }

} elseif ($action == "edit") {

    if ($type == "video") {
        /* update one whole row of  video table by primary id  */
        $video_id = $post['video_id'];
        $video_title = $post['video_title'];
        $video_link = $post['video_link'];

        $sql = "UPDATE video SET video_title = ?, video_link = ? WHERE video_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sss", $video_title, $video_link, $video_id);
        $stmt->execute();
        $stmt->close();

    } elseif ($type == "public") {
        /* update one public of video table by primary id  */
        $video_id = $post['video_id'];
        $public_value = $post['public_value'];

        $sql = "UPDATE video SET public = ? WHERE video_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $public_value, $video_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($type == "length") {
        /* update one length of video table by primary id  */
        $video_id = $post['video_id'];
        $length = $post['length'];

        $sql = "UPDATE video SET length = SEC_TO_TIME(?) WHERE video_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $length, $video_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($type == "lock_account") {
        /**
         * lock user by user id
         * First get user id from video by video_id
         * and update users table as lock(user_status) column
         */
        $video_id = $post['video_id'];
        $lock_value = $post['lock_value'];

        $sql = "SELECT author_id FROM video WHERE video_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $video_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($author_id);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        $sql = "UPDATE users SET user_status = ? WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $lock_value, $author_id);
        $stmt->execute();
        $stmt->close();
    }

} elseif ($action == "delete") {

    if ($type == "video") {
        /* delete one row from video table by primary id */
        $video_id = $post['video_id'];

        $sql = "DELETE FROM video WHERE video_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $video_id);
        $stmt->execute();
        $stmt->close();

        $sql = "DELETE FROM user_video WHERE video_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $video_id);
        $stmt->execute();
        $stmt->close();

    }

}

$data['result'] = $result;

header('Content-Type: application/json');
echo json_encode($data);

exit();

?>
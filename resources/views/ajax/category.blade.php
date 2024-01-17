<?php
use Illuminate\Support\Facades\DB;

$result = "success";
$data = array();

$type = trimAndClean($post['type']);

if ($action == 'add') {

    if ($type == "grade") {
        /* add grade from admin options js but I don't think this function is used on admin-options php*/
        $grade = trimAndClean($post['grade']);
        $sql = "INSERT INTO grade (grade_name) VALUES(?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $grade);
        $stmt->execute();
        $stmt->close();
    } elseif ($type == "subject") {
        /* add subject with grade id from admin options js but I don't think this function is used on admin-options php*/
        $grade = trimAndClean($post['grade']);
        $subject = trimAndClean($post['subject']);
        $sql = "INSERT INTO subject (subject_name,grade_id) VALUES(?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $subject, $grade);
        $stmt->execute();
        $stmt->close();
    } elseif ($type == "sub-category") {
        /* add sub category with subject from admin options js but I don't think this function is used on admin-options php*/
        $subject = trimAndClean($post['subject']);
        $sub_category = trimAndClean($post['sub_category']);
        $sql = "INSERT INTO sub_category (name,subject_id) VALUES(?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $sub_category, $subject);
        $stmt->execute();
        $stmt->close();
    }

} elseif ($action == 'get') {

    if ($type == "subject") {
        $grade = trimAndClean($post['grade']);
        /**
         * get all current logged in user's subjects - only get data from subjects table from functions js
         * in case of grade == history matching user_tests user_id equals by current user's id value
         * and in case of grade == personal tests author_id equals by current user's id value
         * and in case of grade == grade id get only subjects matching by grade_id == $grade(post value
        */
        if ($grade == "history") {
            $user_id = $loggedUser->user_id;
            $sql = "
                SELECT subject.*
                FROM subject
                JOIN (        
                  SELECT test.*
                  FROM test
                  JOIN user_test
                  ON test.test_id = user_test.test_id
                  WHERE user_test.user_id = '$user_id' AND user_test.test_info != ''
                  GROUP BY test.test_id
                  ) test_subject
                ON subject.subject_id = test_subject.subject_id
                GROUP BY subject.subject_id
                ORDER BY subject.subject_id ASC
              ";
        } elseif ($grade == "personal") {
            $user_id = $loggedUser->user_id;
            $sql = "
                    SELECT subject.*
                    FROM subject
                    JOIN test
                    ON subject.subject_id = test.subject_id
                    WHERE test.author_id = '$user_id' AND test.public = 0
                    GROUP BY subject.subject_id
                    ORDER BY subject.subject_id ASC
                ";
        } else {
            if (isset($loggedUser->user_role)){
                if ($loggedUser->user_role == 1) {
                    $sql = "SELECT * FROM subject WHERE grade_id = '$grade'";
                } else {
                    $sql = "SELECT * FROM subject WHERE grade_id = '$grade' AND view_option = 1";
                }
            } else {
                $sql = "SELECT * FROM subject WHERE grade_id = '$grade' AND view_option = 1";
            }
        }
        $subject = DB::select($sql);
        if ($subject) :
            $subject = json_decode(json_encode($subject), true);
        endif;
        $data['subject'] = $subject;
    } elseif ($type == "sub-category") {
        /* get sub categories by category id but I don't think this function is used */
        $subject = trimAndClean($post['subject']);
        $sql = "SELECT * FROM sub_category WHERE subject_id = '$subject'";
        $sub_category = DB::select($sql);
        if ($sub_category) :
            $sub_category = json_decode(json_encode($sub_category), true);
        endif;
        $data['sub_category'] = $sub_category;
    }

} elseif ($action == "delete") {

    if ($type == "grade") {
        /* delete one row from grade table by primary id but I don't think this function is used */
        $grade = trimAndClean($post['grade']);
        $sql = "DELETE  FROM grade WHERE grade_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $grade);
        $stmt->execute();
        $stmt->close();
    } elseif ($type == "subject") {
        /* delete one row from subjects table by primary id but I don't think this function is used */
        $subject = trimAndClean($post['subject']);
        $sql = "DELETE FROM subject WHERE subject_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $subject);
        $stmt->execute();
        $stmt->close();
    } elseif ($type == "sub-category") {
        /* delete one row from sub_category table by primary id but I don't think this function is used */
        $sub_category = trimAndClean($post['sub_category']);
        $sql = "DELETE FROM sub_category WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $sub_category);
        $stmt->execute();
        $stmt->close();
    }

}

$data['result'] = $result;

header('Content-Type: application/json');
echo json_encode($data);

exit();

?>
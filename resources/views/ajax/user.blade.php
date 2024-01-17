<?php
use Illuminate\Support\Facades\DB;
// result returns success by default
$result = "success";

// default data array creation
$data = array();


// if user id is passed in, set it
if (isset($post['user_id'])) {
    $user_id = $post['user_id'];
}


// NOT USED
///////////////////UPDATE SUBSCRIPTION///////////////////UPDATE SUBSCRIPTION///////////////////UPDATE SUBSCRIPTION///////////////////UPDATE SUBSCRIPTION
if ($action == "subscription") {
    /* update subscription to users table current logged in id  */
    $subscription = $post['subscription'];
    $sql = "UPDATE users SET subscription = ? WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $subscription, $user_id);
    $stmt->execute();
    $stmt->close();
}

///////////////////UPDATE STATUS///////////////////UPDATE STATUS///////////////////UPDATE STATUS///////////////////UPDATE STATUS///////////////////UPDATE STATUS
elseif ($action == "status") {
    /* update user_status to users table current logged in id(primary id)  */
    $user_status = $post['user_status'];
    $sql = "UPDATE users SET user_status = ? WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $user_status, $user_id);
    $stmt->execute();
    $stmt->close();
}

// RARELY USED
///////////////////UPDATE ROLE///////////////////UPDATE ROLE///////////////////UPDATE ROLE///////////////////UPDATE ROLE///////////////////UPDATE ROLE
elseif ($action == "role") {
    /* update user_role to users table current logged in id(primary id)  */
    $user_role = $post['user_role'];
    $sql = "UPDATE users SET user_role = ? WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $user_role, $user_id);
    $stmt->execute();
    $stmt->close();
}


///////////////////GET DB INFO - SORT///////////////////GET DB INFO - SORT///////////////////GET DB INFO - SORT///////////////////GET DB INFO - SORT///////////////////GET DB INFO - SORT
elseif ($action == "get") {
    /**
     * get different information by post data <type> from users table by current logged in id(primary id
     * for example type = "subscription" then get subscription
     */
    if (!isset($user_id)) {
        $user_id = $loggedUser->user_id;
    }
    $sql = "SELECT * FROM users WHERE user_id = '$user_id'";
    $user = DB::select($sql);
    if ($user) :
        $user = json_decode(json_encode($user), true);
    endif;
    $type = $post['type'];
    if ($type == "subscription") {
        $data['subscription'] = $user[0]['subscription'];
    } elseif ($type == "status") {
        $data['status'] = $user[0]['user_status'];
    } elseif ($type == "role") {
        $data['role'] = $user[0]['user_role'];
    } elseif ($type == "sort-users") {
        /**
         * get all users list from users table by different sort option
         * for example $sort == level then order by level DESC
         */
        $sort = $post['sort'];
        if ($sort == "last login") {
            $sql = "SELECT * FROM users ORDER BY login_time DESC ";
        } elseif ($sort == "date joined") {
            $sql = "SELECT * FROM users ORDER BY user_registered_time ASC ";
        } elseif ($sort == "level") {
            $sql = "SELECT * FROM users ORDER BY level DESC";
        } elseif ($sort == "email a-z") {
            $sql = "SELECT * FROM users ORDER BY email ASC";
        } elseif ($sort == "name a-z") {
            $sql = "SELECT * FROM users ORDER BY name ASC";
        } elseif ($sort == "admin") {
            $sql = "SELECT * FROM users WHERE user_role = 1";
        }
        $users = DB::select($sql);
        if ($users) :
            $users = json_decode(json_encode($users), true);
        endif;
        for ($i = 0; $i < count($users); $i++) {
            $users[$i]['user_registered_time'] = date("m-d-y", strtotime($users[$i]['user_registered_time']));
            $users[$i]['login_time'] = date("m-d-y", strtotime($users[$i]['login_time']));
            $users[$i]['user_email'] = $users[$i]['email'];
            $users[$i]['user_name'] = $users[$i]['name'];
        }
        $data['user'] = $users;
    } elseif ($type == "leader") {
        $data['show_leader'] = $user[0]['show_leader'];
    }
}


///////////////////CANCEL SUB///////////////////CANCEL SUB///////////////////CANCEL SUB///////////////////CANCEL SUB///////////////////CANCEL SUB
elseif ($action == "credit") {
    /* Braintree Payment by current logged in id */

    /* first set subscription for current user */
    $sql = "UPDATE users SET subscription = 0 WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $user_id = $loggedUser->user_id;
    $stmt->execute();
    $stmt->close();

    /* get subscription for form subscription table by curent logged in user_id */
    $sql = "SELECT subscription_id FROM subscription WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $stmt->store_result();
    $row = $stmt->num_rows();
    $stmt->bind_result($subscription_id);
    $stmt->fetch();
    $stmt->free_result();
    $stmt->close();

    if ($row != 0) {
        // Finally cancel the subscription
        //require_once "../braintree/lib/Braintree.php";

        //Braintree_Configuration::environment("sandbox");
        //Braintree_Configuration::merchantId("zmkhgrtfpzs3k6n4");
        //Braintree_Configuration::publicKey("p5wvzkqnmz8m3m24");
        //Braintree_Configuration::privateKey("8f97d31d547eaa2bf4beefebdf1bf30e");

        //$subscription_result = Braintree_Subscription::cancel($subscription_id);

        if ($subscription_result->success) {
            $sql = "DELETE FROM subscription WHERE user_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $stmt->close();

        } else {
            $result = "failed";
            foreach ($subscription_result->errors->deepAll() as $error) {
                echo($error->code . ": " . $error->message . "<br />");
            }
        }
    }
}


///////////////////REPORTS TIME///////////////////REPORTS TIME///////////////////REPORTS TIME///////////////////REPORTS TIME///////////////////REPORTS TIME///////////////////REPORTS TIME
elseif ($action == "reports") {
    $type = $post['type'];
    if ($type == "submit") {
        /* update extra info of users table by logged in primary id  from extra js */
        /* reports time[0] is no longer needed */
        $reports_email = trimAndClean($post['reports_email']);
        $reports_email = filter_var($reports_email, FILTER_SANITIZE_EMAIL);

        $reports_time = $post['reports_time'];
        $reports_option = $post['reports_option'];

        $reports_phone = trimAndClean($post['reports_phone']);
        $country_code = trimAndClean($post['country_code']);

        $reports_interval = $post['reports_interval'];

        $reports_time = serialize($reports_time);
        $reports_option = serialize($reports_option);

        $sql = "UPDATE users SET reports_email = ?, reports_time = ?, reports_option = ?, reports_phone = ?, country_code = ?, reports_interval = ? WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sssssss", $reports_email, $reports_time, $reports_option, $reports_phone, $country_code, $reports_interval, $user_id);
        $user_id = $loggedUser->user_id;
        $stmt->execute();
        $stmt->close();
    }

///////////////////UPDATE REPORT INFO///////////////////UPDATE REPORT INFO///////////////////UPDATE REPORT INFO///////////////////UPDATE REPORT INFO///////////////////UPDATE REPORT INFO
elseif ($type == "save_change") {
        /* update extra info of users table by logged in primary id  from extra js same as above */
       // $reports_email = trimAndClean($post['reports_email']);
       // $reports_email = filter_var($reports_email, FILTER_SANITIZE_EMAIL);
       // $reports_time = $post['reports_time'] ;

        $reports_option = $post['reports_option'] ;
        $reports_phone = trimAndClean($post['reports_phone']);
       // $country_code = trimAndClean($post['country_code']);

        $reports_interval = trimAndClean($post['reports_interval']) ;

       // $reports_time = serialize($reports_time);
        $reports_option = serialize($reports_option);

        $sql = "UPDATE users SET  reports_option = ?, reports_phone = ?, reports_interval = ? WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssss",  $reports_option, $reports_phone, $reports_interval, $user_id);
        $user_id = $loggedUser->user_id;
        $stmt->execute();
        $stmt->close();
    }
}

///////////////////UNSUB///////////////////UNSUB///////////////////UNSUB///////////////////UNSUB///////////////////UNSUB///////////////////UNSUB
elseif ($action == "unsubscribe") {
    /* update reports email of users table by logged in primary id from unsubscribe.js  */

    /* first check md5 email equals with reports email from users table by primary id  */
    $md5_email = $post['md5_email'];
    $sql = "SELECT reports_email FROM users WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($reports_email);
    $stmt->fetch();
    $stmt->free_result();
    $stmt->close();

    if ($md5_email == md5($reports_email)) {
        $reports_email = "";
    }

    /* update reports email */
    $sql = "UPDATE users SET reports_email = ? WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $reports_email, $user_id);
    $stmt->execute();
    $stmt->close();
}


///////////////////UPDATE WATCH HISTORY///////////////////UPDATE WATCH HISTORY///////////////////UPDATE WATCH HISTORY///////////////////UPDATE WATCH HISTORY
elseif ($action == "watch_history") {
    /**
     * update watch_history to users table by current logged in user_id from functions js
     * first get watch history by logged in id and unserialize, serialize with updated value and save
     */
    $page_name = $post['page_name'];
    $user_id = $loggedUser->user_id;
    $sql = "SELECT watch_history FROM users WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($watch_history);
    $stmt->fetch();
    $stmt->free_result();
    $stmt->close();

    if ($watch_history != "") {
        $watch_history = unserialize($watch_history);
    }

    $watch_history[$page_name] = 1;
    $watch_history = serialize($watch_history);

    $sql = "UPDATE users SET watch_history = ? WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $watch_history, $user_id);
    $stmt->execute();
    $stmt->close();
}


///////////////////UPDATE AUDIO OPTION///////////////////UPDATE AUDIO OPTION///////////////////UPDATE AUDIO OPTION///////////////////UPDATE AUDIO OPTION///////////////////UPDATE AUDIO OPTION
elseif ($action == "audio") {
    /* update audio column of users table by logged in primary id */
    $audio = $post['audio'];
    $user_id = $loggedUser->user_id;
    $sql = "UPDATE users SET audio = ? WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $audio, $user_id);
    $stmt->execute();
    $stmt->close();
}

///////////////////UPDATE ZOOM OPTION///////////////////UPDATE ZOOM OPTION///////////////////UPDATE ZOOM OPTION///////////////////UPDATE ZOOM OPTION///////////////////UPDATE ZOOM OPTION
elseif ($action == "zoom") {
    /* update zoom column of users table by logged in primary id */
    $zoom = $post['zoom'];
    $user_id = $loggedUser->user_id;
    $sql = "UPDATE users SET zoom = ? WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $zoom, $user_id);
    $stmt->execute();
    $stmt->close();
}

///////////////////UPDATE SHOW LEADER///////////////////UPDATE SHOW LEADER///////////////////UPDATE SHOW LEADER///////////////////UPDATE SHOW LEADER///////////////////UPDATE SHOW LEADER
elseif ($action == "leader") {
    /* update show_leader column of users table by logged in primary id */
    $show_leader = $post['show_leader'];
    $sql = "UPDATE users SET show_leader = ? WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $show_leader, $user_id);
    $stmt->execute();
    $stmt->close();
}


///////////////////UPDATE TIME REMAINING///////////////////UPDATE TIME REMAINING///////////////////UPDATE TIME REMAINING///////////////////UPDATE TIME REMAINING
elseif ($action == "time_remaining") {
    /* update time_remaining column of users table by logged in primary id */
    $time_remaining = $post['time_remaining'];
    $user_id = $loggedUser->user_id;
    $sql = "UPDATE users SET time_remaining = ? WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $time_remaining, $user_id);
    $stmt->execute();
    $stmt->close();
}

///////////////////FILE EXISTS CHECK///////////////////FILE EXISTS CHECK///////////////////FILE EXISTS CHECK///////////////////FILE EXISTS CHECK///////////////////FILE EXISTS CHECK
elseif ($action == "file-exists-check") {

    $fileURL = $post['fileURL'];

    // if ../ exists in string, remove it for consistency
    if (strpos($fileURL, '../') !== false) {
        $file_path = substr($fileURL, 2);
    }

    // we place ../ in path because of the current position
    $fileURL =  "../" . $fileURL;

    if ( file_exists($fileURL) ) {
        $result = "success";
    }
    else {
        $result = "failed";
    }
}


///////////////////UPDATE USER EMAIL ADDRESS - UPDATE-CONTACT.PHP///////////////////UPDATE USER EMAIL ADDRESS - UPDATE-CONTACT.PHP///////////////////UPDATE USER EMAIL ADDRESS - UPDATE-CONTACT.PHP
elseif ($action == 'contact') {

    /* update user's email by user id from update-contact js when user change recover email */
    $new_email = $post['new_email'];

    // use php check for a vaild email format
    if ( filter_var($new_email, FILTER_VALIDATE_EMAIL) ) {
        $data['email'] = "email_valid";
    }
    else {
        $data['email'] = "email-validation-failed";
    }

    if ( $data['email'] == "email_valid" ){

        $sql = "UPDATE users SET user_email = ? WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $new_email, $user_id);
        $stmt->execute();
        $stmt->close();

    }
}


///////////////////CREATE JSON FROM ARRAY AND EXIT TO JS///////////////////CREATE JSON FROM ARRAY AND EXIT TO JS///////////////////CREATE JSON FROM ARRAY AND EXIT TO JS
$data['result'] = $result;

header('Content-Type: application/json');
echo json_encode($data);

exit();

?>
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Test extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'test';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'test_id';

    public static function getQuestions($test)
    {
        $sql = "SELECT question_title, attach_image, attach_audio, show_button, correct_note, question_answer, image_800, image_350, wiki_link, question_id, test_id FROM question WHERE test_id = $test ORDER BY question_answer ASC";
        return DB::select($sql);
    }

    public static function findMasterTest($test_id)
    {
        return DB::select("SELECT test_info, in_game_mastered FROM user_test WHERE user_id = ".Auth::id()." AND test_id = $test_id");
    }

    public static function getAllAudio()
    {
        return DB::select("SELECT * FROM audio_alerts");
    }

    public static function getTotalAnswers()
    {
        $today_total_answered  = 0;
        $date = new \DateTime("now");
        $current_date = $date->format("n-j-y g:iA");
        $current_date_array = explode(" ", $current_date);
        $sql = "SELECT * FROM cron_email WHERE user_id = ".Auth::id()." AND SUBSTRING_INDEX(SUBSTRING_INDEX(report_date, ' ', 1), ' ', -1) = '" . $current_date_array[0] . "'";
        $cron_email = DB::select($sql);
        if ($cron_email) :
            $today_total_answered = 2 * $cron_email[0]->correct_answer_num - $cron_email[0]->total_answer_num;
        endif;
        return $today_total_answered;
    }

    public static function getDataForSubjectPage($subject)
    {
        $data = [];
        if ( ($subject == 'science') || ($subject == 'history') ) {
            // USE SUBJECT GET ALL VOCAB WORDS
            $sql = "SELECT question.question_answer            
                    FROM question             
                    INNER JOIN test ON question.test_id=test.test_id 
                    INNER JOIN subject ON test.subject_id=subject.subject_id 
                    INNER JOIN grade ON subject.grade_id=grade.grade_id             
                    WHERE question.canonical_flag = 1 AND subject.subject_name = '$subject' AND test.public = 2         
                    ORDER BY question.question_answer ASC ";
            $canonical_results = DB::select($sql);
            if ($canonical_results) {
                foreach ($canonical_results as $cr) :
                    $data['canonical_results'][] = $cr->question_answer;
                    $data['result'] = 'success';
                endforeach;
            } else {
                $data['result'] = 'failed';
            } 
        }
        return $data;
    }

    public static function getDataForTestPage($id)
    {
        $data = [];
        $sql = "SELECT subject.subject_name        
            FROM subject         
            INNER JOIN test ON test.subject_id=subject.subject_id                
            WHERE     test.test_id = $id";
        //$found_subject = DB::select($sql);


        // USE TEST ID, GET VOCAB WORDS
        $sql = "SELECT question.question_answer        
            FROM question         
            INNER JOIN test ON question.test_id=test.test_id 
            INNER JOIN subject ON test.subject_id=subject.subject_id 
            INNER JOIN grade ON subject.grade_id=grade.grade_id         
            WHERE question.test_id = $id
            ORDER BY question.question_answer ASC" ;
        $canonical_results = DB::select($sql);
        if ($canonical_results) {
            foreach ($canonical_results as $cr) :
                $data['canonical_results'][] = $cr->question_answer;
            endforeach;
            $data['result'] = 'success';
        } else {
            $data['result'] = 'failed';
        }

        return $data;
    }

    public static function insertCronEmail($test_id, $current_date, $correct_answer_num, $total_answer_num, $question_report, $pre_post_report)
    {
        $sql = "INSERT INTO 
                    cron_email (user_id,
                                test_id,
                                report_date,
                                correct_answer_num,
                                total_answer_num,
                                question_report,
                                pre_post_report
                                ) 
                    VALUES (".Auth::user()->user_id.", ".$test_id.", ".$current_date.", ".$correct_answer_num.", ".$total_answer_num.", ".$question_report.", ".$pre_post_report.")
                    ";
        return DB::select($sql);
    }

    public static function getCronEmail()
    {
        $sql = "SELECT id, question_report, pre_post_report, report_date FROM cron_email WHERE user_id = ".Auth::user()->user_id;
        return DB::select($sql);
    }

    public static function updateCronEmail($test_id, $current_date, $correct_answered, $total_answered, $question_report, $pre_post_report, $cron_email_id)
    {
        $sql = "UPDATE 
                    cron_email 
                SET
                    test_id = {$test_id}, 
                    report_date = {$current_date}, 
                    correct_answer_num = {$correct_answered},
                    total_answer_num = {$total_answered}, 
                    question_report = {$question_report}, 
                    pre_post_report = {$pre_post_report} 
                WHERE 
                    id = {$cron_email_id}
                ";
        return DB::select($sql);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Question extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'question';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'question_id';

    public static function getDefinition($test_id)
    {
        $sql = "SELECT question_title, attach_image, attach_audio, show_button, correct_note, question_answer, image_800, image_350, wiki_link, khan_link, question_id, canonical, full_audio, title_tag, description_front, canonical_all_subject
                FROM question WHERE test_id = $test_id
                GROUP BY question.question_answer
                ORDER BY question.question_answer ASC";
        return DB::select($sql);
    }

    public static function getChapters($chapter)
    {
        $search_results = array();
        $orderBy = "'Science','History','Spanish'";
        if (!empty($chapter)) :
            $orderBy = "'Science','History','Spanish'";
            $sql = "SELECT
                        DISTINCT `subject`.subject_name
                        FROM
                        `test`
                        INNER JOIN `subject` ON `subject`.subject_id = test.subject_id
                    WHERE  
                    `test`.subject_extra_name = '".$chapter."'";
            $subject = DB::select($sql);
            if (!empty($subject[0]->subject_name)) :
                switch ($subject[0]->subject_name) :
                    case "History":
                        $orderBy = "'History','Science','Spanish'";
                    break;
                    case "Spanish":
                        $orderBy = "'Spanish','Science','History'";
                    break;
                    default:
                        $orderBy = "'Science','History','Spanish'";
                endswitch;
            endif;
        endif;
        $sql = "SELECT
        test.subject_extra_name,
        test.subject_id
        FROM
            `subject`
            INNER JOIN test ON `subject`.subject_id = test.subject_id
            INNER JOIN grade ON `subject`.grade_id = grade.grade_id
        WHERE 
            `subject`.view_option = '1' AND 
            `subject`.subject_name IN ('Science','History','Spanish')

            GROUP BY `subject`.subject_id, test.subject_extra_name, test.subject_id
           
        ORDER BY             
            FIELD(`subject`.subject_name,'Science','History','Spanish'),
            `subject`.grade_id ASC";
        $data = DB::select($sql);
        if ($data) :
            foreach ($data as $row) :
                array_push($search_results, [
                    'id' => $row->subject_id,
                    'text' => $row->subject_extra_name,
                    'value' => $row->subject_extra_name,
                    'slug' => spacestoDashes($row->subject_extra_name),
                    'href' => '/progress?chapter='.$row->subject_extra_name,
                ]);
            endforeach;
        endif;
        return $search_results;
    }

    public static function searchChapters()
    {
        $search_results = array();
        $sql = "SELECT 
                distinct question_answer
                FROM question
                ORDER BY question_answer asc";
        $data = DB::select($sql);
        if ($data) :
            foreach ($data as $row) :
                array_push($search_results, [
                    'API' => $row->question_answer,
                    'Description' => $row->question_answer,
                ]);
            endforeach;
        endif;
        return $search_results;
    }
}

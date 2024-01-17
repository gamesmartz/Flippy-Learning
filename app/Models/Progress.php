<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Progress extends Model
{
    use HasFactory;

    public static function getProgress($search = null, $chapter = null)
    {
        $results = $mastery_results = [];
        if (Auth::check()) :
            $sql = "SELECT 
                user_test.test_id, user_test.mode_number, user_test.question_history            
                FROM user_test
                WHERE user_test.user_id = ".Auth::id()."
                ORDER BY user_test.test_id
                ";
            $mastery_results = DB::select($sql);
        endif;

        $sql = "SELECT
            `subject`.seo_subject_image,
            test.subject_extra_name,
            `subject`.subject_id,
            grade.grade_name
            FROM
            `subject`
            INNER JOIN test ON `subject`.subject_id = test.subject_id
            INNER JOIN grade ON `subject`.grade_id = grade.grade_id ";
        if (!empty($search)) :    
            $sql .= "INNER JOIN question ON `test`.test_id = question.test_id";
        endif;
        $sql .= " WHERE `subject`.view_option = '1' AND ";
        if (!empty($search)) :    
            $sql .= "`question`.question_answer = '".$search."' AND ";
        endif;
        if (!empty($chapter)) :    
            $sql .= "`test`.subject_extra_name = '".$chapter."' ";
        else :
            $sql .= "`subject`.subject_name IN ('Science','History','Spanish')";
        endif;        
        $sql .= "
            GROUP BY 
            `subject`.subject_id
            ORDER BY             
            FIELD(`subject`.subject_name,'Science','History','Spanish'),
            `subject`.grade_id ASC
        ";
        $data = DB::select($sql);
        if ($data) :            
            foreach ($data as $row) :
                $sql = "SELECT 
                        test.test_id, test.test_name, test.public, test.school_year,
                        test.subject_extra_name, subject.subject_name, subject.grade_id                
                        FROM subject                 
                        INNER JOIN test ON test.subject_id=subject.subject_id";
                    if (!empty($search)) :    
                        $sql .= " INNER JOIN question ON `test`.test_id = question.test_id";
                    endif;                
                    $sql .= " WHERE subject.subject_id = ".$row->subject_id;
                    if (!empty($search)) :    
                        $sql .= " AND `question`.question_answer = '".$search."' ";
                    endif;                    
                    $sql .= " ORDER BY test.school_year ASC";
                    $data2 = DB::select($sql);
                    $test_results = [];
                    if ($data2) :
                        foreach ($data2 as $row2) :
                            if ( $row2->public == 2 ) :

                                $image = '/assets/images/icon-progress-default.png';
                                $icon_image_badge_blue = ('upload/subjects/' . strtolower($row2->subject_name)  . '/chapter-icons/' . $row2->grade_id . '/' . removeColonSpacesToDashes($row2->test_name) .  '/badge-blue/'  . removeColonSpacesToDashes($row2->test_name) . '.png');
                                $icon_image_badge_grey = ('upload/subjects/' . strtolower($row2->subject_name)  . '/chapter-icons/' . $row2->grade_id . '/' . removeColonSpacesToDashes($row2->test_name) .  '/badge-grey/'  . removeColonSpacesToDashes($row2->test_name) . '.png');
                                $icon_image_badge_yellow = ('upload/subjects/' . strtolower($row2->subject_name)  . '/chapter-icons/' . $row2->grade_id . '/' . removeColonSpacesToDashes($row2->test_name) .  '/badge-yellow/'  . removeColonSpacesToDashes($row2->test_name) . '.png');
                                if ( file_exists($icon_image_badge_blue) && file_exists($icon_image_badge_grey) && file_exists($icon_image_badge_yellow) ) :
                                    $image = $icon_image_badge_grey;
                                    $key_of_user_test = array_search($row2->test_id, array_column($mastery_results, 'test_id'));
                                    if ($key_of_user_test !== false) :
                                        // if question_history in the user_test table is not empty, then there is an array there, and a question has been answered, so show the blue badge
                                        if ( !empty($mastery_results[$key_of_user_test]->question_history) )  :
                                            $image = $icon_image_badge_blue;
                                         // elseif mode_number greater than 0, show yellow badge, because the test has been mastered
                                        elseif ($mastery_results[$key_of_user_test]->mode_number > 0) :
                                            $image = $icon_image_badge_yellow;
                                        endif;
                                    endif;
                                endif;

                                array_push($test_results, [
                                    'test_id' => $row2->test_id,
                                    'test_name' => testNameAfterColon($row2->test_name),                                    
                                    'public' => $row2->public,
                                    'school_year' => $row2->school_year,
                                    'subject_name' => $row2->subject_name,
                                    'subject_extra_name' => $row2->subject_extra_name,
                                    'grade_id' => $row2->grade_id,
                                    'url' => '/chapter/'.$row2->test_id.'/'.spacestoDashes(testNameAfterColon($row2->test_name)),
                                    'quiz_url' => '/test-question/'.$row2->test_id.'/que',
                                    'image' => $image,
                                ]);
                            endif;
                        endforeach;
                    endif;
                    if (count($test_results) > 0) : 
                        array_push($results, [
                            'subject_extra_name' => $row->subject_extra_name,
                            'subject_id' => $row->subject_id,
                            'totalActiveRecords' => count($test_results),
                            'url' => '/grade/'.$row->subject_id.'/'.spacestoDashes($row->subject_extra_name),
                            'test_results' => $test_results,
                        ]);
                    endif;
            endforeach;
        endif;

        return $results;
    }
}

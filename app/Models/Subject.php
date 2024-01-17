<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Subject extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subject';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'subject_id';

    public static function getResults($subject_id)
    {
        $sql = "SELECT 
        test.test_id, test.test_name, test.public, test.school_year, test.subject_extra_name, subject.subject_name, subject.grade_id        
        FROM subject         
        INNER JOIN test ON test.subject_id=subject.subject_id        
        WHERE subject.subject_id = {$subject_id} 
        ORDER BY test.school_year ASC";
        return DB::select($sql);
    }

    public static function getSubjects($subject)
    {
        if ($subject == 'science') :
            $sql = "SELECT 
                    `subject`.seo_subject_image,
                    test.subject_extra_name,
                    `subject`.subject_id,
                    grade.grade_name
                    FROM
                    `subject`
                    INNER JOIN test ON `subject`.subject_id = test.subject_id
                    INNER JOIN grade ON `subject`.grade_id = grade.grade_id
                    WHERE
                    `subject`.view_option = '1' AND
                    `subject`.subject_name = 'Science'
                    GROUP BY
                    `subject`.subject_id";
        else:
            $sql = "SELECT 
                    `subject`.seo_subject_image,
                    test.subject_extra_name,
                    `subject`.subject_id,
                    grade.grade_name
                    FROM
                    `subject`
                    INNER JOIN test ON `subject`.subject_id = test.subject_id
                    INNER JOIN grade ON `subject`.grade_id = grade.grade_id
                    WHERE
                    `subject`.view_option = '1' AND
                    `subject`.subject_name = 'History'
                    GROUP BY
                    `subject`.subject_id";      
        endif;
        return DB::select($sql);
    }
}

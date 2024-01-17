<?php
function get_test_data($subject_id_passed_in) {
    $testResults = [];

    // Fetch the data using Laravel's Query Builder
    $fetchedResults = DB::table('subject')
                        ->join('test', 'test.subject_id', '=', 'subject.subject_id')
                        ->where('subject.subject_id', $subject_id_passed_in)
                        ->orderBy('test.school_year', 'asc')
                        ->get([
                            'test.test_id', 
                            'test.test_name', 
                            'test.public', 
                            'test.school_year', 
                            'test.subject_extra_name', 
                            'subject.subject_name', 
                            'subject.grade_id'
                        ]);

    // Manually constructing the array
    foreach ($fetchedResults as $result) {
        $testResults[] = [
            'test_id' => $result->test_id,
            'test_name' => $result->test_name,
            'public' => $result->public,
            'school_year' => $result->school_year,
            'subject_extra_name' => $result->subject_extra_name,
            'subject_name' => $result->subject_name,
            'grade_id' => $result->grade_id
        ];
    }

    return $testResults;
}
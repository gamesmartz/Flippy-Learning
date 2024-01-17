<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Test;
use App\Models\Subject;
use App\Models\Grade;
use App\Models\Config;

class DefinitionController extends Controller
{
    /**
     * Show the home page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Question $question, $name)
    {
        $test = Test::findOrFail($question->test_id);
        $subject = Subject::findOrFail($test->subject_id);
        $grade = Grade::findOrFail($subject->grade_id);       

        return view('definitions', [            
            'question' => $question,
            'test' => $test
        ]);
    }
}

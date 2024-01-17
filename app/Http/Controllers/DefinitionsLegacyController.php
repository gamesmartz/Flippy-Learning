<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Test;
use App\Models\Subject;
use App\Models\Grade;
use Illuminate\Http\Request;

class DefinitionsLegacyController extends Controller
{
    /**
     * Show the page for a legacy definition URL.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Extract the question_id from the query parameter 'definition'
        $questionId = $request->query('definition');

        // Find the Question or fail if not found
        $question = Question::findOrFail($questionId);
        $test = Test::findOrFail($question->test_id);
        $subject = Subject::findOrFail($test->subject_id);
        $grade = Grade::findOrFail($subject->grade_id);

        // Render the view with the required data
        return view('definitions', [
            'question' => $question,
            'test' => $test
            // Include any other necessary data
        ]);
    }
}

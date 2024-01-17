<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ChapterLegacyController extends Controller
{
    /**
     * Show the chapter page for legacy URLs.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Retrieve the testId from the query parameter 'c'
        $testId = $request->query('c');

        // Fetch the test information based on the provided test ID
        $test = DB::table('test')
            ->where('test_id', $testId)
            ->first();

        if (empty($test) || empty($test->subject_id)) {
            abort(404);
        }

      // Fetch the $question_results data using the getChapterData method
            $question_results = $this->getChapterData($test->test_id);
            $subject_id = $test->subject_id; // Define $subject_id based on $test

            // Retrieve additional data here using DB queries
            $subjectData = DB::table('subject')
                ->select('subject_name', 'grade_id')
                ->where('subject_id', $test->subject_id)
                ->first();

            $gradeData = DB::table('grade')
                ->select('grade_name')
                ->where('grade_id', $subjectData->grade_id)
                ->first();

            // Add more queries as needed to retrieve other data
            $configData350 = DB::table('config')
                ->select('option_value')
                ->where('option_name', 'gs_350_default')
                ->first();

            $configData800 = DB::table('config')
                ->select('option_value')
                ->where('option_name', 'gs_800_default')
                ->first();

            // Check if the data is available and set the variables accordingly
            $gs_350_default = $configData350 ? $configData350->option_value : null;
            $gs_800_default = $configData800 ? $configData800->option_value : null;

            // Commented out the redundant call to getChapterData
            // $subject_id = $test->subject_id;
            // $question_results = $this->getChapterData($subject_id);

        // Return the view with the required data
        return view('chapter', [
            'test' => $test,
            'subjectData' => $subjectData,
            'gradeData' => $gradeData,
            'gs_350_default' => $gs_350_default,
            'gs_800_default' => $gs_800_default,
            'question_results' => $question_results, // Pass the $question_results variable
            'subject_id' => $subject_id, // Pass $subject_id to the view
            // Add other data as needed
        ]);
    }

    public function getChapterData($test_id)
    {
        //dd($test_id);
        // Fetch questions and answers
        $questionsAndAnswers = DB::table('question')
            ->select(/* columns */)
            ->leftJoin('answer', 'question.question_id', '=', 'answer.question_id')
            ->where('question.test_id', $test_id)
            ->orderBy('question.question_answer', 'asc')
            ->get();

        // Organize data into a structured format
        $question_results = [];
        foreach ($questionsAndAnswers as $row) {
            $question_id = $row->question_id;
            if (!isset($question_results[$question_id])) {
                $question_results[$question_id] = [
                    'question_title' => $row->question_title,
                    'attach_image' => $row->attach_image,
                    'attach_audio' => $row->attach_audio,
                    'show_button' => $row->show_button,
                    'correct_note' => $row->correct_note,
                    'question_answer' => $row->question_answer,
                    'image_800' => $row->image_800,
                    'image_350' => $row->image_350,
                    'wiki_link' => $row->wiki_link,
                    'question_id' => $row->question_id,                    
                    'answers' => [],
                ];
            }
            // Add answers to the question
            if (!empty($row->text)) {
                $question_results[$question_id]['answers'][] = [
                    'text' => $row->text,
                    'attach' => $row->attachment,
                    'correct' => $row->correct,
                ];
            }
        }

        // Pass the fetched and organized data to the view
        return $question_results;
    }

    // Define the getTestIdFromSubject method to fetch the test ID based on subject_id
    private function getTestIdFromSubject($subject_id)
    {
        // Assuming you have a 'test' table with a 'subject_id' column
        $test = DB::table('test')
            ->select('test_id')
            ->where('subject_id', $subject_id)
            ->first();

        if ($test) {
            return $test->test_id;
        } else {
            // Handle the case where no test ID is found for the subject.
            // You can return null or throw an exception as needed.
            return null;
        }
    }
}

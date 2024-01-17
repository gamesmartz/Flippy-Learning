<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class GradeLegacyController extends Controller
{
    /**
     * Show the grade page for legacy URLs.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $v = null)
    {
        // Retrieve the subject_id from the query parameter 'g'
        $subjectId = $request->query('g');

        // Find the subject or abort with a 404 error if not found
        $subject = Subject::findOrFail($subjectId);

        // The rest of the logic from GradeController
        // ...

        // Fetch grade name
        $gradeName = DB::table('grade')
            ->where('grade_id', $subject->grade_id)
            ->value('grade_name');

        // Fetch configuration values
        $configValues = DB::table('config')
            ->whereIn('option_name', ['gs_350_default', 'gs_800_default'])
            ->pluck('option_value', 'option_name');

        // Fetch tests related to the subject
        $tests = DB::table('test')
            ->join('subject', 'test.subject_id', '=', 'subject.subject_id')
            ->where('subject.subject_id', $subject->subject_id)
            ->orderBy('test.school_year', 'asc')
            ->get();

        return view('grade', [
            'subject' => $subject,
            'gradeName' => $gradeName,
            'gs_350_default' => $configValues['gs_350_default'] ?? null,
            'gs_800_default' => $configValues['gs_800_default'] ?? null,
            'tests' => $tests,
            'v' => $v, // Pass the optional parameter to the view, if needed
        ]);
    }

    // Include any other methods from GradeController if needed
    // ...
}

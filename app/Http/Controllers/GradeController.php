<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Grade;
use App\Models\Config;
use App\Models\Question;

use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    /**
     * Show the grade page.
     *
     * @param Subject $subject
     * @param string|null $v
     * @return \Illuminate\Http\Response
     */
    public function index(Subject $subject, $v = null)
    {
        if (empty($subject->view_option) || empty($subject->grade_id)) {
            abort(404);
        }

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
}

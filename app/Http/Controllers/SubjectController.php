<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Config;

class SubjectController extends Controller
{
    /**
     * Show the subject page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($subject)
    {
        if ($subject != 'science' && $subject != 'history') :
            abort(404);
        endif;
        return view('subject', [
            'subject' => $subject            
        ]);
    }
}

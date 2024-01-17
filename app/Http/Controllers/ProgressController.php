<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Progress;

class ProgressController extends Controller
{
    /**
     * Show the home page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $chapter = null;
        if ($request->filled('search')) :
            $search = $request->input('search');
        endif;
        if ($request->filled('chapter')) :
            $chapter = $request->input('chapter');
        endif;

        $que = [];
        if (Auth::check()) :
            $que = Auth::user()->que;
        endif;

        return view('progress', [
            'que' => $que,
            'search' => $search,
            'chapter' => $chapter,
        ]);
    }
}

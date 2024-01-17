<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    /**
     * Show the ajax video page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $action)
    {
        return view('ajax.video', [
            'action' => $action,
            'post' => $request->all()
        ]);
    }
}

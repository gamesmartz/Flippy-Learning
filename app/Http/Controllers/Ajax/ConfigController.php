<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    /**
     * Show the ajax config page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $action)
    {
        return view('ajax.config', [
            'action' => $action,
            'post' => $request->all()
        ]);
    }

    /**
     * Show the ajax upload audio alert page.
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadAudioAlert(Request $request)
    {
        return view('ajax.uploadAudioAlert', [
            'post' => $request->all()
        ]);
    }
}

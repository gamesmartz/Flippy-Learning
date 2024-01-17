<?php

namespace App\Http\Controllers;

use App\Models\Test;

class TestController extends Controller
{
    /**
     * Show the test page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Test $test, $type)
    {
        return view('test-question', [
            'test' => $test,
            'audioAlerts' => Test::getAllAudio()
        ]);
    }
}

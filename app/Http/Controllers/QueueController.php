<?php

namespace App\Http\Controllers;

class QueueController extends Controller
{
    /**
     * Show the queue page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('queue');
    }
}

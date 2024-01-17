<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HistoryController extends Controller
{
    /**
     * Show the history page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        return view('history');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Show the report page with a specific date.
     *
     * @param  string  $date
     * @return \Illuminate\Http\Response
     */
    public function index($date)
    {        
        // Pass the $date variable to the 'reports' view
        return view('reports', ['date' => $date]);
    }
}


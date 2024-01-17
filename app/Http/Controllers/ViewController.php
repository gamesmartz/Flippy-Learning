<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewController extends Controller
{
    /**
     * Show the view page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        return view('view');
    }
}

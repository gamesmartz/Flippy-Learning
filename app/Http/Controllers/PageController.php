<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    /**
     * Show the download page.
     *
     * @return \Illuminate\Http\Response
     */
    public function download()
    {        
        return view('download');
    }
}
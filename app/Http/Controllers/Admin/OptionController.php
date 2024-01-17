<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    /**
     * Show the admin options page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.options');
    }
}

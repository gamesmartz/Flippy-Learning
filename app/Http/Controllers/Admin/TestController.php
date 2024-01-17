<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class TestController extends Controller
{
    /**
     * Show the admin test page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.tests');
    }

    /**
     * Show the admin multiple page.
     *
     * @return \Illuminate\Http\Response
     */
    public function multiple()
    {
        return view('admin.multiple');
    }
}

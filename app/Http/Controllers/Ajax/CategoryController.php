<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Show the ajax category page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $action)
    {
        return view('ajax.category', [
            'action' => $action,
            'post' => $request->all()
        ]);
    }
}

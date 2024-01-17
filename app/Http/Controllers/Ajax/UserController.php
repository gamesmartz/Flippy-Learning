<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Show the ajax user page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $action)
    {
        return view('ajax.user', [
            'action' => $action,
            'post' => $request->all()
        ]);
    }
}

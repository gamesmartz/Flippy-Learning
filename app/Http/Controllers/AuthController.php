<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login page.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (Auth::check()) :
            return redirect(route('progress'));
        endif;
        return view('auth.login', [
            'canonical' => '',
        ]);
    }

    /**
     * Show the register page.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        if (Auth::check()) :
            return redirect(route('progress'));
        endif;
        return view('auth.register', [
            'canonical' => '',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;

class AutoCompleteController extends Controller
{
    /**
     * Manage/Add test que.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($action, Request $request)
    {
        $data = [];
        if ($action == 'bysubject') :
            $data = Test::getDataForSubjectPage($request->input('subject'));
        endif;
        if ($action == 'byid') :
            $data = Test::getDataForTestPage($request->input('id'));
        endif;
        return $data;
    }
}

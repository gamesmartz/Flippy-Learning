<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//TEST CONTROLLER

class TestController extends Controller
{  
    // NOT BEING USED
    public function index($action, Request $request)
    {
        if ($action !== 'get') {
            return response()->json(['result' => 'error', 'message' => 'Invalid action']);
        }
    
        $type = $request->input('type');
        $response = [];
    
        if ($type == "track_history") {
            $track_history = $request->input('track_history');
            $user_id = $request->user()->id; // Assuming user is authenticated and you can get the user id
    
            // Your raw SQL query
            $sql = "SELECT t.*, ut.* FROM test t
                    JOIN user_test ut ON t.test_id = ut.test_id
                    WHERE ut.user_id = :user_id AND ut.test_info != ''
                    GROUP BY t.test_id
                    ORDER BY ut.start_date DESC";
    
            $bindings = ['user_id' => $user_id];
            $tests = DB::select($sql, $bindings);
    
            $response = [
                'result' => 'success',
                'test' => $tests
            ];
        } else {
            $response = ['result' => 'error', 'message' => 'Invalid type'];
        }
    
        return response()->json($response);
    }
    


    /**
     * Manage/Add test que.
     *
     * @return \Illuminate\Http\Response
     */
    public function studyingStop(Request $request)
    {
        return view('ajax.studying-stop');
    }
}

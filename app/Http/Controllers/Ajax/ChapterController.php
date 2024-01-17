<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Question;

class ChapterController extends Controller
{
    public function getChapters($chapter = null)
    {
        return Question::getChapters($chapter);
    }    

    public function searchChapters()
    {
        $search_results = Question::searchChapters();
        return [
            'count' => count($search_results),
            'entries' => $search_results
        ];
    }
}

<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/welcome', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// From Filament Student Setup
    Route::get('{student}/invoice/generate', [App\Http\Controllers\InvoicesController::class, 'generatePdf'])->name('student.invoice.generate');


Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    // Other authenticated routes
    Route::get('test-question/{test}/{type}', [\App\Http\Controllers\TestController::class, 'index'])->name('test.question');
    Route::get('queue', [\App\Http\Controllers\QueueController::class, 'index'])->name('queue');
    Route::get('options', [\App\Http\Controllers\OptionController::class, 'index'])->name('options');
    Route::get('help', [\App\Http\Controllers\PageController::class, 'help'])->name('help');
    Route::get('history', [\App\Http\Controllers\HistoryController::class, 'index'])->name('history');
    Route::get('reports/{date}', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports');    
    Route::get('view', [\App\Http\Controllers\ViewController::class, 'index'])->name('view');
    // ... add more authenticated routes as needed ...
});

// Admin routes - accessible only to users with admin privileges
Route::group(['prefix' => 'admin', 'middleware' => ['auth:sanctum', 'verified', 'admin']], function () {
    Route::get('tests', [\App\Http\Controllers\Admin\TestController::class, 'index'])->name('admin.tests');
    Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users');
    Route::get('videos', [\App\Http\Controllers\Admin\VideoController::class, 'index'])->name('admin.videos');
    Route::get('options', [\App\Http\Controllers\Admin\OptionController::class, 'index'])->name('admin.options');
    Route::get('create-test-multiple-choice', [\App\Http\Controllers\Admin\TestController::class, 'multiple'])->name('admin.tests.multiple');
    // ... add more admin routes as needed ...
});

// Public routes - accessible without authentication

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
//Route::get('login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login');
//Route::get('register', [\App\Http\Controllers\AuthController::class, 'register'])->name('register');

Route::get('download', [\App\Http\Controllers\PageController::class, 'download'])->name('download');
Route::get('progress', [\App\Http\Controllers\ProgressController::class, 'index'])->name('progress');
Route::get('grade/{subject}/{v?}', [\App\Http\Controllers\GradeController::class, 'index'])->name('grade');

Route::get('chapter/{test}/{v?}', [\App\Http\Controllers\ChapterController::class, 'index'])->name('chapter');
Route::get('chapter/{subject_id}/data', [\App\Http\Controllers\ChapterController::class, 'getChapterData'])->name('chapter.data');

Route::get('definitions/{question}/{name}', [\App\Http\Controllers\DefinitionController::class, 'index'])->name('definition');
Route::get('subject/{subject}', [\App\Http\Controllers\SubjectController::class, 'index'])->name('subject')->where(['subject' => '[a-z]+']);

// Old URL routes. These routes take legacy urls like  https://gamesmartz.com/definitions?definition=4591&bacteria
// and will render the definitions page correctly, so there is no SEO loss on the site move.

use App\Http\Controllers\DefinitionsLegacyController;
Route::get('/definitions', [DefinitionsLegacyController::class, 'index']);

use App\Http\Controllers\ChapterLegacyController;
Route::get('/chapter', [ChapterLegacyController::class, 'index']);

use App\Http\Controllers\GradeLegacyController;
Route::get('/grade', [GradeLegacyController::class, 'index']);

// ... add more public routes as needed ...

// ajax routes
Route::group(['prefix' => 'ajax'], function () {
    Route::get('async-get-chapters/{chapter?}', [\App\Http\Controllers\Ajax\ChapterController::class, 'getChapters'])->name('ajax.get.chapters');
    Route::get('async-search-chapters', [\App\Http\Controllers\Ajax\ChapterController::class, 'searchChapters'])->name('ajax.search.chapters');

    Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {        
        Route::post('async-autocomplete-data/{action}', [\App\Http\Controllers\Ajax\AutoCompleteController::class, 'index'])->name('ajax.autocomplete.data');
        Route::post('async-tests/{action}', [\App\Http\Controllers\Ajax\TestController::class, 'index'])->name('ajax.test');
        Route::post('async-category/{action}', [\App\Http\Controllers\Ajax\CategoryController::class, 'index'])->name('ajax.category');
        Route::post('async-updateUser/{action}', [\App\Http\Controllers\Ajax\UserController::class, 'index'])->name('ajax.update.user');
        Route::post('async-video/{action}', [\App\Http\Controllers\Ajax\VideoController::class, 'index'])->name('ajax.video');
        Route::post('async-config/{action}', [\App\Http\Controllers\Ajax\ConfigController::class, 'index'])->name('ajax.config');
        Route::post('async-upload-audio-alert', [\App\Http\Controllers\Ajax\ConfigController::class, 'uploadAudioAlert'])->name('ajax.upload.audio.alert');
        Route::post('async-studying/stop', [\App\Http\Controllers\Ajax\TestController::class, 'studyingStop'])->name('ajax.studying.stop');
    });    
});

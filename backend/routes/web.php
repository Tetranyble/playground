<?php

use App\Models\User;
use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

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

Route::get('/', function () {
    return 'welcome to playground application';
});
Route::get('/dashboard', function (\App\Http\Requests\GeneralRequest $request) {
    $activities = (new \App\Models\Activity)
        ->activityFor(null)
        ->paginate($request->quantity);

    return view('dashboard', compact('activities'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('activities/{activity:id}/tasks', [\App\Http\Controllers\Trilio\Web\TaskController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('activities.tasks.index');
Route::get('activities/{activity:id}/tasks/create', [\App\Http\Controllers\Trilio\Web\TaskController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('tasks.create');
Route::post('tasks', [\App\Http\Controllers\Trilio\Web\TaskController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('tasks.store');
Route::delete('tasks/{task:id}', [\App\Http\Controllers\Trilio\Web\TaskController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('tasks.destroy');

Route::resource('activities', \App\Http\Controllers\Trilio\Web\ActivityController::class)
    ->middleware(['auth', 'verified']);

require __DIR__.'/auth.php';
require __DIR__.'/youtube.php';

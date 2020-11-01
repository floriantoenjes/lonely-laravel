<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Redirect::route('lonely-dashboard');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia\Inertia::render('Dashboard');
})->name('dashboard');

//Route::middleware(['auth:sanctum', 'verified'])->get('/company', function () {
//    return Inertia\Inertia::render('Company', [
//        'fromBackend' => 'Backend data'
//    ]);
//})->name('company');
//
//Route::middleware(['auth:sanctum', 'verified'])->post('/company', function (\Illuminate\Http\Request $request) {
//    $userNames = array_map(function ($user) {
//        return $user['name'];
//    }, User::all()->toArray());
//    return $userNames;
//});

Route::get('/lonely-dashboard', [DashboardController::class, 'lonelyDashBoard'])->name('lonely-dashboard');
Route::post('/lonely-dashboard', [DashboardController::class, 'setLonelySettings']);
Route::post('/lonely-no-more', [DashboardController::class, 'notLonelyAnymore'])->name('lonely-no-more');

Route::get('/chat/{userId}', [ChatController::class, 'chatWithUser'])->name('chat');
Route::post('/chat/{userId}', [ChatController::class, 'sendChatMessage'])->name('send-chat-message');

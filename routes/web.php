<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
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
Route::get('/lonely-dashboard/refresh', [DashboardController::class, 'refreshDashBoard'])->name('refresh-dashboard');
Route::post('/lonely-dashboard', [DashboardController::class, 'setLonelySettings']);
Route::post('/lonely-no-more', [DashboardController::class, 'notLonelyAnymore'])->name('lonely-no-more');

Route::get('/chat/{userId?}', [ChatController::class, 'chatWithUser'])->name('chat');
Route::post('/chat/{userId}', [ChatController::class, 'sendChatMessage'])->name('send-chat-message');

Route::get('/activity/new', [ActivityController::class, 'newActivity'])->name('new-activity-form');
Route::post('/activity/new', [ActivityController::class, 'createActivity'])->name('create-activity');
Route::post('/activity/{activityId}/join', [ActivityController::class, 'joinActivity'])->name('join-activity');
Route::post('/activity/{activityId}/leave', [ActivityController::class, 'leaveActivity'])->name('leave-activity');
Route::get('/activity/{activityId}',  [ActivityController::class, 'activityDetail'])->name('activity-detail');
Route::post('/activity/{activityId}',  [ActivityController::class, 'sendActivityMessage'])->name('send-activity-message');

Route::get('/user-notifications', [\App\Http\Controllers\UserNotificationController::class, 'getUserNotifications'])->name('user-notifications');
Route::delete('/user-notifications', [\App\Http\Controllers\UserNotificationController::class, 'markUserNotificationsRead'])->name('mark-user-notifications-read');

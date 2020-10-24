<?php

use App\Models\UserLonelySetting;
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
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia\Inertia::render('Dashboard');
})->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->get('/company', function () {
    return Inertia\Inertia::render('Company', [
        'fromBackend' => 'Backend data'
    ]);
})->name('company');

Route::middleware(['auth:sanctum', 'verified'])->post('/company', function (\Illuminate\Http\Request $request) {
    $userNames = array_map(function ($user) {
        return $user['name'];
    }, \App\Models\User::all()->toArray());
    return $userNames;
});


Route::middleware(['auth:sanctum', 'verified'])->get('/lonely-dashboard', function () {
    return Inertia\Inertia::render('LonelyDashboard');
})->name('lonely-dashboard');

Route::middleware(['auth:sanctum', 'verified'])->post('/lonely-dashboard', function (\Illuminate\Http\Request $request) {
    $lonelySetting = new UserLonelySetting();
//    $lonelySetting->latitu

    return Redirect::route('lonely-dashboard');
});

<?php

use App\Models\User;
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
    }, User::all()->toArray());
    return $userNames;
});


Route::middleware(['auth:sanctum', 'verified'])->get('/lonely-dashboard', function () {
    $currentUserLonelySettings = Auth::user()->userLonelySetting()->first();

    $lonely = false;
    if ($currentUserLonelySettings !== null) {
        $lonely = isLonelyToday($currentUserLonelySettings);
    }

    $lonelyPersonSettings = UserLonelySetting::where(DB::raw('DATE(lonely_since)'), '=', DB::raw('CURDATE()'))->get();

    $lonelyPersonIds = [];
    foreach ($lonelyPersonSettings as $lonelyPersonSetting) {
        if ($lonelyPersonSetting->user_id === $currentUserLonelySettings->user_id) {
            continue;
        }
        $lonelyPersonIds[] = $lonelyPersonSetting->user_id;
    }

    $lonelyPersons = User::whereIn('id', $lonelyPersonIds)->get();

    return Inertia\Inertia::render('LonelyDashboard', [
        'userLonelySettings' => $currentUserLonelySettings,
        'success' => true,
        'lonely' => $lonely,
        'lonelyPersons' => $lonelyPersons
    ]);
})->name('lonely-dashboard');


/**
 * @param $userLonelySettings
 * @return bool
 * @throws Exception
 */
function isLonelyToday($userLonelySettings): bool
{
    $userLonelySinceDateTime = new DateTime($userLonelySettings->lonely_since);
    $userLonelySinceDateTime->setTime(0, 0);

    $now = new DateTime();
    $now->setTime(0, 0);

    $lonely = ($now->getTimestamp() === $userLonelySinceDateTime->getTimestamp());
    return $lonely;
}


Route::middleware(['auth:sanctum', 'verified'])->post('/lonely-dashboard', function (\Illuminate\Http\Request $request) {
    $address = $request->input('address') . ' ' . $request->input('city') . ' ' . $request->input('postcode');

    $coordinates = getCoordinatesFromAddress($address);
    if ($coordinates === false) {
        return Redirect::route('lonely-dashboard');
    }

    createOrUpdateLonelySetting($coordinates, $request);

    return Redirect::route('lonely-dashboard');
});


function getCoordinatesFromAddress(string $address) {
//    $prepAddr = str_replace(' ','+',$address);
//    $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false');
//    $output = json_decode($geocode);
//
//    if ($output->error_message) {
//        return false;
//    }
//
//    return [
//        'latitude' => $output->results[0]->geometry->location->lat,
//        'longitude' => $output->results[0]->geometry->location->lng
//    ];

    return [
        'latitude' => '53.044941',
        'longitude' => '8.517674'
    ];
}


/**
 * @param array $coordinates
 * @param \Illuminate\Http\Request $request
 */
function createOrUpdateLonelySetting(array $coordinates, \Illuminate\Http\Request $request): void
{
    $lonelySetting = Auth::user()->userLonelySetting()->firstOr(['*'], function () {
        return new UserLonelySetting();
    });

    $lonelySetting->latitude = $coordinates['latitude'];
    $lonelySetting->longitude = $coordinates['longitude'];
    $lonelySetting->city = $request->input('city');
    $lonelySetting->postcode = $request->input('postcode');
    $lonelySetting->address = $request->input('address');

    $lonelySetting->lonely_since = date('c');

    $lonelySetting->radius = $request->input('radius');
    $lonelySetting->meet_up_age_from = $request->input('ageFrom');
    $lonelySetting->meet_up_age_to = $request->input('ageTo');

    $lonelySetting->user_id = Auth::user()->id;
    $lonelySetting->save();
}

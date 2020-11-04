<?php


namespace App\Http\Controllers;


use App\Models\User;
use App\Models\UserLonelySetting;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'verified']);
    }

    public function lonelyDashBoard()
    {
        $currentUserLonelySettings = Auth::user()->userLonelySetting()->first();

        $lonely = false;
        if ($currentUserLonelySettings !== null) {
            $lonely = $this->isLonelyToday($currentUserLonelySettings);
        }

        if ($lonely === true) {
            $lonelyPersonSettings = UserLonelySetting::where(DB::raw('DATE(lonely_since)'), '=', DB::raw('CURDATE()'))->get();

            $lonelyPersonIds = [];
            foreach ($lonelyPersonSettings as $lonelyPersonSetting) {
                if ($lonelyPersonSetting->user_id === $currentUserLonelySettings->user_id) {
                    continue;
                }
                $lonelyPersonIds[] = $lonelyPersonSetting->user_id;
            }

            $lonelyPersons = User::whereIn('id', $lonelyPersonIds)->get();
        } else {
            $lonelyPersons = [];
        }

        return Inertia::render('LonelyDashboard', [
            'userLonelySettings' => $currentUserLonelySettings,
            'success' => true,
            'lonely' => $lonely,
            'lonelyPersons' => $lonelyPersons
        ]);
    }

    public function setLonelySettings(Request $request)
    {
        $address = $request->input('address') . ' ' . $request->input('city') . ' ' . $request->input('postcode');

        $coordinates = $this->getCoordinatesFromAddress($address);
        if ($coordinates === false) {
            return Redirect::route('lonely-dashboard');
        }

        $this->createOrUpdateLonelySetting($coordinates, $request);

        return Redirect::route('lonely-dashboard');
    }

    public function notLonelyAnymore()
    {
        $lonelySetting = Auth::user()->userLonelySetting()->first();

        if ($lonelySetting !== null) {
            $lonelySetting->lonely_since = null;
            $lonelySetting->save();
        }

        return Redirect::route('lonely-dashboard');
    }

    /**
     * @param $userLonelySettings
     * @return bool
     * @throws Exception
     */
    private function isLonelyToday($userLonelySettings): bool
    {
        if ($userLonelySettings->lonely_since === null) {
            return false;
        }

        $userLonelySinceDateTime = new DateTime($userLonelySettings->lonely_since);
        $userLonelySinceDateTime->setTime(0, 0);

        $now = new DateTime();
        $now->setTime(0, 0);

        $lonely = ($now->getTimestamp() === $userLonelySinceDateTime->getTimestamp());
        return $lonely;
    }

    function getCoordinatesFromAddress(string $address) {
        $prepAddr = str_replace(' ','+',$address);

        $geocodeApiKey = \config('google_api.geocodeApiKey');
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json?address='. $prepAddr . '&key=' . $geocodeApiKey);

        $location = json_decode($response->body())->results[0]->geometry->location;
        $lat = $location->lat;
        $lng = $location->lng;

        return [
            'latitude' => $lat,
            'longitude' => $lng
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
}

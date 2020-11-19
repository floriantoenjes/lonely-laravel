<?php


namespace App\Http\Controllers;


use App\Helpers\LonelyHelpers;
use App\Models\Activity;
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

        $lonelyPersons = $this->getLonelyPeople($lonely, $currentUserLonelySettings);

        return Inertia::render('LonelyDashboard', [
            'userLonelySettings' => $currentUserLonelySettings,
            'success' => true,
            'lonely' => $lonely,
            'lonelyPersons' => $lonelyPersons,
            'activities' => Activity::all()
        ]);
    }

    public function refreshDashBoard()
    {
        $currentUserLonelySettings = Auth::user()->userLonelySetting()->first();

        $lonely = false;
        if ($currentUserLonelySettings !== null) {
            $lonely = $this->isLonelyToday($currentUserLonelySettings);
        }

        return \Response::json([
            'lonelyPersons' => $this->getLonelyPeople($lonely, $currentUserLonelySettings),
            'activities' => Activity::all()
        ]);
    }

    public function setLonelySettings(Request $request)
    {
        $address = $request->input('address') . ' ' . $request->input('city') . ' ' . $request->input('postcode');

        $coordinates = LonelyHelpers::getCoordinatesFromAddress($address);
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

    /**
     * @param bool $lonely
     * @param \Illuminate\Database\Eloquent\Model|null $currentUserLonelySettings
     * @return array|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getLonelyPeople(bool $lonely, ?\Illuminate\Database\Eloquent\Model $currentUserLonelySettings)
    {
        if ($lonely === true) {
            $lonelyPersonSettings = UserLonelySetting::where(DB::raw('DATE(lonely_since)'), '=', DB::raw('CURDATE()'))->get();

            $lonelyPersonIds = [];
            foreach ($lonelyPersonSettings as $lonelyPersonSetting) {
                if ($lonelyPersonSetting->user_id === $currentUserLonelySettings->user_id) {
                    continue;
                }

                $dist = LonelyHelpers::distFrom($currentUserLonelySettings->latitude, $currentUserLonelySettings->longitude,
                    $lonelyPersonSetting->latitude, $lonelyPersonSetting->longitude);

                if ($dist > $currentUserLonelySettings->radius) {
                    continue;
                }

                $lonelyPersonIds[] = $lonelyPersonSetting->user_id;
            }

            $lonelyPersons = User::with('userLonelySetting')->whereIn('id', $lonelyPersonIds)->get();
        } else {
            $lonelyPersons = [];
        }
        return $lonelyPersons;
    }

}

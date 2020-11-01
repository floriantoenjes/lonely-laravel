<?php


namespace App\Http\Controllers;


use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ActivityController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'verified']);
    }

    public function newActivity()
    {
        return Inertia::render('ActivityForm');
    }

    public function createActivity(Request $request)
    {
        // TODO: Extract function from DashboardController
        $coordinates = [
            'latitude' => '53.044941',
            'longitude' => '8.517674'
        ];

        $activity = new Activity();
        $activity->name = $request->input('name');
        $activity->description = $request->input('description');
        $activity->city = $request->input('city');
        $activity->postcode = $request->input('postcode');
        $activity->address = $request->input('address');

        $activity->latitude = $coordinates[0];
        $activity->longitude = $coordinates[1];

        $activity->creator_id = Auth::user()->id;
        $activity->save();
    }

}

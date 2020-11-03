<?php


namespace App\Http\Controllers;


use App\Events\ActivityMessageReceived;
use App\Models\Activity;
use App\Models\ActivityMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ActivityController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'verified']);
    }

    public function newActivity()
    {
        return Inertia::render('ActivityForm', [
            'activities' => Activity::get(),
            'joinedActivities' => Auth::user()->joinedActivities()->get()
        ]);
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

        $activity->latitude = $coordinates['latitude'];
        $activity->longitude = $coordinates['longitude'];

        $activity->creator_id = Auth::user()->id;
        $activity->save();

        return Redirect::route('lonely-dashboard');
    }

    public function joinActivity($activityId)
    {
        $activity = Activity::find($activityId);
        $activity->users()->save(Auth::user());

        return response()->json([
            'joined' => true,
        ]);
    }

    public function leaveActivity($activityId)
    {
        $activity = Activity::find($activityId);
        $activity->users()->detach(Auth::user()->id);

        return response()->json([
            'left' => true,
        ]);
    }

    public function activityDetail($activityId)
    {
        $activity = Activity::with('activityMessages', 'activityMessages.sender')->find($activityId);

        return Inertia::render('ActivityChat', [
            'activity' => $activity,
            'currentUser' => Auth::user()
        ]);
    }

    public function sendActivityMessage(Request $request, $activityId)
    {
        $activityMessage = new ActivityMessage();
        $activityMessage->sender_id = Auth::user()->id;
        $activityMessage->activity_id = $activityId;
        $activityMessage->activity_message = $request->input('activityMessageInput');

        $activityMessage->save();

        $activityMessageWithSender = ActivityMessage::with('sender')->find($activityMessage->id);

        event(new ActivityMessageReceived($activityMessageWithSender));

        return Redirect::route('activity-detail', [
            'activityId' => $activityId
        ]);
    }
}

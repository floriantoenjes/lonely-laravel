<?php


namespace App\Http\Controllers;


use App\Events\ActivityMessageReceived;
use App\Events\UserNotificationReceived;
use App\Helpers\LonelyHelpers;
use App\Models\Activity;
use App\Models\ActivityMessage;
use App\Models\UserNotification;
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
        $activities = Activity::with('creator')->get();
        $userLonelySettings = Auth::user()->userLonelySetting()->first();

        $distances = [];
        foreach ($activities as $activity) {
            $distance = LonelyHelpers::distFrom($activity->latitude, $activity->longitude, $userLonelySettings->latitude, $userLonelySettings->longitude);
            $distance = round($distance, 1);

            $distances[$activity->id] = $distance;
        }

        return Inertia::render('ActivityForm', [
            'activities' => $activities,
            'joinedActivities' => Auth::user()->joinedActivities()->with('creator')->get(),
            'distances' => $distances
        ]);
    }

    public function createActivity(Request $request)
    {
        $address = $request->input('address') . ' ' . $request->input('city') . ' ' . $request->input('postcode');

        $coordinates = LonelyHelpers::getCoordinatesFromAddress($address);

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

        return Redirect::route('new-activity-form');
    }

    public function joinActivity($activityId)
    {
        $activity = Activity::find($activityId);

        if (Auth::id() !== $activity->creator_id) {
            $activity->users()->save(Auth::user());
        }

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
        $activity = Activity::with('activityMessages', 'activityMessages.sender', 'creator')->find($activityId);
        $joinedUsers = $activity->users()->get();

        return Inertia::render('ActivityChat', [
            'activity' => $activity,
            'currentUser' => Auth::user(),
            'joinedUsers' => $joinedUsers
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


        $activity = Activity::find($activityId);
        $userNotificationMessage = 'Chat Message in ' . $activity->name;

        foreach ($activity->users as $user) {

            $existingNotifications = UserNotification::where('user_id', '=', $user->id)
                ->where('activity_id', '=', $activityId)->get();

            if (count($existingNotifications) === 0) {

                $userNotification = new UserNotification();
                $userNotification->user_id = $user->id;
                $userNotification->sender_id = Auth::id();
                $userNotification->activity_id = $activityId;
                $userNotification->message = $userNotificationMessage;
                $userNotification->type = 'activityMessage';

                event(new UserNotificationReceived($userNotification));
                $userNotification->save();
            }
        }

        event(new ActivityMessageReceived($activityMessageWithSender));

        return Redirect::route('activity-detail', [
            'activityId' => $activityId
        ]);
    }
}

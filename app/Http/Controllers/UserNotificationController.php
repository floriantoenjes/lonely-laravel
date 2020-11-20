<?php


namespace App\Http\Controllers;


use App\Models\UserNotification;
use Illuminate\Http\Request;

class UserNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'verified']);
    }

    public function getUserNotifications()
    {
        return \Response::json([
            'userNotifications' => UserNotification::where('user_id', '=', \Auth::id())->get()
        ]);
    }

    public function markUserNotificationsRead()
    {
        UserNotification::where('user_id', '=', \Auth::id())->delete();

        return \Response::noContent();
    }

    public function markOneUserNotificationsRead($userNotificationId)
    {
        UserNotification::where('id', '=', $userNotificationId)->delete();

        return \Response::noContent();
    }
}

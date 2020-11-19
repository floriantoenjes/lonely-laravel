<?php

namespace App\Events;

use App\Models\UserNotification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserNotificationReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;

    public $type;

    public $message;

    public $senderId;

    public $activityId;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\UserNotification $userNotification
     */
    public function __construct(UserNotification $userNotification)
    {
        $this->userId = $userNotification->user_id;
        $this->type = $userNotification->type;
        $this->message = $userNotification->message;
        $this->senderId = $userNotification->sender_id;
        $this->activityId = $userNotification->activity_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('user-notifications.' . $this->userId);
    }
}

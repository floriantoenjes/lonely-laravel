<?php

namespace App\Events;

use App\Helpers\UserNotification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserNotificationReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userNotification;

    /**
     * Create a new event instance.
     *
     * @param UserNotification $userNotification
     */
    public function __construct(UserNotification $userNotification)
    {
        $this->userNotification = $userNotification;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('user-notifications.' . $this->userNotification->userId);
    }
}

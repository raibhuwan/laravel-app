<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserCreatedFacebookEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $profileImage;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param $profileImage
     * @param $permission
     */
    public function __construct(User $user, $profileImage)
    {
        $this->user         = $user;
        $this->profileImage = $profileImage;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}

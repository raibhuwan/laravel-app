<?php

namespace App\Events\UserEvents;


use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * Class UserCreatedEvent
 * @package App\Events\UserEvents
 */
class UserCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var
     */
    public $user;
    public $setting;
    public $image;

    /**
     * UserCreatedEvent constructor.
     *
     * @param User $user
     * @param $setting
     * @param $image
     */
    public function __construct(User $user, $setting, $image)
    {
        $this->user    = $user;
        $this->setting = $setting;
        $this->image = $image;
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

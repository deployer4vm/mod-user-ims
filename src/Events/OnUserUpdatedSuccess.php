<?php

namespace hpsynapse\moduser\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

use Illuminate\Support\Facades\Log;

/**
 * saat data user diupdate
 */
class OnUserUpdatedSuccess
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_old,$user_new;

    /**
     * Create a new event instance.
     *
     * @param Array $user_old record data user sebelum diupdate
     * @param Array $user_new record data user setelah diupdate
     * 
     * @return void
     */
    public function __construct($user_old,$user_new)
    {
        $this->user_old = $user_old;
        $this->user_new = $user_new;
    }
}

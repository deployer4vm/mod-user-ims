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
 * saat registrasi user berhasil
 */
class OnUserRegisteredSuccess
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    /**
     * Create a new event instance.
     *
     * @param Array $user record user yang berhasil dicreate
     * 
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }
}

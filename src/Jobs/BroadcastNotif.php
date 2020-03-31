<?php

namespace hpsynapse\moduser\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Client;

use hpsynapse\moduser\Facades\UserRepo;

/**
 * prosess jobs dari queue yg digenerate jobs ProcessUserUpdate
 */
class BroadcastNotif implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId,$users,$title,$message,$description;
    
    /**
     * Create a new job instance.
     *
     * @param array $userData Data user yang diupdate
     * @return void
     */
    public function __construct($userId,$users,$title,$description,$message)
    {
        $this->userId = $userId;
        $this->users = $users;
        $this->title = $title;
        $this->description = $description;
        $this->message = $message;

        // $this->connection = config('bssystem.queue_connection_ac');
        // $this->queue = 'high';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $users = UserRepo::listUser();
        foreach($users['data'] as $user){
            if($this->userId!=$user['id'])
                UserRepo::sendAdminMessage($user['id'],$this->title,$this->message,['description'=>$this->description]);
        }
        
    }
}

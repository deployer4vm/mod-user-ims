<?php

namespace hpsynapse\moduser\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Client;

use Facades\hpsynapse\moduser\Repositories\UserRepo;

/**
 * prosess jobs dari queue yg digenerate jobs ProcessUserUpdate
 */
class BroadcastNotif implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $users,$title,$message,$description;
    
    /**
     * Create a new job instance.
     *
     * @param array $userData Data user yang diupdate
     * @return void
     */
    public function __construct($users,$title,$description,$message)
    {
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
            UserRepo::sendAdminMessage($user['id'],$this->title,$this->message,['description'=>$this->description]);
        }
        
    }
}

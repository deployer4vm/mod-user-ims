<?php

namespace BSSystem\LIBAccount\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use Facades\BSSystem\LIBAccount\Repositories\UserRepo;

class UserActivation extends Mailable
{
    use Queueable, SerializesModels;
    
    protected $userId,$appsId,$isSecondary;
    
    
    /**
     * Create a new message instance.
     * 
     * @param type $userId
     * @param type $appsId
     * @param type $isSecondary
     */
    public function __construct($userId,$appsId,$isSecondary=false)
    {
        $this->userId = $userId;
        $this->appsId = $appsId;
        $this->isSecondary = $isSecondary;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $userData = UserRepo::verificationEmailDataFormat($this->userId,$this->appsId,$this->isSecondary);
        return $this->subject(__('email.useractivation_subject'))->view('user.emails.userActivation')->with($userData);
    }
}

<?php

namespace hpsynapse\moduser\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use Facades\BSSystem\LIBAccount\Repositories\UserRepo;

class EmailVerification extends Mailable
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
        return $this->subject(__('email.verification_subject'))->view('user.emails.emailverify')->with($userData);
    }

}
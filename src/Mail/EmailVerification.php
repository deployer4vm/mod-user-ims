<?php

namespace hpsynapse\moduser\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use hpsynapse\moduser\Facades\UserRepo;

class EmailVerification extends Mailable
{
    use Queueable, SerializesModels;
    
    protected $userId,$isSecondary;
    
    /**
     * Create a new message instance.
     * 
     * @param type $userId
     * @param type $isSecondary
     */
    public function __construct($userId,$isSecondary=false)
    {
        $this->userId = $userId;
        $this->isSecondary = $isSecondary;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $userData = UserRepo::verificationEmailDataFormat($this->userId,$this->isSecondary);
        return $this->subject(__('email.verification_subject'))->view('user.emails.emailverify')->with($userData);
    }

}
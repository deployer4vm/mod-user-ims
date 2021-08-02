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
    
    protected $userId,$isSecondary,$appName,$appDomain,$appUrl;
    
    /**
     * Create a new message instance.
     * 
     * @param type $userId
     * @param type $isSecondary
     */
    public function __construct($userId,$isSecondary=false,$appName='',$appDomain='',$appUrl='')
    {
        $this->userId = $userId;
        $this->isSecondary = $isSecondary;
        $this->appName = $appName;
        $this->appDomain = $appDomain;
        $this->appUrl = $appUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $userData = UserRepo::verificationEmailDataFormat($this->userId,$this->isSecondary);
        $userData['app_name'] = $this->appName;
        $userData['app_domain'] = $this->appDomain;
        $userData['app_url'] = $this->appUrl;
        
        return $this->subject(__('auth.register.verification_mail.subject',['website'=>$userData['app_domain']]))->view('user.emails.emailVerify')->with($userData);
    }

}
<?php

namespace hpsynapse\moduser\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
// use App\Mailling;

use hpsynapse\moduser\Facades\UserRepo;

class UserResetPassword extends Mailable
{
    use Queueable, SerializesModels;
    protected $userId,$appName,$appDomain,$appUrl;
    
    /**
     * Create a new message instance.
     *
     * @param array $user array data user
     *      name
     *      email
     *      resetPasswordUrl
     * @return void
     */
    public function __construct($userId,$appName='',$appDomain='',$appUrl='')
    {
        $this->userId = $userId;
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
        $userData = UserRepo::resetPasswordEmailDataFormat($this->userId);
        $userData['app_name'] = $this->appName;
        $userData['app_domain'] = $this->appDomain;
        $userData['app_url'] = $this->appUrl;
        
        return $this->subject(__('auth.forgotpassword.email.subject',['website'=>$userData['app_domain']]))->view('user.emails.userResetPassword')->with($userData);
    }
}

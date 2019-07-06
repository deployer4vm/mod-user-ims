<?php

namespace BSSystem\LIBAccount\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
// use App\Mailling;

use Facades\BSSystem\LIBAccount\Repositories\UserRepo;

class UserResetPassword extends Mailable
{
    use Queueable, SerializesModels;
    protected $userId,$appsId;
    
    /**
     * Create a new message instance.
     *
     * @param array $user array data user
     *      name
     *      email
     *      resetPasswordUrl
     * @return void
     */
    public function __construct($userId,$appsId)
    {
        $this->userId = $userId;
        $this->appsId = $appsId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $userData = UserRepo::resetPasswordEmailDataFormat($this->userId,$this->appsId);
        return $this->subject(__('email.resetpassword_subject'))->view('user.emails.userResetPassword')->with($userData);
    }
}

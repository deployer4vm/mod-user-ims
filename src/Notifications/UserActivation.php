<?php

namespace hpsynapse\moduser\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserActivation extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $userId,$isSecondary,$appName,$appDomain,$appUrl;

    /**
     * Create a new notification instance.
     *
     * @return void
     * 
     * Data :
     * Link Activation
     */
    public function __construct($userId,$isSecondary=false,$appName='',$appDomain='',$appUrl='')
    {
        $this->userId = $userId;
        $this->isSecondary = $isSecondary;
        $this->appName = $appName;
        $this->appDomain = $appDomain;
        $this->appUrl = $appUrl;
        
        // $this->connection = config('bssystem.queue_connection_ac');
        $this->queue = 'verification';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $email = new \hpsynapse\moduser\Mail\UserActivation(
            $this->userId,
            $this->isSecondary,
            $this->appName,
            $this->appDomain,
            $this->appUrl
        );
        return $email->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

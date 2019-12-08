<?php

namespace hpsynapse\moduser\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use hpsynapse\moduser\Channels\FirebaseChannels;
use hpsynapse\moduser\Channels\DbChannels;
/**
 * broad cast message dari admin ke member
 */
class AdminMessage extends Notification implements ShouldQueue
{
    use Queueable;
    protected $title, $body, $data;
    /**
     * Create a new notification instance.
     *
     * @return void
     * 
     * Data :
     * Event terbaru
     * Link event
     * 
     */
    public function __construct($title, $body, $data)
    {
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
        
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // return ['mail',DbChannels::class,FirebaseChannels::class];
        // return ['mail', DbChannels::class];
        return [DbChannels::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $email = new \hpsynapse\moduser\Mail\AdminMessage($this->title, $this->body);
        return $email->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */

    public function toDatabase($notifiable)
    {
        return [
            'subject' => $this->title,
            'description' => isset($this->data['description'])?$this->data['description']:'',
            'body' => $this->body,
            'from' => [
                'name' => isset($this->data['from']['name'])?$this->data['from']['name']:'',
                'icon' => isset($this->data['from']['icon'])?$this->data['from']['icon']:''
            ],
            'link_web' => [
                'link' => '',
                'route' => 'notification.detail',
                'parameter' => [
                    'notifId' => $this->id
                ]
            ],
            'link_apps' => isset($this->data['link_apps'])?$this->data['link_apps']:''
        ];
    }
    
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

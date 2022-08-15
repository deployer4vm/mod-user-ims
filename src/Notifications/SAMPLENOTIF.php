<?php

namespace hpsynapse\moduser\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use hpsynapse\moduser\Channels\FirebaseChannels;
use hpsynapse\moduser\Channels\AwsSNSChannels;
use hpsynapse\moduser\Channels\PusherChannels;
use hpsynapse\moduser\Channels\SmsChannels;
use hpsynapse\moduser\Channels\DbChannels;

class SAMPLENOTIF extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $someData;

    /**
     * Create a new notification instance.
     *
     * @return void
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
        $config = [DbChannels::class];

        if(config('AppConfig.packageLocal.moduser.notification.mail.enable',1))
            $config[] = 'mail';
        
        if(config('AppConfig.packageLocal.moduser.notification.sms.enable',1))
            $config[] = SmsChannels::class;

        if(config('AppConfig.packageLocal.moduser.notification.firebase.enable',1)){
            $config[] = FirebaseChannels::class;
        }else if(config('AppConfig.packageLocal.moduser.notification.pusher.enable',1)){
            $config[] = PusherChannels::class;
        }else if(config('AppConfig.packageLocal.moduser.notification.aws_sns.enable',1)){
            $config[] = AwsSNSChannels::class;
        }

        return $config;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        //create email langsung
        // return (new MailMessage)
        //             ->line('The introduction to the notification.')
        //             ->action('Notification Action', url('/'))
        //             ->line('Thank you for using our application!');
        
        // //atau menggunakan class email
        // $email = new \App\MainApp\Mail\PengajuanCreated($this->pengajuan);
        // return $email->to($notifiable->email);
        // //untuk render email template jika diperlukan
        // //$email = (new MailInvoice($this->title,$this->invoice))->render();
    }

    public function toFirebase($notifiable)
    {
        return [
            // 'topic' => 'broadcaset channel',
            'token' => $notifiable->api_token->push_token,
            'notification' => [
                'title' => $this->title,
                'body' => $this->body
            ],
            'data' => [
                'description' => isset($this->data['description'])?$this->data['description']:'',
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
                'link_apps' => isset($this->data['link_apps'])?$this->data['link_apps']:'',
            ]  
        ];
    }
    
    /**
     * Get the database array format representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return $this->toArray($notifiable);
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
}

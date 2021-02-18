<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewUserNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
       // dd('UserNotifi'+$user);
    }

    public function via($notifiable)
    {
        return ['mail'];
    }
    public function toMail($notifiable)
{
    

    return (new MailMessage)
                ->greeting('Hello!' .$this->lead)
                ->line('Welcome to Wisebrix')
                ->action('Click here to login','http://vevees.com/wisebrix/#/login')
                ->line('Thank you for using Wisebrix!');
}

  
}

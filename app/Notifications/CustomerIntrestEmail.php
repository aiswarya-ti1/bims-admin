<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CustomerIntrestEmail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
      
    }
    public function via($notifiable)
    {
        return ['mail'];
    }

  

    public function toMail($notifiable)
{
    

    return (new MailMessage)
                ->greeting('Hello!')
                ->line('You have received a interest for your quote submitted. Please wait until we call you to schedule a site visit.')
               // ->action('Click to view','http://vevees.com/wisebrix/#/assoc-login')
                ->line('Thank you for using Wisebrix!');
}
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SiteVisitShceduled extends Notification
{
    use Queueable;
    protected $WorkID;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($value)
    {
$this->date=$value;
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
   

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    
    public function toMail($notifiable)
{
    

    return (new MailMessage)
                ->greeting('Hello!')
                ->line('Your Site Ananlysis has been scheduled on '.$this->date)
                //->action('View Invoice','http://vevees.com/wisebrix/#/login')
                ->line('Thank you for using Wisebrix!');
}
}

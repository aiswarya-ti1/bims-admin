<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RequestAssocVisitEmail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($value1,$value2)
    {
        $this->CustName=$value1;
        $this->Work_ID=$value2;
    }
    public function via($notifiable)
    {
        return ['mail'];
    }

  

    public function toMail($notifiable)
{
    

    return (new MailMessage)
                ->greeting('Hello!')
                ->line($this->CustName.' Requested for Associate Visit for Work ID '.$this->Work_ID)
                ->action('Click to view','http://vevees.com/wisebrix/#/assoc-login')
                ->line('Thank you for using Wisebrix!');
}
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WorkRejectedEmail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($value1, $value2, $value3)
    
    {
        $this->AssocName=$value1;
        $this->CustName=$value2;
        $this->WorkID=$value3;
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

  

    public function toMail($notifiable)
{
   
        return (new MailMessage)
        ->greeting('Hello!')
        ->line($this->CustName.' Rejected '.$this->AssocName.' for Work '.$this->WorkID)
       // ->action('Click to view','http://vevees.com/wisebrix/#/assoc-login')
        ->line('Thank you for using Wisebrix!');
   
    }


    
}


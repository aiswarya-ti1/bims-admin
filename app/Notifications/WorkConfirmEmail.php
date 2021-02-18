<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WorkConfirmEmail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($value1, $value2, $type)
    
    {
        $this->work_ID=$value1;
        $this->CustName=$value2;
        $this->type=$type;
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
    if($this->type==1)
    {
        return (new MailMessage)
        ->greeting('Hello!')
        ->line('Work '.$this->work_ID.' Confirmed by '.$this->CustName)
       // ->action('Click to view','http://vevees.com/wisebrix/#/assoc-login')
        ->line('Thank you for using Wisebrix!');
    }
    if($this->type==2)
    {
        return (new MailMessage)
        ->greeting('Hello!')
        ->line('Your work '.$this->work_ID.' is confirmed by Customer. Please wait till the work order is generated')
       // ->action('Click to view','http://vevees.com/wisebrix/#/assoc-login')
        ->line('Thank you for using Wisebrix!');
    }


    
}
}

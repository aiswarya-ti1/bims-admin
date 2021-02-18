<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SignUpCompletedEmail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($value,$type)
    {
     
        $this->Work_ID=$value;
        $this->type=$type;
    }
    public function via($notifiable)
    {
        return ['mail'];
    }

  

    public function toMail($notifiable)
{
    
if($this->type ==1)
{
    return (new MailMessage)
    ->greeting('Hello!')
    ->line('Customer has signed up Work '.$this->Work_ID)
    //->action('Click to view','http://vevees.com/wisebrix/#/assoc-login')
    ->line('Thank you for using Wisebrix!');
}
if($this->type ==2)
{
    return (new MailMessage)
    ->greeting('Hello!')
    ->line('Associate has signed up Work '.$this->Work_ID)
    //->action('Click to view','http://vevees.com/wisebrix/#/assoc-login')
    ->line('Thank you for using Wisebrix!');
}

   
}
}

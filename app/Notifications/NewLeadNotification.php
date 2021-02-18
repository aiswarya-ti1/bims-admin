<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewLeadNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($values)
    {
        $this->lead = $values;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toArray($notifiable)
    {
        return [
            'data'=>'New Enquiry from '.$this->lead
        ];
    }

    public function toMail($notifiable)
{
    

    return (new MailMessage)
                ->greeting('Hello!')
                ->line('New Enquiry Received from '.$this->lead)
                //->action('View Invoice','http://vevees.com/wisebrix/#/login')
                ->line('Thank you for using Wisebrix!');
}


}

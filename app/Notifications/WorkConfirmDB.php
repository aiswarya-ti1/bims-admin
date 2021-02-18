<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WorkConfirmDB extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($value1,$value2,$type)
    {
        $this->Work_ID = $value1;
        $this->CustName =$value2;
        
    $this->type=$type;
    }
    public function via($notifiable)
    {
        return ['database'];
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
    public function toArray($notifiable)
    {
        if($this->type ==1)
        {
            return [
                'data' => 'Work '.$this->Work_ID.' Confirmed by '.$this->CustName ,
            ];
        }
        if($this->type ==2)
        {
            return [
                'data' => 'Your Work '.$this->Work_ID.' is Confirmed' ,
            ];
        }
        
    }
}

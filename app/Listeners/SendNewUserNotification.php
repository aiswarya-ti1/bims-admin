<?php

namespace App\Listeners;
use App\LoginUser;


use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNewUserNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        /*$admins = LoginUser::whereHas('Role_ID', function ($query) {
            $query->where('ID', 1);
        })->get();
      dd('SendNewUser'+$admins);

    Notification::send($admins, new NewUserNotification($event->user));*/
    }
}

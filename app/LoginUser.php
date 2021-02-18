<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class LoginUser extends Model
{
    use Notifiable;
    protected $table= 'users';
    protected $guarded = [];
    public $timestamps = false;
}

<?php

namespace App;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    //use HasApiTokens;
    protected $table= 'users';
    protected $guarded = [];
    public $timestamps = false;
    //protected $connection = 'secondsql';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function routeNotificationForNexmo($notification)
    {
        return $this->phone_number;
    }
    public function routeNotificationForMail($notification)
    {
        // Return email address only...
        return $this->email_address;
    }
}

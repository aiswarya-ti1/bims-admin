<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use Notifiable;
    protected $table= 'sales_lead';
    protected $guarded = [];
    public $timestamps = false;
}

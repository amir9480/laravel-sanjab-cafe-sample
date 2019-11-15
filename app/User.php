<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Sanjab\Models\SanjabUser;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, SanjabUser;

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
}

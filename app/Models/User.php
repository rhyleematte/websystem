<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'email',
        'username', // ✅ MUST be here
        'password',
        'fname',
        'mname',
        'lname',
        'gender',
        'bday',
        'role',
        'doctor_status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'bday' => 'date',
    ];
}

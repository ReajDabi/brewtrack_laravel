<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username',
        'password',
        'full_name',
        'email',
        'role',
        'is_active',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'last_login' => 'datetime',
    ];

    // Use username instead of email for login
    public function getAuthIdentifierName(): string
    {
        return 'username';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
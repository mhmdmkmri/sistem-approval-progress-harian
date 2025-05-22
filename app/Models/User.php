<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'project_id', 'pin',
    ];

    protected $hidden = [
        'password', 'remember_token', 'pin',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function progresses()
    {
        return $this->hasMany(Progress::class);
    }
}

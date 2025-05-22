<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressHistory extends Model
{
    public $timestamps = false; // gunakan action_at timestamp manual

    protected $fillable = [
        'progress_id', 'user_id', 'action', 'comment', 'action_at'
    ];

    protected $dates = ['action_at'];

    public function progress()
    {
        return $this->belongsTo(Progress::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

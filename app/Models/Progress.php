<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    protected $table = 'progress';

    protected $casts = [
        'date' => 'date', // atau 'datetime' jika termasuk waktu
    ];

    protected $fillable = [
        'user_id',
        'project_id',
        'date',
        'progress_percent',
        'evidence_path',
        'description',
        'status',
        'qr_code'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function histories()
    {
        return $this->hasMany(ProgressHistory::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'activity_id', 'activity_completed', 'quiz_completed',
        'quiz_score', 'quiz_max_score', 'completed_at',
    ];

    protected $casts = [
        'activity_completed' => 'boolean',
        'quiz_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}

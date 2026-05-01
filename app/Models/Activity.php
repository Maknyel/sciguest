<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id', 'order', 'name', 'icon', 'description',
        'procedure', 'safety_reminders', 'is_locked', 'deadline_enabled', 'deadline_at',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'deadline_enabled' => 'boolean',
        'deadline_at' => 'datetime',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function quizQuestions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function studentActivities()
    {
        return $this->hasMany(StudentActivity::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id', 'order', 'name', 'icon', 'description', 'image',
        'procedure', 'safety_reminders', 'is_locked', 'deadline_enabled', 'deadline_at',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }

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

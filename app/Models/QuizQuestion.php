<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['activity_id', 'order', 'question', 'options', 'correct_answer'];

    protected $casts = ['options' => 'array'];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}

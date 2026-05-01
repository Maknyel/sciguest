<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = ['order', 'name', 'icon', 'description', 'color'];

    public function activities()
    {
        return $this->hasMany(Activity::class)->orderBy('order');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'description', 'user_id'];

    // Projekt patrí jednému používateľovi
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Projekt má viac úloh
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}

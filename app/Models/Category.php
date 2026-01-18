<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name','color', 'user_id'];

    //kategoria patri jednemu uzivatelovi
    public function user() { return $this->belongsTo(User::class); }
    //M:n vztah kategoria ma viac uloh
    public function tasks() { return $this->belongsToMany(Task::class); }
}

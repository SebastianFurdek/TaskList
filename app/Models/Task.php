<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title','description','due_date','completed','project_id','user_id'];
    protected $casts = ['completed' => 'boolean', 'due_date' => 'date'];
    public function user() { return $this->belongsTo(User::class); }
    public function project() { return $this->belongsTo(Project::class); }
    public function categories() { return $this->belongsToMany(Category::class); }
}

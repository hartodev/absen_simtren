<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassTeacher extends Model
{
    protected $guarded = [];

    public function classRoom() { return $this->belongsTo(ClassRoom::class, 'class_id'); }
    public function user() { return $this->belongsTo(User::class); }
}

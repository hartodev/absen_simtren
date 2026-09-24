<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentGuardian extends Model
{
    protected $guarded = [];
    protected $casts = ['is_primary' => 'boolean', 'can_submit_permission' => 'boolean'];

    public function student() { return $this->belongsTo(Student::class); }
    public function user() { return $this->belongsTo(User::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    protected $table = 'class_rooms';
    protected $guarded = [];
    protected $casts = ['is_active' => 'boolean'];

    public function company() { return $this->belongsTo(Company::class); }
    public function students() { return $this->hasMany(Student::class, 'class_id'); }
    public function homeroomTeacher() { return $this->belongsTo(User::class, 'homeroom_teacher_id'); }
    public function teachers() { return $this->hasMany(ClassTeacher::class, 'class_id'); }
    public function attendances() { return $this->hasMany(StudentAttendance::class, 'class_id'); }
}

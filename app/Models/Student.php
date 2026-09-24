<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $casts = [
        'birth_date'  => 'date',
        'enrolled_at' => 'date',
        'is_boarding' => 'boolean',
        'is_active'   => 'boolean',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function classRoom() { return $this->belongsTo(ClassRoom::class, 'class_id'); }
    public function guardians() { return $this->hasMany(StudentGuardian::class); }
    public function attendances() { return $this->hasMany(StudentAttendance::class); }
    public function permissions() { return $this->hasMany(StudentPermission::class); }
    public function boardingPermissions() { return $this->hasMany(BoardingPermission::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    public const STATUSES = ['hadir', 'terlambat', 'izin', 'sakit', 'alpa'];

    protected $guarded = [];
    protected $casts = ['date' => 'date'];

    public function student() { return $this->belongsTo(Student::class); }
    public function classRoom() { return $this->belongsTo(ClassRoom::class, 'class_id'); }
    public function device() { return $this->belongsTo(AttendanceDevice::class, 'device_id'); }
    public function recorder() { return $this->belongsTo(User::class, 'recorded_by'); }
}

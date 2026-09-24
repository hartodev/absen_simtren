<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AttendanceDevice extends Model
{
    protected $guarded = [];
    protected $hidden = ['device_token'];
    protected $casts = ['is_active' => 'boolean', 'last_seen_at' => 'datetime'];

    public static function newToken(): string
    {
        return Str::random(48);
    }

    public function company() { return $this->belongsTo(Company::class); }
    public function classRoom() { return $this->belongsTo(ClassRoom::class, 'class_id'); }
    public function registrar() { return $this->belongsTo(User::class, 'registered_by'); }
}

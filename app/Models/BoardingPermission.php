<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoardingPermission extends Model
{
    protected $guarded = [];
    protected $casts = [
        'tanggal_keluar'          => 'date',
        'tanggal_kembali_rencana' => 'date',
        'tanggal_kembali_aktual'  => 'date',
        'reviewed_at'             => 'datetime',
    ];

    public function student() { return $this->belongsTo(Student::class); }
    public function submitter() { return $this->belongsTo(User::class, 'submitted_by'); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
}

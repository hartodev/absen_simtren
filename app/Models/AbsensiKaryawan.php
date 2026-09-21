<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiKaryawan extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $table = 'absensi_karyawan';

    protected $fillable = [
        'tenant_id',
        'karyawan_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'lokasi_masuk',
        'lokasi_pulang',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function sudahCheckIn(): bool
    {
        return ! empty($this->jam_masuk);
    }

    public function sudahCheckOut(): bool
    {
        return ! empty($this->jam_pulang);
    }
}

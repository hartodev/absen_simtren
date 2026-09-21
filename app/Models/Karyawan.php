<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $table = 'karyawan';

    protected $fillable = [
        'tenant_id',
        'nama',
        'nip',
        'jabatan',
        'departemen',
        'no_hp',
        'email',
        'jam_masuk',
        'jam_pulang',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
        'jam_masuk' => 'string',
        'jam_pulang' => 'string',
    ];

    public function absensi()
    {
        return $this->hasMany(AbsensiKaryawan::class);
    }

    /**
     * Absensi karyawan ini untuk hari ini (kalau ada).
     */
    public function absensiHariIni()
    {
        return $this->hasOne(AbsensiKaryawan::class)->whereDate('tanggal', now()->toDateString());
    }
}

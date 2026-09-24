<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutabaahYaumiyah extends Model
{
    /** Nilai yang dianggap "lanjut" (naik halaman). Selain ini = mengulang. */
    public const LANJUT = ['A+', 'A', 'A-', 'B+', 'B'];

    protected $table = 'mutabaah_yaumiyahs';
    protected $guarded = [];
    protected $casts = ['tanggal' => 'date', 'signed_at' => 'datetime', 'is_lanjut' => 'boolean'];

    protected static function booted(): void
    {
        // is_lanjut otomatis dari nilai, kecuali sudah diisi manual (override ustadz).
        static::creating(function (self $m) {
            if ($m->is_lanjut === null) {
                $m->is_lanjut = in_array($m->keterangan, self::LANJUT, true);
            }
        });
    }

    public function scopeHariIni($q) { return $q->whereDate('tanggal', today()); }
    public function scopeBulan($q, int $bulan, int $tahun) { return $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun); }
    public function scopeOfSantri($q, int $id) { return $q->where('santri_id', $id); }

    public function santri() { return $this->belongsTo(User::class, 'santri_id'); }
    public function ustadz() { return $this->belongsTo(User::class, 'ustadz_id'); }
    public function signer() { return $this->belongsTo(User::class, 'signed_by'); }
}

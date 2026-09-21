<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $table = 'presensi';
    protected $fillable = ['tenant_id', 'siswa_id', 'tanggal', 'status'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

}
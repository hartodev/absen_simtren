<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;
     use BelongsToTenant;

    protected $table = 'kelas';
    protected $fillable = ['tenant_id', 'nama_kelas'];

    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }

}
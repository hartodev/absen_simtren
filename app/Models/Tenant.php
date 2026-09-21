<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_lembaga',
        'subdomain',
        'logo',
        'warna_tema',
        'paket',
        'status',
        'publik',
        'tipe_lembaga',
        'deskripsi',
    ];

    protected $casts = [
        'publik' => 'boolean',
        'tipe_lembaga' => 'string',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KaryawanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'nama' => $this->nama,
            'nip' => $this->nip,
            'jabatan' => $this->jabatan,
            'departemen' => $this->departemen,
            'no_hp' => $this->no_hp,
            'email' => $this->email,
            'jam_masuk' => $this->jam_masuk,
            'jam_pulang' => $this->jam_pulang,
            'status_aktif' => (bool) $this->status_aktif,
            'absensi_hari_ini' => new AbsensiKaryawanResource($this->whenLoaded('absensiHariIni')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

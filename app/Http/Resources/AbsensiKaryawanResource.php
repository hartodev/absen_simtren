<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AbsensiKaryawanResource extends JsonResource
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
            'karyawan_id' => $this->karyawan_id,
            'karyawan' => [
                'id' => $this->whenLoaded('karyawan', fn () => $this->karyawan->id),
                'nama' => $this->whenLoaded('karyawan', fn () => $this->karyawan->nama),
                'jabatan' => $this->whenLoaded('karyawan', fn () => $this->karyawan->jabatan),
            ],
            'tanggal' => $this->tanggal?->toDateString(),
            'jam_masuk' => $this->jam_masuk,
            'jam_pulang' => $this->jam_pulang,
            'lokasi_masuk' => $this->lokasi_masuk,
            'lokasi_pulang' => $this->lokasi_pulang,
            'status' => $this->status,
            'keterangan' => $this->keterangan,
            'sudah_check_in' => $this->sudahCheckIn(),
            'sudah_check_out' => $this->sudahCheckOut(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

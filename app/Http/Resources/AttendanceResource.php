<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Bentuk data absensi yang konsisten, dipakai bersama oleh:
 *   - Web/Employee/EmployeeAttendanceWebController (versi web, subdomain tenant)
 *   - Api/Employee/EmployeeAttendanceController   (versi mobile, sudah ada)
 *
 * Supaya kartu "detail absensi" di halaman tenant manapun (company / tpq /
 * sekolah) selalu punya field yang sama, biarpun sumber & role-nya beda.
 */
class AttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => (int) $this->id,
            'user_id'             => (int) $this->user_id,
            'company_id'          => (int) $this->company_id,
            'date'                => optional($this->date)->format('Y-m-d') ?? $this->date,
            'shift_name'          => $this->whenLoaded('shift', fn () => $this->shift?->name),
            'check_in_time'       => $this->time_in,
            'check_out_time'      => $this->time_out,
            'scheduled_in'        => $this->scheduled_in,
            'scheduled_out'       => $this->scheduled_out,
            'late_minutes'        => (int) ($this->late_minutes ?? 0),
            'early_leave_minutes' => (int) ($this->early_leave_minutes ?? 0),
            'overtime_minutes'    => (int) ($this->overtime_minutes ?? 0),
            'status'              => $this->status,
            'face_verified'       => (bool) ($this->face_verified ?? false),
            'latlon_in'           => $this->latlon_in,
            'latlon_out'          => $this->latlon_out,
        ];
    }
}

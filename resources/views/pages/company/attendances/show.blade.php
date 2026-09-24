@extends('layouts.company')

@section('title', 'Detail Absensi')

@section('content')
<div class="min-h-screen bg-gray-50">

    <div class="bg-blue-800 px-5 pt-6 pb-8 rounded-b-3xl">
        <div class="flex items-center gap-3">
            <a href="{{ route('company.attendances.index') }}" class="text-white">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-white text-xl font-bold">Detail Absensi</h1>
        </div>
    </div>

    <div class="px-5 -mt-4 pb-6">

        {{-- Profil --}}
        <div class="bg-white rounded-2xl p-4 shadow-sm flex items-center gap-3">
            <div
                class="w-12 h-12 rounded-full bg-blue-800 text-white flex items-center justify-center font-semibold text-lg shrink-0">
                {{ strtoupper(substr($attendance->user->name ?? '-', 0, 1)) }}
            </div>
            <div>
                <p class="font-semibold text-gray-900">{{ $attendance->user->name ?? '-' }}</p>
                <p class="text-sm text-gray-500">{{ $attendance->user->email ?? '-' }}</p>
            </div>
        </div>

        {{-- Ringkasan waktu --}}
        <div class="grid grid-cols-2 gap-3 mt-4">
            <div class="bg-white rounded-2xl p-4 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Tanggal</p>
                <p class="font-semibold text-gray-900">
                    {{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('d F Y') }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Status</p>
                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium
                    @class([
                        'bg-green-100 text-green-700' => $attendance->status === 'on_time',
                        'bg-amber-100 text-amber-700' => $attendance->status === 'late',
                        'bg-blue-100 text-blue-700'   => $attendance->status === 'overtime',
                        'bg-gray-100 text-gray-500'    => in_array($attendance->status, ['absent', null], true),
                    ])
                >
                    {{ $attendance->status ?? '-' }}
                </span>
            </div>

            <div class=" bg-white rounded-2xl p-4 shadow-sm">
                    <p class="text-xs text-gray-500 mb-1">Check-in</p>
                    <p class="font-semibold text-gray-900">{{ $attendance->time_in ?? '-' }}</p>
                    <p class="text-xs text-gray-400">Jadwal: {{ $attendance->scheduled_in ?? '-' }}</p>
            </div>
            <div class="bg-white rounded-2xl p-4 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Check-out</p>
                <p class="font-semibold text-gray-900">{{ $attendance->time_out ?? 'Belum checkout' }}</p>
                <p class="text-xs text-gray-400">Jadwal: {{ $attendance->scheduled_out ?? '-' }}</p>
            </div>

            <div class="bg-white rounded-2xl p-4 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Terlambat</p>
                <p class="font-semibold text-gray-900">{{ $attendance->late_minutes ?? 0 }} menit</p>
            </div>
            <div class="bg-white rounded-2xl p-4 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Pulang Lebih Awal</p>
                <p class="font-semibold text-gray-900">{{ $attendance->early_leave_minutes ?? 0 }} menit</p>
            </div>

            @if($attendance->shift)
            <div class="col-span-2 bg-white rounded-2xl p-4 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Shift</p>
                <p class="font-semibold text-gray-900">
                    {{ $attendance->shift->name }}
                    ({{ $attendance->shift->start_time }} &ndash; {{ $attendance->shift->end_time }})
                </p>
            </div>
            @endif

            <div class="col-span-2 bg-white rounded-2xl p-4 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Verifikasi Wajah</p>
                <p class="font-semibold {{ $attendance->face_verified ? 'text-green-600' : 'text-gray-400' }}">
                    {{ $attendance->face_verified ? 'Terverifikasi' : 'Belum terverifikasi' }}
                </p>
            </div>

            @if($attendance->latlon_in)
            <div class="col-span-2 bg-white rounded-2xl p-4 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Lokasi Check-in</p>
                <p class="font-mono text-sm text-gray-700">{{ $attendance->latlon_in }}</p>
            </div>
            @endif

            @if($attendance->latlon_out)
            <div class="col-span-2 bg-white rounded-2xl p-4 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Lokasi Check-out</p>
                <p class="font-mono text-sm text-gray-700">{{ $attendance->latlon_out }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
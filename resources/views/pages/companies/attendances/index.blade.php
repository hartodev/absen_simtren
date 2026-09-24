@extends('layouts.tenant')

@section('title', 'Absensi — Daftar Karyawan')

@section('content')
<div class="min-h-screen bg-gray-50">

    <div class="bg-blue-800 px-5 pt-6 pb-8 rounded-b-3xl">
        <div class="flex items-center gap-3">
            <a href="{{ route('company.dashboard') }}" class="text-white">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-white text-xl font-bold">Daftar Karyawan Hari Ini</h1>
        </div>
    </div>

    <div class="px-5 -mt-4">

        {{-- Filter --}}
        <form method="GET" action="{{ route('company.attendances.index') }}"
            class="bg-white rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / email / posisi..."
                class="flex-1 border border-gray-200 rounded-xl px-3 py-2 text-sm">
            <input type="date" name="date" value="{{ $date }}"
                class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
            <button type="submit" class="bg-blue-800 text-white rounded-xl px-4 py-2 text-sm font-medium">
                Filter
            </button>
        </form>

        {{-- Summary --}}
        <div class="grid grid-cols-4 gap-2 mt-4">
            <div class="bg-white rounded-xl p-3 text-center shadow-sm">
                <p class="text-lg font-bold text-gray-900">{{ $summary['total'] }}</p>
                <p class="text-xs text-gray-500">Total</p>
            </div>
            <div class="bg-white rounded-xl p-3 text-center shadow-sm">
                <p class="text-lg font-bold text-green-600">{{ $summary['hadir'] }}</p>
                <p class="text-xs text-gray-500">Hadir</p>
            </div>
            <div class="bg-white rounded-xl p-3 text-center shadow-sm">
                <p class="text-lg font-bold text-amber-600">{{ $summary['terlambat'] }}</p>
                <p class="text-xs text-gray-500">Terlambat</p>
            </div>
            <div class="bg-white rounded-xl p-3 text-center shadow-sm">
                <p class="text-lg font-bold text-gray-400">{{ $summary['belum_hadir'] }}</p>
                <p class="text-xs text-gray-500">Belum Hadir</p>
            </div>
        </div>

        {{-- List --}}
        <div class="mt-4 space-y-3 pb-6">
            @forelse($rows as $row)
            <div class="bg-white rounded-2xl p-4 shadow-sm flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-full bg-blue-800 text-white flex items-center justify-center font-semibold shrink-0">
                    {{ strtoupper(substr($row['name'], 0, 1)) }}
                </div>

                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 truncate">{{ $row['name'] }}</p>
                    <p class="text-sm text-gray-500 truncate">{{ $row['email'] }}</p>

                    @if($row['checked_in'])
                    <span
                        class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $row['checked_out'] ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ $row['checked_out'] ? 'Sudah Checkout' : 'Belum Checkout' }}
                    </span>
                    @if($row['status'] === 'late')
                    <span
                        class="inline-flex items-center gap-1 mt-1 ml-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600">
                        Terlambat {{ $row['late_minutes'] }} menit
                    </span>
                    @endif
                    @else
                    <span
                        class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                        Belum Hadir
                    </span>
                    @endif
                </div>

                <div class="text-right shrink-0 text-sm">
                    @if($row['time_in'])
                    <p class="text-green-600 font-medium">&rarr;
                        {{ \Illuminate\Support\Str::substr($row['time_in'], 0, 5) }}</p>
                    @endif
                    @if($row['time_out'])
                    <p class="text-orange-500 font-medium">&larr;
                        {{ \Illuminate\Support\Str::substr($row['time_out'], 0, 5) }}</p>
                    @endif

                    @if($row['attendance_id'])
                    <a href="{{ route('company.attendances.show', $row['attendance_id']) }}"
                        class="text-xs text-blue-700 font-medium">Detail</a>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-10">Tidak ada karyawan yang cocok.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
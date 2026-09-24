@extends('layouts.mobile')
@section('title', 'Rekap Absensi')
@section('back', route($rp.'dashboard'))

@section('content')
@php
    $labels = ['hadir' => 'Hadir', 'terlambat' => 'Terlambat', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa'];
    $colors = ['hadir' => 'text-emerald-700', 'terlambat' => 'text-amber-600', 'izin' => 'text-sky-700', 'sakit' => 'text-violet-700', 'alpa' => 'text-red-700'];
@endphp

@if (! $class)
    <div class="rounded-2xl bg-white px-4 py-10 text-center text-sm text-slate-400">
        Belum ada kelas aktif. <a class="font-semibold underline" href="{{ route($rp.'classes.create') }}">Buat kelas dulu</a>.
    </div>
@else
    <form method="GET" class="grid grid-cols-2 gap-2">
        <select name="class_id" onchange="this.form.submit()" class="input !py-2 text-sm">
            @foreach ($classes as $c)<option value="{{ $c->id }}" @selected($c->id === $class->id)>Kelas {{ $c->name }}</option>@endforeach
        </select>
        <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()" class="input !py-2 text-sm">
    </form>

    <div class="mt-3 grid grid-cols-5 gap-1.5">
        @foreach ($labels as $k => $l)
            <div class="rounded-xl bg-white py-2 text-center shadow-sm">
                <p class="text-lg font-semibold {{ $colors[$k] }}">{{ $summary[$k] }}</p>
                <p class="text-[10px] text-slate-400">{{ $l }}</p>
            </div>
        @endforeach
    </div>

    @if ($students->isEmpty())
        <div class="mt-4 rounded-2xl bg-white px-4 py-10 text-center text-sm text-slate-400">Belum ada murid di kelas ini.</div>
    @else
        <form method="POST" action="{{ route($rp.'student-attendances.store') }}" class="mt-3">
            @csrf
            <input type="hidden" name="class_id" value="{{ $class->id }}">
            <input type="hidden" name="date" value="{{ $date }}">

            <div class="divide-y divide-slate-100 rounded-2xl bg-white shadow-sm ring-1 ring-black/5">
                @foreach ($students as $s)
                    @php $rec = $records->get($s->id); @endphp
                    <div class="flex items-center gap-2 px-3 py-2.5">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-800">{{ $s->name }}</p>
                            <p class="text-[11px] text-slate-400">NIS {{ $s->nis }}@if ($rec?->check_in_time) · masuk {{ substr($rec->check_in_time, 0, 5) }}@endif</p>
                        </div>
                        <select name="status[{{ $s->id }}]" class="rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs font-semibold">
                            <option value="">{{ $rec ? '' : '— belum —' }}</option>
                            @foreach ($labels as $k => $l)
                                <option value="{{ $k }}" @selected($rec?->status === $k)>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
            </div>

            <button class="btn btn-brand mt-3 w-full py-3">Simpan absensi</button>
            <p class="mt-2 text-center text-[11px] text-slate-400">Yang dibiarkan "belum" tidak diubah. Absen dari kiosk tetap tampil di sini.</p>
        </form>
    @endif
@endif
@endsection

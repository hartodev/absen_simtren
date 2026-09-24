@extends('layouts.admin')
@section('title', 'Rekap Absensi')
@section('subtitle', 'Absensi murid per kelas & tanggal')

@section('content')
@php
    $labels = ['hadir' => 'Hadir', 'terlambat' => 'Terlambat', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa'];
    $colors = ['hadir' => 'text-emerald-700', 'terlambat' => 'text-amber-600', 'izin' => 'text-sky-700', 'sakit' => 'text-violet-700', 'alpa' => 'text-red-700'];
@endphp

@if (! $class)
    <div class="card empty">Belum ada kelas aktif. <a class="font-semibold underline" href="{{ route($rp.'classes.create') }}">Buat kelas dulu</a>.</div>
@else
    <form method="GET" class="card mb-4 flex flex-col gap-3 p-4 sm:flex-row">
        <select name="class_id" onchange="this.form.submit()" class="input sm:w-64">
            @foreach ($classes as $c)<option value="{{ $c->id }}" @selected($c->id === $class->id)>Kelas {{ $c->name }}</option>@endforeach
        </select>
        <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()" class="input sm:w-48">
    </form>

    <div class="mb-4 grid grid-cols-2 gap-3 sm:grid-cols-5">
        @foreach ($labels as $k => $l)
            <div class="card px-4 py-3 text-center">
                <p class="text-2xl font-bold {{ $colors[$k] }}">{{ $summary[$k] }}</p>
                <p class="text-xs text-slate-500">{{ $l }}</p>
            </div>
        @endforeach
    </div>

    @if ($students->isEmpty())
        <div class="card empty">Belum ada murid di kelas ini.</div>
    @else
        <form method="POST" action="{{ route($rp.'student-attendances.store') }}">
            @csrf
            <input type="hidden" name="class_id" value="{{ $class->id }}">
            <input type="hidden" name="date" value="{{ $date }}">

            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="tbl">
                        <thead><tr><th class="w-12">#</th><th>Murid</th><th>NIS</th><th>Jam masuk</th><th class="w-48">Status</th></tr></thead>
                        <tbody>
                            @foreach ($students as $s)
                                @php $rec = $records->get($s->id); @endphp
                                <tr>
                                    <td class="text-slate-400">{{ $loop->iteration }}</td>
                                    <td class="font-semibold text-slate-800">{{ $s->name }}</td>
                                    <td class="text-slate-500">{{ $s->nis }}</td>
                                    <td class="text-slate-500">{{ $rec?->check_in_time ? substr($rec->check_in_time, 0, 5) : '—' }}</td>
                                    <td>
                                        <select name="status[{{ $s->id }}]" class="input !py-1.5">
                                            <option value="">{{ $rec ? '' : '— belum —' }}</option>
                                            @foreach ($labels as $k => $l)
                                                <option value="{{ $k }}" @selected($rec?->status === $k)>{{ $l }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 flex flex-col-reverse items-start gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs text-slate-400">Yang dibiarkan "belum" tidak diubah. Absen dari kiosk tetap tampil di sini.</p>
                <button class="btn btn-brand px-6"><i data-lucide="save" class="h-4 w-4"></i> Simpan absensi</button>
            </div>
        </form>
    @endif
@endif
@endsection

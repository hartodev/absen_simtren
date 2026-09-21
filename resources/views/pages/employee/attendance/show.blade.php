@extends('layouts.employee')

@section('title', 'Detail Absensi')

@section('breadcrumb')
    <a href="{{ route('employee.attendance.index') }}">Absensi</a>
    <span class="current">Detail</span>
@endsection

@section('content')
<div class="page-title">Detail Absensi</div>
<div class="page-sub">{{ \Carbon\Carbon::parse($attendance['date'])->isoFormat('dddd, D MMMM Y') }}</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Ringkasan</span>
        @php $s = $attendance['status'] ?? 'unknown'; @endphp
        @if($s === 'on_time') <span class="badge badge-success">Tepat Waktu</span>
        @elseif($s === 'late') <span class="badge badge-warning">Terlambat</span>
        @elseif($s === 'absent') <span class="badge badge-danger">Alpha</span>
        @elseif($s === 'on_leave') <span class="badge badge-info">Cuti</span>
        @elseif($s === 'permitted') <span class="badge badge-info">Izin</span>
        @else <span class="badge badge-gray">{{ $s }}</span>
        @endif
    </div>
    <div class="card-body">
        <div class="metrics">
            <div class="metric">
                <div class="metric-label">Shift</div>
                <div class="metric-val">{{ $attendance['shift_name'] ?? '—' }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Jam Masuk</div>
                <div class="metric-val">{{ $attendance['check_in_time'] ?? '—' }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Jam Keluar</div>
                <div class="metric-val">{{ $attendance['check_out_time'] ?? '—' }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Terlambat</div>
                <div class="metric-val warning">{{ $attendance['late_minutes'] }} mnt</div>
            </div>
            <div class="metric">
                <div class="metric-label">Pulang Awal</div>
                <div class="metric-val warning">{{ $attendance['early_leave_minutes'] }} mnt</div>
            </div>
            <div class="metric">
                <div class="metric-label">Lembur</div>
                <div class="metric-val info">{{ $attendance['overtime_minutes'] }} mnt</div>
            </div>
        </div>

        <div style="margin-top:18px;font-size:13px;color:var(--c-muted)">
            <div>Lokasi Masuk: {{ $attendance['latlon_in'] ?? '—' }}</div>
            <div>Lokasi Keluar: {{ $attendance['latlon_out'] ?? '—' }}</div>
        </div>

        <a href="{{ route('employee.attendance.index') }}" class="btn btn-outline" style="margin-top:20px">
            &larr; Kembali ke Riwayat
        </a>
    </div>
</div>
@endsection

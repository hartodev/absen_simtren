@extends('layouts.employee')

@section('title', 'Dashboard')

@section('content')
<div class="page-title">Halo, {{ $user->name }}</div>
<div class="page-sub">{{ \Carbon\Carbon::parse($today)->isoFormat('dddd, D MMMM Y') }}</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:14px">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger" style="margin-bottom:14px">{{ session('error') }}</div>
@endif

{{-- Status absensi hari ini --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Status Hari Ini</span>
        @if($attendance && $attendance->time_in && $attendance->time_out)
        <span class="badge badge-success">Selesai</span>
        @elseif($attendance && $attendance->time_in)
        <span class="badge badge-warning">Belum Checkout</span>
        @else
        <span class="badge badge-gray">Belum Check-in</span>
        @endif
    </div>
    <div class="card-body">
        <div class="metrics">
            <div class="metric">
                <div class="metric-label">Shift</div>
                <div class="metric-val">{{ $shift->name ?? 'Default' }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Jadwal Masuk</div>
                <div class="metric-val">{{ $scheduledIn?->format('H:i') ?? '—' }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Jadwal Pulang</div>
                <div class="metric-val">{{ $scheduledOut?->format('H:i') ?? '—' }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Jam Masuk</div>
                <div class="metric-val">{{ $attendance->time_in ?? '—' }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Jam Keluar</div>
                <div class="metric-val">{{ $attendance->time_out ?? '—' }}</div>
            </div>
        </div>

        <a href="{{ route('company.member.attendance.index') }}" class="btn btn-outline"
            style="margin-top:18px;width:100%;text-align:center">
            Buka Absensi &rarr;
        </a>
    </div>
</div>

{{-- Ringkasan bulan ini --}}
<div class="card" style="margin-top:16px">
    <div class="card-header">
        <span class="card-title">Ringkasan Bulan Ini</span>
    </div>
    <div class="card-body">
        <div class="metrics">
            <div class="metric">
                <div class="metric-label">Hadir</div>
                <div class="metric-val">{{ $summary['hadir'] }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Terlambat</div>
                <div class="metric-val warning">{{ $summary['terlambat'] }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Izin</div>
                <div class="metric-val info">{{ $summary['izin'] }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Cuti</div>
                <div class="metric-val info">{{ $summary['cuti'] }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Alpha</div>
                <div class="metric-val warning">{{ $summary['alpha'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Menu cepat --}}
<div class="card" style="margin-top:16px">
    <div class="card-header">
        <span class="card-title">Menu Lainnya</span>
    </div>
    <div class="card-body" style="padding:0">
        @foreach($menu as $item)
        @if($item['active'])
        <a href="{{ route($item['route']) }}" class="menu-row">
            <span>{{ $item['label'] }}</span>
            <span>&rarr;</span>
        </a>
        @else
        <div class="menu-row menu-row-disabled">
            <span>{{ $item['label'] }}</span>
            <span class="badge badge-gray">Segera Hadir</span>
        </div>
        @endif
        @endforeach
    </div>
</div>

<style>
.menu-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border-bottom: 1px solid var(--c-border, #eee);
    text-decoration: none;
    color: inherit;
    font-size: 14px;
    font-weight: 600
}

.menu-row:last-child {
    border-bottom: none
}

.menu-row-disabled {
    color: var(--c-muted, #8a94a6);
    font-weight: 500
}

.alert {
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600
}

.alert-success {
    background: #e0f4ea;
    color: #198754
}

.alert-danger {
    background: #fff1f1;
    color: #b34040
}
</style>
@endsection
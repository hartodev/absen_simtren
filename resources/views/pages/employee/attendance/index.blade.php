@extends('layouts.employee')

@section('title', 'Absensi')

@section('breadcrumb')
<a href="{{ route('company.member.dashboard') }}">Dashboard</a>
<span class="current">Absensi</span>
@endsection

@section('content')
<div class="page-title">Absensi</div>
<div class="page-sub">{{ \Carbon\Carbon::parse($today)->isoFormat('dddd, D MMMM Y') }}</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:14px">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger" style="margin-bottom:14px">{{ session('error') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger" style="margin-bottom:14px">{{ $errors->first() }}</div>
@endif

@if($faceMissing)
<div class="alert alert-danger" style="margin-bottom:14px">
    Anda belum registrasi wajah. Silakan registrasi wajah lewat aplikasi mobile sebelum bisa check-in.
</div>
@endif

<div class="card">
    <div class="card-header">
        <span class="card-title">Jadwal Hari Ini</span>
        <span class="badge badge-info">{{ $shift->name ?? 'Default' }}</span>
    </div>
    <div class="card-body">
        <div class="metrics">
            <div class="metric">
                <div class="metric-label">Jadwal Masuk</div>
                <div class="metric-val">{{ $scheduledIn?->format('H:i') ?? '—' }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Jadwal Pulang</div>
                <div class="metric-val">{{ $scheduledOut?->format('H:i') ?? '—' }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Check-in / Check-out --}}
<div class="card" style="margin-top:16px">
    <div class="card-header">
        <span class="card-title">Presensi</span>
    </div>
    <div class="card-body">

        <div id="geo-status" class="page-sub" style="margin-bottom:10px">Mendeteksi lokasi...</div>

        @if(!$attendance || !$attendance->time_in)
        <form id="checkin-form" method="POST" action="{{ route('company.member.attendance.checkin') }}">
            @csrf
            <input type="hidden" name="latitude" id="checkin-lat">
            <input type="hidden" name="longitude" id="checkin-lng">
            <button type="submit" id="checkin-btn" class="btn" style="width:100%" disabled>
                Check-In Sekarang
            </button>
        </form>
        @elseif(!$attendance->time_out)
        <p style="font-size:13px;color:var(--c-muted);margin-bottom:10px">
            Sudah check-in pukul <strong>{{ $attendance->time_in }}</strong>
        </p>
        <form id="checkout-form" method="POST" action="{{ route('company.member.attendance.checkout') }}">
            @csrf
            <input type="hidden" name="latitude" id="checkout-lat">
            <input type="hidden" name="longitude" id="checkout-lng">
            <button type="submit" id="checkout-btn" class="btn" style="width:100%" disabled>
                Check-Out Sekarang
            </button>
        </form>
        @else
        <div class="metrics">
            <div class="metric">
                <div class="metric-label">Jam Masuk</div>
                <div class="metric-val">{{ $attendance->time_in }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Jam Keluar</div>
                <div class="metric-val">{{ $attendance->time_out }}</div>
            </div>
        </div>
        <p style="font-size:13px;color:var(--c-muted);margin-top:10px">
            Absensi hari ini sudah lengkap. Sampai jumpa besok!
        </p>
        @endif
    </div>
</div>

{{-- Riwayat --}}
<div class="card" style="margin-top:16px">
    <div class="card-header">
        <span class="card-title">Riwayat Absensi</span>
    </div>
    <div class="card-body" style="padding:0">
        @forelse($history as $item)
        <a href="{{ route('company.member.attendance.show', $item->id) }}" class="menu-row">
            <span>
                {{ \Carbon\Carbon::parse($item->date)->isoFormat('D MMM Y') }}
                <br>
                <small style="font-weight:400;color:var(--c-muted)">
                    {{ $item->time_in ?? '—' }} - {{ $item->time_out ?? '—' }}
                </small>
            </span>
            @php $s = $item->status; @endphp
            @if($s === 'on_time') <span class="badge badge-success">Tepat Waktu</span>
            @elseif($s === 'late') <span class="badge badge-warning">Terlambat</span>
            @elseif($s === 'absent') <span class="badge badge-danger">Alpha</span>
            @else <span class="badge badge-gray">{{ $s ?? '—' }}</span>
            @endif
        </a>
        @empty
        <div class="menu-row menu-row-disabled">Belum ada riwayat absensi.</div>
        @endforelse
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

<script>
(function() {
    var statusEl = document.getElementById('geo-status');
    var buttons = [document.getElementById('checkin-btn'), document.getElementById('checkout-btn')].filter(Boolean);

    if (!navigator.geolocation) {
        statusEl.textContent = 'Browser tidak mendukung deteksi lokasi.';
        return;
    }

    navigator.geolocation.getCurrentPosition(function(pos) {
        var lat = pos.coords.latitude;
        var lng = pos.coords.longitude;

        ['checkin', 'checkout'].forEach(function(prefix) {
            var latEl = document.getElementById(prefix + '-lat');
            var lngEl = document.getElementById(prefix + '-lng');
            if (latEl) latEl.value = lat;
            if (lngEl) lngEl.value = lng;
        });

        statusEl.textContent = 'Lokasi terdeteksi.';
        buttons.forEach(function(btn) {
            btn.disabled = false;
        });
    }, function() {
        statusEl.textContent =
            'Gagal mendeteksi lokasi. Izinkan akses lokasi di browser lalu muat ulang halaman.';
    }, {
        enableHighAccuracy: true,
        timeout: 10000
    });
})();
</script>
@endsection
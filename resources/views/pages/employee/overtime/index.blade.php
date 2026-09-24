@extends('layouts.employee')

@section('title', 'Lembur')

@section('breadcrumb')
<a href="{{ route('company.member.dashboard') }}">Dashboard</a>
<span class="current">Lembur</span>
@endsection

@section('content')
<div class="page-title">Lembur</div>
<div class="page-sub">Riwayat pengajuan lembur</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:14px">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger" style="margin-bottom:14px">{{ session('error') }}</div>
@endif

<a href="{{ route('company.member.overtimes.create') }}" class="btn" style="width:100%;text-align:center;margin-bottom:16px">
    + Ajukan Lembur Baru
</a>

<form method="GET" class="card" style="padding:14px;margin-bottom:16px">
    <select name="status" class="form-control" onchange="this.form.submit()">
        <option value="">Semua Status</option>
        @foreach(['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'canceled' =>
        'Dibatalkan'] as $val => $label)
        <option value="{{ $val }}" {{ ($filters['status'] ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
</form>

<div class="card">
    <div class="card-body" style="padding:0">
        @forelse($overtimes as $o)
        <a href="{{ route('company.member.overtimes.show', $o->id) }}" class="menu-row">
            <span>
                {{ \Carbon\Carbon::parse($o->date)->isoFormat('D MMM Y') }}
                <br>
                <small style="font-weight:400;color:var(--c-muted)">
                    {{ $o->start_time }} - {{ $o->end_time }} &middot; {{ $o->minutes }} menit
                </small>
            </span>
            @if($o->status === 'approved') <span class="badge badge-success">Disetujui</span>
            @elseif($o->status === 'rejected') <span class="badge badge-danger">Ditolak</span>
            @elseif($o->status === 'canceled') <span class="badge badge-gray">Dibatalkan</span>
            @else <span class="badge badge-warning">Menunggu</span>
            @endif
        </a>
        @empty
        <div class="menu-row menu-row-disabled">Belum ada pengajuan lembur.</div>
        @endforelse
    </div>
</div>
<div style="margin-top:12px">{{ $overtimes->links() }}</div>

<style>
.form-control {
    width: 100%;
    border: 1px solid var(--c-border, #e3e8f0);
    border-radius: 10px;
    padding: 9px 12px;
    font-size: 14px;
    font-family: inherit
}

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
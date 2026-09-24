@extends('layouts.employee')

@section('title', 'Catatan')

@section('breadcrumb')
<a href="{{ route('company.member.dashboard') }}">Dashboard</a>
<span class="current">Catatan</span>
@endsection

@section('content')
<div class="page-title">Catatan dari HR</div>
<div class="page-sub">{{ $summary->total_unread }} catatan belum dibaca</div>

<div class="card">
    <div class="card-body">
        <div class="metrics">
            <div class="metric">
                <div class="metric-label">Total</div>
                <div class="metric-val">{{ $summary->total_notes }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Peringatan</div>
                <div class="metric-val warning">{{ $summary->total_warning }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Pujian</div>
                <div class="metric-val">{{ $summary->total_praise }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Performa</div>
                <div class="metric-val info">{{ $summary->total_performance }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Belum Dibaca</div>
                <div class="metric-val warning">{{ $summary->total_unread }}</div>
            </div>
        </div>
    </div>
</div>

<form method="GET" class="card" style="margin-top:16px;padding:14px;display:flex;gap:8px;flex-wrap:wrap">
    <select name="type" class="form-control" style="flex:1;min-width:140px" onchange="this.form.submit()">
        <option value="">Semua Tipe</option>
        @foreach(['warning' => 'Peringatan', 'praise' => 'Pujian', 'performance' => 'Performa', 'absence' => 'Absensi',
        'general' => 'Umum'] as $val => $label)
        <option value="{{ $val }}" {{ ($filters['type'] ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <select name="is_read" class="form-control" style="flex:1;min-width:140px" onchange="this.form.submit()">
        <option value="">Semua Status</option>
        <option value="0" {{ ($filters['is_read'] ?? '') === '0' ? 'selected' : '' }}>Belum Dibaca</option>
        <option value="1" {{ ($filters['is_read'] ?? '') === '1' ? 'selected' : '' }}>Sudah Dibaca</option>
    </select>
</form>

<div class="card" style="margin-top:16px">
    <div class="card-body" style="padding:0">
        @forelse($notes as $n)
        <a href="{{ route('company.member.notes.show', $n->id) }}" class="menu-row">
            <span>
                {{ $n->title }}
                <br>
                <small style="font-weight:400;color:var(--c-muted)">
                    {{ $n->creator->name ?? 'HR' }} &middot; {{ $n->created_at->isoFormat('D MMM Y') }}
                </small>
            </span>
            <span style="display:flex;align-items:center;gap:6px">
                @if(!$n->is_read)<span class="badge badge-warning">Baru</span>@endif
                @if($n->type === 'warning') <span class="badge badge-danger">Peringatan</span>
                @elseif($n->type === 'praise') <span class="badge badge-success">Pujian</span>
                @elseif($n->type === 'performance') <span class="badge badge-info">Performa</span>
                @else <span class="badge badge-gray">{{ ucfirst($n->type) }}</span>
                @endif
            </span>
        </a>
        @empty
        <div class="menu-row menu-row-disabled">Belum ada catatan.</div>
        @endforelse
    </div>
</div>
<div style="margin-top:12px">{{ $notes->links() }}</div>

<style>
.form-control {
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
</style>
@endsection
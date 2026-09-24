@extends('layouts.employee')

@section('title', 'Hari Libur')

@section('breadcrumb')
<a href="{{ route('company.member.dashboard') }}">Dashboard</a>
<span class="current">Hari Libur</span>
@endsection

@section('content')
<div class="page-title">Hari Libur Perusahaan</div>
<div class="page-sub">Jadwal cuti bersama & hari libur nasional</div>

<form method="GET" class="card" style="padding:14px;margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap">
    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama libur..." class="form-control"
        style="flex:1;min-width:150px">
    <select name="type" class="form-control" style="flex:1;min-width:130px">
        <option value="">Semua Tipe</option>
        <option value="national" {{ ($filters['type'] ?? '') === 'national' ? 'selected' : '' }}>Nasional</option>
        <option value="company" {{ ($filters['type'] ?? '') === 'company' ? 'selected' : '' }}>Perusahaan</option>
    </select>
    <button type="submit" class="btn">Cari</button>
</form>

<div class="card">
    <div class="card-body" style="padding:0">
        @forelse($holidays as $h)
        <a href="{{ route('company.member.holidays.show', $h['id']) }}" class="menu-row">
            <span>
                {{ $h['name'] }}
                <br>
                <small style="font-weight:400;color:var(--c-muted)">
                    {{ \Carbon\Carbon::parse($h['start_date'])->isoFormat('D MMM') }}
                    @if($h['start_date'] !== $h['end_date'])
                    &ndash; {{ \Carbon\Carbon::parse($h['end_date'])->isoFormat('D MMM Y') }}
                    @else
                    {{ \Carbon\Carbon::parse($h['start_date'])->isoFormat('Y') }}
                    @endif
                    &middot; {{ $h['total_days'] }} hari
                </small>
            </span>
            @if($h['is_active_today'])
            <span class="badge badge-success">Hari Ini</span>
            @else
            <span class="badge badge-gray">{{ ucfirst($h['type']) }}</span>
            @endif
        </a>
        @empty
        <div class="menu-row menu-row-disabled">Belum ada data hari libur.</div>
        @endforelse
    </div>
</div>
<div style="margin-top:12px">{{ $holidays->links() }}</div>

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
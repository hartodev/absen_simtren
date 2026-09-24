@extends('layouts.employee')

@section('title', 'Leaderboard Performa')

@section('breadcrumb')
<a href="{{ route('company.member.performance-scores.index') }}">Performa</a>
<span class="current">Leaderboard</span>
@endsection

@section('content')
<div class="page-title">Leaderboard</div>
<div class="page-sub">{{ \Carbon\Carbon::create($year, $month, 1)->isoFormat('MMMM Y') }}</div>

<form method="GET" class="card" style="padding:14px;margin-bottom:16px;display:flex;gap:10px">
    <select name="month" class="form-control">
        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']
        as $i => $label)
        <option value="{{ $i + 1 }}" {{ $month == $i + 1 ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <input type="number" name="year" value="{{ $year }}" class="form-control" style="max-width:100px">
    <button type="submit" class="btn">Lihat</button>
</form>

@if($myScore)
<div class="card" style="margin-bottom:16px">
    <div class="card-header"><span class="card-title">Posisi Kamu</span></div>
    <div class="card-body">
        <div class="metrics">
            <div class="metric">
                <div class="metric-label">Ranking</div>
                <div class="metric-val">#{{ $myRank }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Skor</div>
                <div class="metric-val">{{ $myScore->final_score }}</div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="card">
    <div class="card-body" style="padding:0">
        @forelse($leaderboard as $item)
        <div class="menu-row" style="{{ $item->is_me ? 'background:#eef4ff' : '' }}">
            <span>#{{ $item->rank }} &middot; {{ $item->user->name ?? '—' }} {{ $item->is_me ? '(Kamu)' : '' }}</span>
            <span class="badge badge-info">{{ $item->final_score }}</span>
        </div>
        @empty
        <div class="menu-row menu-row-disabled">Belum ada data leaderboard untuk periode ini.</div>
        @endforelse
    </div>
</div>

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
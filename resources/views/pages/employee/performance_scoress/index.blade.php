@extends('layouts.employee')

@section('title', 'Performa')

@section('breadcrumb')
<a href="{{ route('company.member.dashboard') }}">Dashboard</a>
<span class="current">Performa</span>
@endsection

@section('content')
<div class="page-title">Performa Saya</div>
<div class="page-sub">Riwayat skor performa bulanan</div>

<div class="card" style="margin-bottom:16px">
    <div class="card-header">
        <span class="card-title">Papan Peringkat</span>
    </div>
    <div class="card-body">
        <a href="{{ route('company.member.performance-scores.leaderboard') }}" class="btn btn-outline" style="width:100%;text-align:center">
            Lihat Leaderboard Bulan Ini &rarr;
        </a>
    </div>
</div>

<form method="GET" class="card" style="padding:14px;margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap">
    <select name="month" class="form-control" style="flex:1;min-width:120px" onchange="this.form.submit()">
        <option value="">Semua Bulan</option>
        @foreach(range(1, 12) as $m)
        <option value="{{ $m }}" {{ ($filters['month'] ?? '') == $m ? 'selected' : '' }}>
            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
        </option>
        @endforeach
    </select>
    <select name="year" class="form-control" style="flex:1;min-width:120px" onchange="this.form.submit()">
        <option value="">Semua Tahun</option>
        @foreach(range(now()->year, now()->year - 3) as $y)
        <option value="{{ $y }}" {{ ($filters['year'] ?? '') == $y ? 'selected' : '' }}>{{ $y }}</option>
        @endforeach
    </select>
</form>

<div class="card">
    <div class="card-body" style="padding:0">
        @forelse($scores as $score)
        <a href="{{ route('company.member.performance-scores.show', $score->id) }}" class="menu-row">
            <span>{{ \Carbon\Carbon::create($score->year, $score->month, 1)->translatedFormat('MMMM Y') }}</span>
            <span class="badge badge-info">{{ $score->final_score }}</span>
        </a>
        @empty
        <div class="menu-row menu-row-disabled">
            <span>Belum ada data performa.</span>
        </div>
        @endforelse
    </div>
</div>

<div style="margin-top:16px">
    {{ $scores->links() }}
</div>
@endsection

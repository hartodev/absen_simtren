@extends('layouts.employee')

@section('title', 'Laporan Bulanan')

@section('breadcrumb')
<a href="{{ route('company.member.dashboard') }}">Dashboard</a>
<span class="current">Laporan Bulanan</span>
@endsection

@section('content')
<div class="page-title">Laporan Bulanan</div>
<div class="page-sub">Rekap target, pencapaian, kendala & solusi tiap bulan</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:14px">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger" style="margin-bottom:14px">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-header"><span class="card-title">Ringkasan</span></div>
    <div class="card-body">
        <div class="metrics">
            <div class="metric">
                <div class="metric-label">Total</div>
                <div class="metric-val">{{ $summary['total'] }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Approved</div>
                <div class="metric-val">{{ $summary['approved'] }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Submitted</div>
                <div class="metric-val info">{{ $summary['submitted'] }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Draft</div>
                <div class="metric-val">{{ $summary['draft'] }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Rejected</div>
                <div class="metric-val warning">{{ $summary['rejected'] }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Skor Rata-rata</div>
                <div class="metric-val">{{ $summary['avg_score'] }}</div>
            </div>
        </div>

        <a href="{{ route('company.member.monthly-reports.create') }}" class="btn"
            style="width:100%;margin-top:18px;text-align:center">
            + Buat Laporan Baru
        </a>
    </div>
</div>

<div class="card" style="margin-top:16px">
    <div class="card-header"><span class="card-title">Daftar Laporan</span></div>
    <div class="card-body" style="padding:0">
        @forelse($reports as $r)
        <a href="{{ route('company.member.monthly-reports.show', $r->id) }}" class="menu-row">
            <span>{{ \Carbon\Carbon::create($r->year, $r->month, 1)->isoFormat('MMMM Y') }}</span>
            @if($r->status === 'approved') <span class="badge badge-success">Approved</span>
            @elseif($r->status === 'submitted') <span class="badge badge-info">Submitted</span>
            @elseif($r->status === 'rejected') <span class="badge badge-danger">Rejected</span>
            @else <span class="badge badge-gray">Draft</span>
            @endif
        </a>
        @empty
        <div class="menu-row menu-row-disabled">Belum ada laporan bulanan.</div>
        @endforelse
    </div>
</div>
<div style="margin-top:12px">{{ $reports->links() }}</div>

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
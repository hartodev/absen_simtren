@extends('layouts.employee')

@section('title', 'Slip Gaji')

@section('breadcrumb')
<a href="{{ route('company.member.dashboard') }}">Dashboard</a>
<span class="current">Slip Gaji</span>
@endsection

@section('content')
<div class="page-title">Slip Gaji</div>
<div class="page-sub">Riwayat penggajian</div>

<form method="GET" class="card" style="padding:14px;margin-bottom:16px">
    <select name="status" class="form-control" onchange="this.form.submit()">
        <option value="">Semua Status</option>
        @foreach(['draft' => 'Draft', 'approved' => 'Disetujui', 'paid' => 'Sudah Dibayar'] as $val => $label)
        <option value="{{ $val }}" {{ ($filters['status'] ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
</form>

<div class="card">
    <div class="card-body" style="padding:0">
        @forelse($payrolls as $p)
        <a href="{{ route('company.member.payrolls.show', $p->id) }}" class="menu-row">
            <span>
                {{ \Carbon\Carbon::parse($p->period_start)->isoFormat('D MMM') }}
                &ndash;
                {{ \Carbon\Carbon::parse($p->period_end)->isoFormat('D MMM Y') }}
                <br>
                <small style="font-weight:400;color:var(--c-muted)">
                    Rp {{ number_format($p->net_salary ?? 0, 0, ',', '.') }}
                </small>
            </span>
            @if($p->status === 'paid') <span class="badge badge-success">Sudah Dibayar</span>
            @elseif($p->status === 'approved') <span class="badge badge-info">Disetujui</span>
            @else <span class="badge badge-gray">Draft</span>
            @endif
        </a>
        @empty
        <div class="menu-row menu-row-disabled">Belum ada slip gaji.</div>
        @endforelse
    </div>
</div>
<div style="margin-top:12px">{{ $payrolls->links() }}</div>

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
</style>
@endsection
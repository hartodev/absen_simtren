@extends('layouts.employee')

@section('title', 'Izin')

@section('breadcrumb')
<a href="{{ route('company.member.dashboard') }}">Dashboard</a>
<span class="current">Izin</span>
@endsection

@section('content')
<div class="page-title">Izin</div>
<div class="page-sub">Riwayat pengajuan izin</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:14px">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger" style="margin-bottom:14px">{{ session('error') }}</div>
@endif

<a href="{{ route('company.member.permissions.create') }}" class="btn"
    style="width:100%;text-align:center;margin-bottom:16px">
    + Ajukan Izin Baru
</a>

<div class="card">
    <div class="card-body" style="padding:0">
        @forelse($permissions as $p)
        <a href="{{ route('company.member.permissions.show', $p->id) }}" class="menu-row">
            <span>
                {{ \Carbon\Carbon::parse($p->date_permission)->isoFormat('D MMM Y') }}
                <br>
                <small
                    style="font-weight:400;color:var(--c-muted)">{{ \Illuminate\Support\Str::limit($p->reason, 40) }}</small>
            </span>
            @if(is_null($p->is_approved)) <span class="badge badge-warning">Menunggu</span>
            @elseif($p->is_approved) <span class="badge badge-success">Disetujui</span>
            @else <span class="badge badge-danger">Ditolak</span>
            @endif
        </a>
        @empty
        <div class="menu-row menu-row-disabled">Belum ada pengajuan izin.</div>
        @endforelse
    </div>
</div>
<div style="margin-top:12px">{{ $permissions->links() }}</div>

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
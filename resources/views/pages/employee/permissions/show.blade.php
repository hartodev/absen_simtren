@extends('layouts.employee')

@section('title', 'Detail Izin')

@section('breadcrumb')
<a href="{{ route('company.member.permissions.index') }}">Izin</a>
<span class="current">Detail</span>
@endsection

@section('content')
<div class="page-title">Detail Izin</div>
<div class="page-sub">{{ \Carbon\Carbon::parse($permission->date_permission)->isoFormat('dddd, D MMMM Y') }}</div>

@if(session('error'))
<div class="alert alert-danger" style="margin-bottom:14px">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <span class="card-title">Status</span>
        @if(is_null($permission->is_approved)) <span class="badge badge-warning">Menunggu Persetujuan</span>
        @elseif($permission->is_approved) <span class="badge badge-success">Disetujui</span>
        @else <span class="badge badge-danger">Ditolak</span>
        @endif
    </div>
    <div class="card-body">
        <p style="font-size:13px;color:var(--c-muted)"><strong>Alasan:</strong></p>
        <p style="font-size:14px;margin-top:4px">{{ $permission->reason }}</p>

        @if($permission->image)
        <div style="margin-top:16px">
            <img src="{{ asset($permission->image) }}" alt="Lampiran" style="max-width:100%;border-radius:10px">
        </div>
        @endif

        <div style="display:flex;gap:10px;margin-top:20px;flex-wrap:wrap">
            <a href="{{ route('company.member.permissions.index') }}" class="btn btn-outline">&larr; Kembali</a>

            @if($permission->is_approved !== true)
            <form method="POST" action="{{ route('company.member.permissions.cancel', $permission->id) }}"
                onsubmit="return confirm('Batalkan pengajuan izin ini?')">
                @csrf
                <button type="submit" class="btn btn-outline"
                    style="color:#b34040;border-color:#f0c4c4">Batalkan</button>
            </form>
            @endif
        </div>
    </div>
</div>

<style>
.alert {
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600
}

.alert-danger {
    background: #fff1f1;
    color: #b34040
}
</style>
@endsection
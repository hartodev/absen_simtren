@extends('layouts.employee')

@section('title', 'Detail Cuti')

@section('breadcrumb')
<a href="{{ route('company.member.leaves.index') }}">Cuti</a>
<span class="current">Detail</span>
@endsection

@section('content')
<div class="page-title">Detail Cuti</div>
<div class="page-sub">
    {{ \Carbon\Carbon::parse($leave->start_date)->isoFormat('D MMMM') }}
    &ndash;
    {{ \Carbon\Carbon::parse($leave->end_date)->isoFormat('D MMMM Y') }}
</div>

@if(session('error'))
<div class="alert alert-danger" style="margin-bottom:14px">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <span class="card-title">Status</span>
        @if($leave->status === 'approved') <span class="badge badge-success">Disetujui</span>
        @elseif($leave->status === 'rejected') <span class="badge badge-danger">Ditolak</span>
        @elseif($leave->status === 'canceled') <span class="badge badge-gray">Dibatalkan</span>
        @else <span class="badge badge-warning">Menunggu</span>
        @endif
    </div>
    <div class="card-body">
        <div class="metrics">
            <div class="metric">
                <div class="metric-label">Jenis</div>
                <div class="metric-val">{{ ucfirst($leave->type) }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Durasi</div>
                <div class="metric-val">
                    {{ \Carbon\Carbon::parse($leave->start_date)->diffInDays(\Carbon\Carbon::parse($leave->end_date)) + 1 }}
                    hari
                </div>
            </div>
        </div>

        @if($leave->reason)
        <p style="font-size:13px;color:var(--c-muted);margin-top:16px"><strong>Alasan:</strong></p>
        <p style="font-size:14px;margin-top:4px">{{ $leave->reason }}</p>
        @endif

        @if($leave->status === 'approved' || $leave->status === 'rejected')
        <p style="font-size:13px;color:var(--c-muted);margin-top:16px">
            {{ $leave->status === 'approved' ? 'Disetujui' : 'Ditolak' }} oleh: {{ $leave->approver->name ?? '—' }}
        </p>
        @endif

        <div style="display:flex;gap:10px;margin-top:20px;flex-wrap:wrap">
            <a href="{{ route('company.member.leaves.index') }}" class="btn btn-outline">&larr; Kembali</a>

            @if($leave->status === 'pending')
            <form method="POST" action="{{ route('company.member.leaves.cancel', $leave->id) }}"
                onsubmit="return confirm('Batalkan pengajuan cuti ini?')">
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
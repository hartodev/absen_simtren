@extends('layouts.employee')

@section('title', 'Detail Lembur')

@section('breadcrumb')
<a href="{{ route('company.member.overtimes.index') }}">Lembur</a>
<span class="current">Detail</span>
@endsection

@section('content')
<div class="page-title">Detail Lembur</div>
<div class="page-sub">{{ \Carbon\Carbon::parse($overtime->date)->isoFormat('dddd, D MMMM Y') }}</div>

@if(session('error'))
<div class="alert alert-danger" style="margin-bottom:14px">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <span class="card-title">Status</span>
        @if($overtime->status === 'approved') <span class="badge badge-success">Disetujui</span>
        @elseif($overtime->status === 'rejected') <span class="badge badge-danger">Ditolak</span>
        @elseif($overtime->status === 'canceled') <span class="badge badge-gray">Dibatalkan</span>
        @else <span class="badge badge-warning">Menunggu</span>
        @endif
    </div>
    <div class="card-body">
        <div class="metrics">
            <div class="metric">
                <div class="metric-label">Jam Mulai</div>
                <div class="metric-val">{{ $overtime->start_time }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Jam Selesai</div>
                <div class="metric-val">{{ $overtime->end_time }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Durasi</div>
                <div class="metric-val">{{ $overtime->minutes }} menit</div>
            </div>
        </div>

        @if($overtime->reason)
        <p style="font-size:13px;color:var(--c-muted);margin-top:16px"><strong>Alasan:</strong></p>
        <p style="font-size:14px;margin-top:4px">{{ $overtime->reason }}</p>
        @endif

        @if($overtime->attendance)
        <p style="font-size:13px;color:var(--c-muted);margin-top:16px">
            Dikaitkan dengan absensi tanggal
            {{ \Carbon\Carbon::parse($overtime->attendance->date)->isoFormat('D MMM Y') }}
        </p>
        @endif

        @if($overtime->evidence_image)
        <div style="margin-top:16px">
            <img src="{{ asset($overtime->evidence_image) }}" alt="Bukti" style="max-width:100%;border-radius:10px">
        </div>
        @endif

        @if(in_array($overtime->status, ['approved', 'rejected']))
        <p style="font-size:13px;color:var(--c-muted);margin-top:16px">
            {{ $overtime->status === 'approved' ? 'Disetujui' : 'Ditolak' }} oleh:
            {{ $overtime->approver->name ?? '—' }}
        </p>
        @endif

        <div style="display:flex;gap:10px;margin-top:20px;flex-wrap:wrap">
            <a href="{{ route('company.member.overtimes.index') }}" class="btn btn-outline">&larr; Kembali</a>

            @if($overtime->status === 'pending')
            <form method="POST" action="{{ route('company.member.overtimes.cancel', $overtime->id) }}"
                onsubmit="return confirm('Batalkan pengajuan lembur ini?')">
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
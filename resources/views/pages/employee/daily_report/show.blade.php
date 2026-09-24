@extends('layouts.employee')

@section('title', 'Detail Laporan Harian')

@section('breadcrumb')
<a href="{{ route('company.member.daily-reports.index') }}">Laporan Harian</a>
<span class="current">Detail</span>
@endsection

@section('content')
<div class="page-title">Detail Laporan Harian</div>
<div class="page-sub">{{ \Carbon\Carbon::parse($report->date)->isoFormat('dddd, D MMMM Y') }}</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Ringkasan</span>
        @if(!$report->achievement) <span class="badge badge-gray">Belum Diisi Sore</span>
        @elseif($report->is_achieved) <span class="badge badge-success">Tercapai</span>
        @else <span class="badge badge-danger">Tidak Tercapai</span>
        @endif
    </div>
    <div class="card-body">
        <p style="font-size:13px;color:var(--c-muted)"><strong>Target:</strong></p>
        <p style="font-size:14px;margin-top:4px">{{ $report->target }}</p>

        @if($report->achievement)
        <p style="font-size:13px;color:var(--c-muted);margin-top:16px"><strong>Pencapaian:</strong></p>
        <p style="font-size:14px;margin-top:4px">{{ $report->achievement }}</p>
        @endif

        @if(!$report->is_achieved && $report->reason_not_achieved)
        <p style="font-size:13px;color:var(--c-muted);margin-top:16px"><strong>Alasan Tidak Tercapai:</strong></p>
        <p style="font-size:14px;margin-top:4px">{{ $report->reason_not_achieved }}</p>
        @endif

        @if($report->attachment)
        <div style="margin-top:16px">
            <img src="{{ asset($report->attachment) }}" alt="Lampiran" style="max-width:100%;border-radius:10px">
        </div>
        @endif

        <a href="{{ route('company.member.daily-reports.index') }}" class="btn btn-outline" style="margin-top:20px">
            &larr; Kembali
        </a>
    </div>
</div>
@endsection
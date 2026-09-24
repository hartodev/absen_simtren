@extends('layouts.employee')

@section('title', 'Detail Laporan Bulanan')

@section('breadcrumb')
<a href="{{ route('company.member.monthly-reports.index') }}">Laporan Bulanan</a>
<span class="current">Detail</span>
@endsection

@section('content')
<div class="page-title">{{ \Carbon\Carbon::create($report->year, $report->month, 1)->isoFormat('MMMM Y') }}</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:14px">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger" style="margin-bottom:14px">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <span class="card-title">Ringkasan</span>
        @if($report->status === 'approved') <span class="badge badge-success">Approved</span>
        @elseif($report->status === 'submitted') <span class="badge badge-info">Submitted</span>
        @elseif($report->status === 'rejected') <span class="badge badge-danger">Rejected</span>
        @else <span class="badge badge-gray">Draft</span>
        @endif
    </div>
    <div class="card-body">
        <p style="font-size:13px;color:var(--c-muted)"><strong>Target:</strong></p>
        <p style="font-size:14px;margin-top:4px">{{ $report->target }}</p>

        <p style="font-size:13px;color:var(--c-muted);margin-top:14px"><strong>Pencapaian:</strong></p>
        <p style="font-size:14px;margin-top:4px">{{ $report->achievement }}</p>

        <p style="font-size:13px;color:var(--c-muted);margin-top:14px"><strong>Kendala:</strong></p>
        <p style="font-size:14px;margin-top:4px">{{ $report->problem }}</p>

        <p style="font-size:13px;color:var(--c-muted);margin-top:14px"><strong>Solusi:</strong></p>
        <p style="font-size:14px;margin-top:4px">{{ $report->solution }}</p>

        @if($report->attachment)
        <div style="margin-top:16px">
            <img src="{{ asset($report->attachment) }}" alt="Lampiran" style="max-width:100%;border-radius:10px">
        </div>
        @endif

        @if($report->status === 'approved')
        <div class="metrics" style="margin-top:16px">
            <div class="metric">
                <div class="metric-label">Skor</div>
                <div class="metric-val">{{ $report->score }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Disetujui Oleh</div>
                <div class="metric-val">{{ $report->approver->name ?? '—' }}</div>
            </div>
        </div>
        @endif

        <div style="display:flex;gap:10px;margin-top:20px;flex-wrap:wrap">
            @if(in_array($report->status, ['draft', 'rejected']))
            <a href="{{ route('company.member.monthly-reports.edit', $report->id) }}" class="btn btn-outline">Edit</a>
            @endif

            @if($report->status === 'draft')
            <form method="POST" action="{{ route('company.member.monthly-reports.submit', $report->id) }}">
                @csrf
                <button type="submit" class="btn">Submit ke HR</button>
            </form>
            <form method="POST" action="{{ route('company.member.monthly-reports.destroy', $report->id) }}"
                onsubmit="return confirm('Hapus laporan draft ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline" style="color:#b34040;border-color:#f0c4c4">Hapus</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
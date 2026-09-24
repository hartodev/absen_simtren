@extends('layouts.employee')

@section('title', 'Detail Hari Libur')

@section('breadcrumb')
<a href="{{ route('company.member.holidays.index') }}">Hari Libur</a>
<span class="current">Detail</span>
@endsection

@section('content')
<div class="page-title">{{ $holiday['name'] }}</div>
<div class="page-sub">
    {{ \Carbon\Carbon::parse($holiday['start_date'])->isoFormat('D MMMM Y') }}
    @if($holiday['start_date'] !== $holiday['end_date'])
    &ndash; {{ \Carbon\Carbon::parse($holiday['end_date'])->isoFormat('D MMMM Y') }}
    @endif
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Detail</span>
        @if($holiday['is_active_today']) <span class="badge badge-success">Berlangsung Hari Ini</span>
        @else <span class="badge badge-gray">{{ ucfirst($holiday['type']) }}</span>
        @endif
    </div>
    <div class="card-body">
        <div class="metrics">
            <div class="metric">
                <div class="metric-label">Total Hari</div>
                <div class="metric-val">{{ $holiday['total_days'] }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Tipe</div>
                <div class="metric-val">{{ ucfirst($holiday['type']) }}</div>
            </div>
        </div>

        @if($holiday['note'])
        <p style="font-size:13px;color:var(--c-muted);margin-top:16px"><strong>Catatan:</strong></p>
        <p style="font-size:14px;margin-top:4px">{{ $holiday['note'] }}</p>
        @endif

        <a href="{{ route('company.member.holidays.index') }}" class="btn btn-outline" style="margin-top:20px">
            &larr; Kembali
        </a>
    </div>
</div>
@endsection
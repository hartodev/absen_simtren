@extends('layouts.employee')

@section('title', 'Detail Slip Gaji')

@section('breadcrumb')
<a href="{{ route('company.member.payrolls.index') }}">Slip Gaji</a>
<span class="current">Detail</span>
@endsection

@section('content')
<div class="page-title">Slip Gaji</div>
<div class="page-sub">
    {{ \Carbon\Carbon::parse($payroll->period_start)->isoFormat('D MMMM') }}
    &ndash;
    {{ \Carbon\Carbon::parse($payroll->period_end)->isoFormat('D MMMM Y') }}
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Rincian</span>
        @if($payroll->status === 'paid') <span class="badge badge-success">Sudah Dibayar</span>
        @elseif($payroll->status === 'approved') <span class="badge badge-info">Disetujui</span>
        @else <span class="badge badge-gray">Draft</span>
        @endif
    </div>
    <div class="card-body">
        <div class="metrics">
            <div class="metric">
                <div class="metric-label">Gaji Pokok</div>
                <div class="metric-val">Rp {{ number_format($payroll->basic_salary ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Tunjangan</div>
                <div class="metric-val">Rp {{ number_format($payroll->allowance ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Lembur</div>
                <div class="metric-val">Rp {{ number_format($payroll->overtime_pay ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Potongan</div>
                <div class="metric-val warning">Rp {{ number_format($payroll->deduction ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>

        <div
            style="margin-top:18px;padding-top:14px;border-top:1px solid var(--c-border,#eee);display:flex;justify-content:space-between;align-items:center">
            <span style="font-size:14px;font-weight:600">Total Diterima</span>
            <span style="font-size:20px;font-weight:700">Rp
                {{ number_format($payroll->net_salary ?? 0, 0, ',', '.') }}</span>
        </div>

        @if($payroll->status === 'paid' && $payroll->paid_at)
        <p style="font-size:12px;color:var(--c-muted);margin-top:10px">
            Dibayar pada {{ \Carbon\Carbon::parse($payroll->paid_at)->isoFormat('D MMMM Y') }}
        </p>
        @endif

        <a href="{{ route('company.member.payrolls.index') }}" class="btn btn-outline" style="margin-top:20px">
            &larr; Kembali
        </a>
    </div>
</div>
@endsection
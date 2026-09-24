@extends('layouts.employee')

@section('title', 'Edit Laporan Bulanan')

@section('breadcrumb')
<a href="{{ route('company.member.monthly-reports.index') }}">Laporan Bulanan</a>
<span class="current">Edit</span>
@endsection

@section('content')
<div class="page-title">Edit Laporan —
    {{ \Carbon\Carbon::create($report->year, $report->month, 1)->isoFormat('MMMM Y') }}</div>

@if($errors->any())
<div class="alert alert-danger" style="margin-bottom:14px">{{ $errors->first() }}</div>
@endif

@if($report->status === 'rejected')
<div class="alert alert-danger" style="margin-bottom:14px">
    Laporan ini ditolak HR. Menyimpan perubahan akan mengembalikan status ke Draft.
</div>
@endif

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('company.member.monthly-reports.update', $report->id) }}"
            enctype="multipart/form-data">
            @csrf

            <label class="form-label">Target Bulan Ini</label>
            <textarea name="target" rows="3" class="form-control"
                required>{{ old('target', $report->target) }}</textarea>

            <label class="form-label" style="margin-top:12px">Pencapaian</label>
            <textarea name="achievement" rows="3" class="form-control"
                required>{{ old('achievement', $report->achievement) }}</textarea>

            <label class="form-label" style="margin-top:12px">Kendala</label>
            <textarea name="problem" rows="3" class="form-control"
                required>{{ old('problem', $report->problem) }}</textarea>

            <label class="form-label" style="margin-top:12px">Solusi</label>
            <textarea name="solution" rows="3" class="form-control"
                required>{{ old('solution', $report->solution) }}</textarea>

            <label class="form-label" style="margin-top:12px">Ganti Lampiran (opsional)</label>
            <input type="file" name="attachment" accept="image/*,.pdf" class="form-control">

            <button type="submit" class="btn" style="width:100%;margin-top:16px">Simpan Perubahan</button>
        </form>
    </div>
</div>

<style>
.form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 5px;
    color: var(--c-text, #1a1a1a)
}

.form-control {
    width: 100%;
    border: 1px solid var(--c-border, #e3e8f0);
    border-radius: 10px;
    padding: 9px 12px;
    font-size: 14px;
    font-family: inherit
}

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
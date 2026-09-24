@extends('layouts.employee')

@section('title', 'Buat Laporan Bulanan')

@section('breadcrumb')
<a href="{{ route('company.member.monthly-reports.index') }}">Laporan Bulanan</a>
<span class="current">Buat Baru</span>
@endsection

@section('content')
<div class="page-title">Buat Laporan Bulanan</div>
<div class="page-sub">Laporan akan tersimpan sebagai draft — kamu bisa edit sebelum submit ke HR</div>

@if($errors->any())
<div class="alert alert-danger" style="margin-bottom:14px">{{ $errors->first() }}</div>
@endif

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('company.member.monthly-reports.store') }}" enctype="multipart/form-data">
            @csrf

            <div style="display:flex;gap:10px">
                <div style="flex:1">
                    <label class="form-label">Bulan</label>
                    <select name="month" class="form-control" required>
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']
                        as $i => $label)
                        <option value="{{ $i + 1 }}" {{ old('month') == $i + 1 ? 'selected' : '' }}>{{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div style="flex:1">
                    <label class="form-label">Tahun</label>
                    <input type="number" name="year" class="form-control" value="{{ old('year', now()->year) }}"
                        required>
                </div>
            </div>

            <label class="form-label" style="margin-top:12px">Target Bulan Ini</label>
            <textarea name="target" rows="3" class="form-control" required>{{ old('target') }}</textarea>

            <label class="form-label" style="margin-top:12px">Pencapaian</label>
            <textarea name="achievement" rows="3" class="form-control" required>{{ old('achievement') }}</textarea>

            <label class="form-label" style="margin-top:12px">Kendala</label>
            <textarea name="problem" rows="3" class="form-control" required>{{ old('problem') }}</textarea>

            <label class="form-label" style="margin-top:12px">Solusi</label>
            <textarea name="solution" rows="3" class="form-control" required>{{ old('solution') }}</textarea>

            <label class="form-label" style="margin-top:12px">Lampiran (opsional)</label>
            <input type="file" name="attachment" accept="image/*,.pdf" class="form-control">

            <button type="submit" class="btn" style="width:100%;margin-top:16px">Simpan sebagai Draft</button>
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
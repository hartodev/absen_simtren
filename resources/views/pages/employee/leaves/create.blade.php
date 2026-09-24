@extends('layouts.employee')

@section('title', 'Ajukan Cuti')

@section('breadcrumb')
<a href="{{ route('company.member.leaves.index') }}">Cuti</a>
<span class="current">Ajukan Baru</span>
@endsection

@section('content')
<div class="page-title">Ajukan Cuti</div>

@if($errors->any())
<div class="alert alert-danger" style="margin-bottom:14px">{{ $errors->first() }}</div>
@endif

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('company.member.leaves.store') }}">
            @csrf

            <div style="display:flex;gap:10px">
                <div style="flex:1">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                </div>
                <div style="flex:1">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
                </div>
            </div>

            <label class="form-label" style="margin-top:12px">Jenis Cuti</label>
            <select name="type" class="form-control" required>
                <option value="annual" {{ old('type') === 'annual' ? 'selected' : '' }}>Tahunan</option>
                <option value="sick" {{ old('type') === 'sick' ? 'selected' : '' }}>Sakit</option>
                <option value="maternity" {{ old('type') === 'maternity' ? 'selected' : '' }}>Melahirkan</option>
                <option value="important" {{ old('type') === 'important' ? 'selected' : '' }}>Kepentingan Penting
                </option>
                <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Lainnya</option>
            </select>

            <label class="form-label" style="margin-top:12px">Alasan (opsional)</label>
            <textarea name="reason" rows="3" class="form-control">{{ old('reason') }}</textarea>

            <button type="submit" class="btn" style="width:100%;margin-top:16px">Kirim Pengajuan</button>
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
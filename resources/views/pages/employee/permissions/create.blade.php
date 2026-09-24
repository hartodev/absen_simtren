@extends('layouts.employee')

@section('title', 'Ajukan Izin')

@section('breadcrumb')
<a href="{{ route('company.member.permissions.index') }}">Izin</a>
<span class="current">Ajukan Baru</span>
@endsection

@section('content')
<div class="page-title">Ajukan Izin</div>

@if($errors->any())
<div class="alert alert-danger" style="margin-bottom:14px">{{ $errors->first() }}</div>
@endif

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('company.member.permissions.store') }}" enctype="multipart/form-data">
            @csrf

            <label class="form-label">Tanggal Izin</label>
            <input type="date" name="date_permission" class="form-control" value="{{ old('date_permission') }}"
                required>

            <label class="form-label" style="margin-top:12px">Alasan</label>
            <textarea name="reason" rows="4" class="form-control" maxlength="500"
                required>{{ old('reason') }}</textarea>

            <label class="form-label" style="margin-top:12px">Lampiran Bukti (opsional)</label>
            <input type="file" name="image" accept="image/*" class="form-control">

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
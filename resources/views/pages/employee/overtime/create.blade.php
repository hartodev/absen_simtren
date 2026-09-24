@extends('layouts.employee')

@section('title', 'Ajukan Lembur')

@section('breadcrumb')
<a href="{{ route('company.member.overtimes.index') }}">Lembur</a>
<span class="current">Ajukan Baru</span>
@endsection

@section('content')
<div class="page-title">Ajukan Lembur</div>

@if($errors->any())
<div class="alert alert-danger" style="margin-bottom:14px">{{ $errors->first() }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger" style="margin-bottom:14px">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('company.member.overtimes.store') }}" enctype="multipart/form-data">
            @csrf

            <label class="form-label">Tanggal</label>
            <input type="date" name="date" class="form-control" value="{{ old('date') }}" required>

            <div style="display:flex;gap:10px;margin-top:12px">
                <div style="flex:1">
                    <label class="form-label">Jam Mulai</label>
                    <input type="time" name="start_time" class="form-control" value="{{ old('start_time') }}" required>
                </div>
                <div style="flex:1">
                    <label class="form-label">Jam Selesai</label>
                    <input type="time" name="end_time" class="form-control" value="{{ old('end_time') }}" required>
                </div>
            </div>
            <p style="font-size:12px;color:var(--c-muted);margin-top:6px">
                Jika jam selesai lebih kecil dari jam mulai, dianggap lewat tengah malam.
            </p>

            @if($attendances->isNotEmpty())
            <label class="form-label" style="margin-top:12px">Kaitkan dengan Absensi (opsional)</label>
            <select name="attendance_id" class="form-control">
                <option value="">— Tidak dikaitkan —</option>
                @foreach($attendances as $a)
                <option value="{{ $a->id }}" {{ old('attendance_id') == $a->id ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::parse($a->date)->isoFormat('D MMM Y') }}
                </option>
                @endforeach
            </select>
            @endif

            <label class="form-label" style="margin-top:12px">Alasan (opsional)</label>
            <textarea name="reason" rows="3" class="form-control">{{ old('reason') }}</textarea>

            <label class="form-label" style="margin-top:12px">Bukti Foto (opsional)</label>
            <input type="file" name="evidence_image" accept="image/*" class="form-control">

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
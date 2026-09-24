@extends('layouts.admin')
@php $edit = $device->exists; @endphp
@section('title', $edit ? 'Edit Device' : 'Tambah Device')
@section('width', 'max-w-2xl')
@section('actions')
    <a href="{{ route($rp.'devices.index') }}" class="btn btn-line"><i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali</a>
@endsection

@section('content')
<form method="POST" action="{{ $edit ? route($rp.'devices.update', $device->id) : route($rp.'devices.store') }}" class="card space-y-4 p-6">
    @csrf
    @if ($edit) @method('PUT') @endif

    <div><label class="label">Nama device *</label>
        <input name="name" value="{{ old('name', $device->name) }}" placeholder="Kiosk Kelas 1A / Tablet Ruang Guru" required class="input"></div>

    <div class="grid gap-4 md:grid-cols-2">
        <div><label class="label">Khusus kelas</label>
            <select name="class_id" class="input">
                <option value="">Umum — semua kelas</option>
                @foreach ($classes as $c)
                    <option value="{{ $c->id }}" @selected(old('class_id', $device->class_id) == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select></div>
        <div><label class="label">Identifier perangkat (opsional)</label>
            <input name="device_identifier" value="{{ old('device_identifier', $device->device_identifier) }}" placeholder="Serial number / IMEI" class="input"></div>
    </div>

    <label class="flex w-fit items-center gap-2 rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $device->is_active)) class="h-4 w-4"> Device aktif
    </label>

    <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
        <a href="{{ route($rp.'devices.index') }}" class="btn btn-line">Batal</a>
        <button class="btn btn-brand px-6">{{ $edit ? 'Simpan' : 'Daftarkan & buat token' }}</button>
    </div>
</form>
@endsection

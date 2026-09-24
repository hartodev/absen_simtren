@extends('layouts.mobile')
@php $edit = $device->exists; @endphp
@section('title', $edit ? 'Edit Device' : 'Tambah Device')
@section('back', route($rp.'devices.index'))

@section('content')
<form method="POST" action="{{ $edit ? route($rp.'devices.update', $device->id) : route($rp.'devices.store') }}" class="space-y-3">
    @csrf
    @if ($edit) @method('PUT') @endif

    <div><label class="mb-1 block text-xs font-semibold text-slate-500">Nama device *</label>
        <input name="name" value="{{ old('name', $device->name) }}" placeholder="Kiosk Kelas 1A / Tablet Ruang Guru" required class="input"></div>

    <div><label class="mb-1 block text-xs font-semibold text-slate-500">Khusus kelas</label>
        <select name="class_id" class="input">
            <option value="">Umum — bisa dipakai semua kelas</option>
            @foreach ($classes as $c)
                <option value="{{ $c->id }}" @selected(old('class_id', $device->class_id) == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select></div>

    <div><label class="mb-1 block text-xs font-semibold text-slate-500">Identifier perangkat (opsional)</label>
        <input name="device_identifier" value="{{ old('device_identifier', $device->device_identifier) }}" placeholder="Serial number / IMEI" class="input"></div>

    <label class="flex items-center gap-2 rounded-xl bg-white px-3 py-3 text-sm ring-1 ring-slate-200">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $device->is_active)) class="h-4 w-4"> Device aktif
    </label>

    <button class="btn btn-brand w-full py-3">{{ $edit ? 'Simpan' : 'Daftarkan & buat token' }}</button>
</form>
@endsection

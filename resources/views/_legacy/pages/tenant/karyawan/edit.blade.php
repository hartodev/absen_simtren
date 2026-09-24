@extends('layouts.company')
@section('title', 'Ubah Karyawan')
@section('nav')
  <a href="{{ route('tenant.dashboard') }}">Dashboard</a>
  <a href="{{ route('tenant.karyawan.index') }}" class="text-[#2563eb]">Karyawan</a>
  <a href="{{ route('tenant.absensi-karyawan.index') }}">Absensi</a>
  <a href="{{ route('tenant.absensi-karyawan.absen') }}">Absen Sekarang</a>
@endsection
@section('content')
<h1 class="text-xl font-extrabold text-[#102b69]">Ubah Karyawan</h1>
@if ($errors->any())
  <div class="mt-4 rounded-lg bg-[#fff1f1] px-3 py-2 text-xs font-semibold text-[#b34040]">{{ $errors->first() }}</div>
@endif
<form method="POST" action="{{ route('tenant.karyawan.update', $karyawan) }}" class="mt-5 max-w-md space-y-4 rounded-xl bg-white p-6 shadow-sm">
  @csrf @method('PUT')
  <label class="block">
    <span class="mb-2 block text-xs font-bold text-[#405978]">Nama</span>
    <input type="text" name="nama" required value="{{ old('nama', $karyawan->nama) }}" class="form-control h-12">
  </label>
  <label class="block">
    <span class="mb-2 block text-xs font-bold text-[#405978]">NIP (opsional)</span>
    <input type="text" name="nip" value="{{ old('nip', $karyawan->nip) }}" class="form-control h-12">
  </label>
  <label class="block">
    <span class="mb-2 block text-xs font-bold text-[#405978]">Jabatan</span>
    <input type="text" name="jabatan" value="{{ old('jabatan', $karyawan->jabatan) }}" class="form-control h-12">
  </label>
  <label class="block">
    <span class="mb-2 block text-xs font-bold text-[#405978]">Departemen</span>
    <input type="text" name="departemen" value="{{ old('departemen', $karyawan->departemen) }}" class="form-control h-12">
  </label>
  <label class="block">
    <span class="mb-2 block text-xs font-bold text-[#405978]">No. HP</span>
    <input type="text" name="no_hp" value="{{ old('no_hp', $karyawan->no_hp) }}" class="form-control h-12">
  </label>
  <label class="block">
    <span class="mb-2 block text-xs font-bold text-[#405978]">Email</span>
    <input type="email" name="email" value="{{ old('email', $karyawan->email) }}" class="form-control h-12">
  </label>
  <div class="grid grid-cols-2 gap-3">
    <label class="block">
      <span class="mb-2 block text-xs font-bold text-[#405978]">Jam Masuk</span>
      <input type="time" name="jam_masuk" value="{{ old('jam_masuk', $karyawan->jam_masuk) }}" class="form-control h-12">
    </label>
    <label class="block">
      <span class="mb-2 block text-xs font-bold text-[#405978]">Jam Pulang</span>
      <input type="time" name="jam_pulang" value="{{ old('jam_pulang', $karyawan->jam_pulang) }}" class="form-control h-12">
    </label>
  </div>
  <label class="flex items-center gap-2">
    <input type="hidden" name="status_aktif" value="0">
    <input type="checkbox" name="status_aktif" value="1" @checked(old('status_aktif', $karyawan->status_aktif)) class="h-4 w-4">
    <span class="text-xs font-bold text-[#405978]">Karyawan aktif</span>
  </label>
  <button type="submit" class="button-lift w-full rounded-xl bg-[#2563eb] py-3 text-sm font-bold text-white">Simpan Perubahan</button>
</form>
@endsection

@php
  $prefix = request()->segment(1);
  $istilah = ['company' => 'Karyawan', 'pesantren' => 'Santri', 'school' => 'Siswa'][$prefix] ?? 'Anggota';
@endphp
@extends('layouts.company')
@section('title', 'Tambah ' . $istilah)
@section('content')
<h1 class="text-xl font-extrabold text-[#102b69]">Tambah {{ $istilah }}</h1>

@if ($errors->any())
  <div class="mt-4 rounded-lg bg-red-50 px-3 py-2 text-sm font-semibold text-red-600">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ url('/' . $prefix . '/employees') }}" class="mt-5 max-w-md space-y-4 rounded-xl bg-white p-6 shadow-sm">
  @csrf
  <div>
    <label class="mb-1 block text-xs font-bold text-gray-500">Nama Lengkap</label>
    <input type="text" name="name" required value="{{ old('name') }}" class="h-12 w-full rounded-lg border border-gray-300 px-3">
  </div>
  <div>
    <label class="mb-1 block text-xs font-bold text-gray-500">Email (untuk login)</label>
    <input type="email" name="email" required value="{{ old('email') }}" class="h-12 w-full rounded-lg border border-gray-300 px-3">
  </div>
  <div>
    <label class="mb-1 block text-xs font-bold text-gray-500">Departemen</label>
    <input type="text" name="department" required value="{{ old('department') }}" class="h-12 w-full rounded-lg border border-gray-300 px-3">
  </div>
  <div>
    <label class="mb-1 block text-xs font-bold text-gray-500">Jabatan</label>
    <input type="text" name="position" required value="{{ old('position') }}" class="h-12 w-full rounded-lg border border-gray-300 px-3">
  </div>
  <div class="grid grid-cols-2 gap-3">
    <div>
      <label class="mb-1 block text-xs font-bold text-gray-500">Password</label>
      <input type="password" name="password" required minlength="6" class="h-12 w-full rounded-lg border border-gray-300 px-3">
    </div>
    <div>
      <label class="mb-1 block text-xs font-bold text-gray-500">Konfirmasi</label>
      <input type="password" name="password_confirmation" required minlength="6" class="h-12 w-full rounded-lg border border-gray-300 px-3">
    </div>
  </div>
  <button type="submit" class="w-full rounded-lg bg-blue-600 py-3 text-sm font-bold text-white">Simpan</button>
</form>
@endsection

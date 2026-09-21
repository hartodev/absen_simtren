@php
  $prefix = request()->segment(1);
  $istilah = ['company' => 'Karyawan', 'pesantren' => 'Santri', 'school' => 'Siswa'][$prefix] ?? 'Anggota';
@endphp
@extends('layouts.tenant')
@section('title', 'Ubah ' . $istilah)
@section('nav')
  <a href="{{ url('/' . $prefix . '/dashboard') }}">Dashboard</a>
  <a href="{{ url('/' . $prefix . '/attendances') }}">Absensi</a>
  <a href="{{ url('/' . $prefix . '/employees') }}" class="text-blue-600">{{ $istilah }}</a>
@endsection
@section('content')
<h1 class="text-xl font-extrabold text-[#102b69]">Ubah {{ $istilah }}</h1>

@if ($errors->any())
  <div class="mt-4 rounded-lg bg-red-50 px-3 py-2 text-sm font-semibold text-red-600">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ url('/' . $prefix . '/employees/' . $employee->id) }}" class="mt-5 max-w-md space-y-4 rounded-xl bg-white p-6 shadow-sm">
  @csrf @method('PUT')
  <div>
    <label class="mb-1 block text-xs font-bold text-gray-500">Nama Lengkap</label>
    <input type="text" name="name" required value="{{ old('name', $employee->name) }}" class="h-12 w-full rounded-lg border border-gray-300 px-3">
  </div>
  <div>
    <label class="mb-1 block text-xs font-bold text-gray-500">Email (untuk login)</label>
    <input type="email" name="email" required value="{{ old('email', $employee->email) }}" class="h-12 w-full rounded-lg border border-gray-300 px-3">
  </div>
  <div class="grid grid-cols-2 gap-3">
    <div>
      <label class="mb-1 block text-xs font-bold text-gray-500">Password Baru (opsional)</label>
      <input type="password" name="password" minlength="6" class="h-12 w-full rounded-lg border border-gray-300 px-3">
    </div>
    <div>
      <label class="mb-1 block text-xs font-bold text-gray-500">Konfirmasi</label>
      <input type="password" name="password_confirmation" minlength="6" class="h-12 w-full rounded-lg border border-gray-300 px-3">
    </div>
  </div>
  <p class="text-xs text-gray-400">Kosongkan password kalau tidak ingin mengubahnya.</p>
  <button type="submit" class="w-full rounded-lg bg-blue-600 py-3 text-sm font-bold text-white">Simpan Perubahan</button>
</form>
@endsection

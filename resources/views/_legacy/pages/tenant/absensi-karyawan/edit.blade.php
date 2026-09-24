@extends('layouts.company')
@section('title', 'Ubah Absensi Karyawan')
@section('nav')
  <a href="{{ route('tenant.dashboard') }}">Dashboard</a>
  <a href="{{ route('tenant.karyawan.index') }}">Karyawan</a>
  <a href="{{ route('tenant.absensi-karyawan.index') }}" class="text-[#2563eb]">Absensi</a>
  <a href="{{ route('tenant.absensi-karyawan.absen') }}">Absen Sekarang</a>
@endsection
@section('content')
<h1 class="text-xl font-extrabold text-[#102b69]">Ubah Absensi Karyawan</h1>
@if ($errors->any())
  <div class="mt-4 rounded-lg bg-[#fff1f1] px-3 py-2 text-xs font-semibold text-[#b34040]">{{ $errors->first() }}</div>
@endif
<form method="POST" action="{{ route('tenant.absensi-karyawan.update', $absensi) }}" class="mt-5 max-w-md space-y-4 rounded-xl bg-white p-6 shadow-sm">
  @csrf @method('PUT')
  <label class="block">
    <span class="mb-2 block text-xs font-bold text-[#405978]">Karyawan</span>
    <select name="karyawan_id" required class="form-control h-12">
      @foreach ($karyawan as $k)
        <option value="{{ $k->id }}" @selected(old('karyawan_id', $absensi->karyawan_id) == $k->id)>{{ $k->nama }}</option>
      @endforeach
    </select>
  </label>
  <label class="block">
    <span class="mb-2 block text-xs font-bold text-[#405978]">Tanggal</span>
    <input type="date" name="tanggal" required
           value="{{ old('tanggal', \Illuminate\Support\Carbon::parse($absensi->tanggal)->toDateString()) }}"
           class="form-control h-12">
  </label>
  <div class="grid grid-cols-2 gap-3">
    <label class="block">
      <span class="mb-2 block text-xs font-bold text-[#405978]">Jam Masuk</span>
      <input type="time" name="jam_masuk" value="{{ old('jam_masuk', $absensi->jam_masuk) }}" class="form-control h-12">
    </label>
    <label class="block">
      <span class="mb-2 block text-xs font-bold text-[#405978]">Jam Pulang</span>
      <input type="time" name="jam_pulang" value="{{ old('jam_pulang', $absensi->jam_pulang) }}" class="form-control h-12">
    </label>
  </div>
  <label class="block">
    <span class="mb-2 block text-xs font-bold text-[#405978]">Status</span>
    <select name="status" required class="form-control h-12">
      @foreach (['hadir' => 'Hadir', 'telat' => 'Telat', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa', 'lembur' => 'Lembur'] as $val => $label)
        <option value="{{ $val }}" @selected(old('status', $absensi->status) == $val)>{{ $label }}</option>
      @endforeach
    </select>
  </label>
  <label class="block">
    <span class="mb-2 block text-xs font-bold text-[#405978]">Keterangan (opsional)</span>
    <textarea name="keterangan" rows="3" class="form-control">{{ old('keterangan', $absensi->keterangan) }}</textarea>
  </label>
  <button type="submit" class="button-lift w-full rounded-xl bg-[#2563eb] py-3 text-sm font-bold text-white">Simpan Perubahan</button>
</form>
@endsection

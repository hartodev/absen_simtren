@extends('layouts.tenant')
@section('title', 'Catat Presensi')
@section('content')
<h1 class="text-xl font-extrabold text-[#102b69]">Catat Presensi</h1>
@if ($errors->any())
  <div class="mt-4 rounded-lg bg-[#fff1f1] px-3 py-2 text-xs font-semibold text-[#b34040]">{{ $errors->first() }}</div>
@endif
<form method="POST" action="{{ route('tenant.presensi.store') }}" class="mt-5 max-w-md space-y-4 rounded-xl bg-white p-6 shadow-sm">
  @csrf
  <label class="block">
    <span class="mb-2 block text-xs font-bold text-[#405978]">Siswa</span>
    <select name="siswa_id" required class="form-control h-12">
      <option value="">-- Pilih Siswa --</option>
      @foreach ($siswa as $s)
        <option value="{{ $s->id }}" @selected(old('siswa_id') == $s->id)>{{ $s->nama }}</option>
      @endforeach
    </select>
  </label>
  <label class="block">
    <span class="mb-2 block text-xs font-bold text-[#405978]">Tanggal</span>
    <input type="date" name="tanggal" required value="{{ old('tanggal', now()->toDateString()) }}" class="form-control h-12">
  </label>
  <label class="block">
    <span class="mb-2 block text-xs font-bold text-[#405978]">Status</span>
    <select name="status" required class="form-control h-12">
      <option value="hadir" @selected(old('status')=='hadir')>Hadir</option>
      <option value="izin" @selected(old('status')=='izin')>Izin</option>
      <option value="sakit" @selected(old('status')=='sakit')>Sakit</option>
      <option value="alpa" @selected(old('status')=='alpa')>Alpa</option>
    </select>
  </label>
  <button type="submit" class="button-lift w-full rounded-xl bg-[#2563eb] py-3 text-sm font-bold text-white">Simpan</button>
</form>
@endsection

@extends('layouts.company')
@section('title', 'Tambah Siswa')
@section('content')
<h1 class="text-xl font-extrabold text-[#102b69]">Tambah Siswa</h1>
@if ($errors->any())
  <div class="mt-4 rounded-lg bg-[#fff1f1] px-3 py-2 text-xs font-semibold text-[#b34040]">{{ $errors->first() }}</div>
@endif
<form method="POST" action="{{ route('tenant.siswa.store') }}" class="mt-5 max-w-md space-y-4 rounded-xl bg-white p-6 shadow-sm">
  @csrf
  <label class="block">
    <span class="mb-2 block text-xs font-bold text-[#405978]">Nama</span>
    <input type="text" name="nama" required value="{{ old('nama') }}" class="form-control h-12">
  </label>
  <label class="block">
    <span class="mb-2 block text-xs font-bold text-[#405978]">NIS (opsional)</span>
    <input type="text" name="nis" value="{{ old('nis') }}" class="form-control h-12">
  </label>
  <label class="block">
    <span class="mb-2 block text-xs font-bold text-[#405978]">Kelas</span>
    <select name="kelas_id" required class="form-control h-12">
      <option value="">-- Pilih Kelas --</option>
      @foreach ($kelas as $k)
        <option value="{{ $k->id }}" @selected(old('kelas_id') == $k->id)>{{ $k->nama_kelas }}</option>
      @endforeach
    </select>
  </label>
  <button type="submit" class="button-lift w-full rounded-xl bg-[#2563eb] py-3 text-sm font-bold text-white">Simpan</button>
</form>
@endsection

@extends('layouts.superadmin')
@section('title', 'Tambah Organisasi')
@section('content')
<h1 class="text-xl font-extrabold text-[#102b69]">Tambah Organisasi Manual</h1>
@if ($errors->any())
  <div class="mt-4 rounded-lg bg-red-50 px-3 py-2 text-sm font-semibold text-red-600">{{ $errors->first() }}</div>
@endif
<form method="POST" action="{{ route('superadmin.tenants.store') }}" class="mt-5 max-w-lg space-y-4 rounded-xl bg-white p-6 shadow-sm">
  @csrf
  <div>
    <label class="mb-1 block text-xs font-bold text-gray-500">Nama Organisasi</label>
    <input type="text" name="name" required value="{{ old('name') }}" class="h-12 w-full rounded-lg border border-gray-300 px-3">
  </div>
  <div>
    <label class="mb-1 block text-xs font-bold text-gray-500">Tipe</label>
    <select name="type" required class="h-12 w-full rounded-lg border border-gray-300 px-3">
      @foreach ($types as $key => $t)
        <option value="{{ $key }}">{{ $t['label'] }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label class="mb-1 block text-xs font-bold text-gray-500">Subdomain</label>
    <input type="text" name="subdomain" required value="{{ old('subdomain') }}" class="h-12 w-full rounded-lg border border-gray-300 px-3">
  </div>
  <div>
    <label class="mb-1 block text-xs font-bold text-gray-500">Status</label>
    <select name="status" required class="h-12 w-full rounded-lg border border-gray-300 px-3">
      <option value="aktif">Aktif</option>
      <option value="pending">Pending</option>
      <option value="nonaktif">Nonaktif</option>
    </select>
  </div>
  <button type="submit" class="w-full rounded-lg bg-blue-600 py-3 text-sm font-bold text-white">Simpan</button>
</form>
@endsection

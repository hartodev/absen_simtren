@php
  $prefix = request()->segment(1);
  $istilah = ['company' => 'Karyawan', 'pesantren' => 'Santri', 'school' => 'Siswa'][$prefix] ?? 'Anggota';
@endphp
@extends('layouts.tenant')
@section('title', $istilah)
@section('content')
<div class="flex items-center justify-between">
  <h1 class="text-xl font-extrabold text-[#102b69]">Data {{ $istilah }}</h1>
  <a href="{{ url('/' . $prefix . '/employees/create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-bold text-white">+ Tambah {{ $istilah }}</a>
</div>

<form method="GET" class="mt-4">
  <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama..." class="h-11 w-full max-w-xs rounded-lg border border-gray-300 px-3 text-sm">
</form>

<div class="mt-5 overflow-hidden rounded-xl bg-white shadow-sm">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 text-left text-xs font-bold uppercase text-gray-400">
      <tr><th class="px-4 py-3">Nama</th><th class="px-4 py-3">Email</th><th class="px-4 py-3 text-right">Aksi</th></tr>
    </thead>
    <tbody class="divide-y">
      @forelse ($karyawan as $e)
        <tr>
          <td class="px-4 py-3 font-semibold">{{ $e->name }}</td>
          <td class="px-4 py-3 text-gray-500">{{ $e->email }}</td>
          <td class="px-4 py-3 text-right space-x-3">
            <a href="{{ url('/' . $prefix . '/employees/' . $e->id . '/edit') }}" class="font-bold text-blue-600">Ubah</a>
            <form method="POST" action="{{ url('/' . $prefix . '/employees/' . $e->id) }}" class="inline" onsubmit="return confirm('Hapus {{ strtolower($istilah) }} ini?')">
              @csrf @method('DELETE')
              <button type="submit" class="font-bold text-red-600">Hapus</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">Belum ada {{ strtolower($istilah) }}.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $karyawan->links() }}</div>
@endsection

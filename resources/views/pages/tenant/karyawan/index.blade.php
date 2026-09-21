@extends('layouts.tenant')
@section('title', 'Karyawan')
@section('nav')
  <a href="{{ route('tenant.dashboard') }}">Dashboard</a>
  <a href="{{ route('tenant.karyawan.index') }}" class="text-[#2563eb]">Karyawan</a>
  <a href="{{ route('tenant.absensi-karyawan.index') }}">Absensi</a>
  <a href="{{ route('tenant.absensi-karyawan.absen') }}">Absen Sekarang</a>
@endsection
@section('content')
<div class="flex items-center justify-between">
  <h1 class="text-xl font-extrabold text-[#102b69]">Daftar Karyawan</h1>
  <a href="{{ route('tenant.karyawan.create') }}" class="button-lift rounded-lg bg-[#2563eb] px-4 py-2 text-xs font-bold text-white">+ Tambah Karyawan</a>
</div>

<div class="mt-5 overflow-hidden rounded-xl bg-white shadow-sm">
  <table class="w-full text-sm">
    <thead class="bg-[#f6f8fb] text-left text-xs font-bold uppercase text-[#71839b]">
      <tr>
        <th class="px-5 py-3">Nama</th>
        <th class="px-5 py-3">NIP</th>
        <th class="px-5 py-3">Jabatan</th>
        <th class="px-5 py-3">Departemen</th>
        <th class="px-5 py-3">Absen Hari Ini</th>
        <th class="px-5 py-3">Status</th>
        <th class="px-5 py-3 text-right">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-[#eef2f7]">
      @forelse ($karyawan as $k)
        <tr>
          <td class="px-5 py-3 font-semibold text-[#102b69]">{{ $k->nama }}</td>
          <td class="px-5 py-3 text-[#71839b]">{{ $k->nip ?? '-' }}</td>
          <td class="px-5 py-3 text-[#71839b]">{{ $k->jabatan ?? '-' }}</td>
          <td class="px-5 py-3 text-[#71839b]">{{ $k->departemen ?? '-' }}</td>
          <td class="px-5 py-3 text-[#71839b]">
            @if ($k->absensiHariIni)
              <span class="rounded-full bg-[#e0f4ea] px-2 py-1 text-xs font-bold text-[#198754] capitalize">{{ $k->absensiHariIni->status }}</span>
            @else
              <span class="rounded-full bg-[#fff1f1] px-2 py-1 text-xs font-bold text-[#b34040]">Belum absen</span>
            @endif
          </td>
          <td class="px-5 py-3">
            @if ($k->status_aktif)
              <span class="text-xs font-bold text-[#198754]">Aktif</span>
            @else
              <span class="text-xs font-bold text-[#b34040]">Nonaktif</span>
            @endif
          </td>
          <td class="px-5 py-3 text-right space-x-3">
            <a href="{{ route('tenant.karyawan.edit', $k) }}" class="font-bold text-[#2563eb]">Ubah</a>
            <form method="POST" action="{{ route('tenant.karyawan.destroy', $k) }}" class="inline" onsubmit="return confirm('Hapus karyawan ini?')">
              @csrf @method('DELETE')
              <button type="submit" class="font-bold text-[#b34040]">Hapus</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="7" class="px-5 py-6 text-center text-[#8192aa]">Belum ada karyawan.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $karyawan->links() }}</div>
@endsection

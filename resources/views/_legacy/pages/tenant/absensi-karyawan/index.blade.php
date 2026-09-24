@extends('layouts.company')
@section('title', 'Absensi Karyawan')
@section('nav')
  <a href="{{ route('tenant.dashboard') }}">Dashboard</a>
  <a href="{{ route('tenant.karyawan.index') }}">Karyawan</a>
  <a href="{{ route('tenant.absensi-karyawan.index') }}" class="text-[#2563eb]">Absensi</a>
  <a href="{{ route('tenant.absensi-karyawan.absen') }}">Absen Sekarang</a>
@endsection
@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
  <h1 class="text-xl font-extrabold text-[#102b69]">Rekap Absensi Karyawan</h1>
  <a href="{{ route('tenant.absensi-karyawan.create') }}" class="button-lift rounded-lg bg-[#2563eb] px-4 py-2 text-xs font-bold text-white">+ Catat Manual</a>
</div>

<form method="GET" class="mt-4 flex flex-wrap items-end gap-3 rounded-xl bg-white p-4 shadow-sm">
  <label class="block">
    <span class="mb-1 block text-xs font-bold text-[#405978]">Tanggal</span>
    <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control h-10">
  </label>
  <label class="block">
    <span class="mb-1 block text-xs font-bold text-[#405978]">Karyawan</span>
    <select name="karyawan_id" class="form-control h-10">
      <option value="">-- Semua Karyawan --</option>
      @foreach ($karyawan as $k)
        <option value="{{ $k->id }}" @selected(request('karyawan_id') == $k->id)>{{ $k->nama }}</option>
      @endforeach
    </select>
  </label>
  <button type="submit" class="button-lift rounded-lg bg-[#2563eb] px-4 py-2 text-xs font-bold text-white h-10">Filter</button>
  <a href="{{ route('tenant.absensi-karyawan.index') }}" class="text-xs font-bold text-[#71839b]">Reset</a>
</form>

<div class="mt-5 overflow-hidden rounded-xl bg-white shadow-sm">
  <table class="w-full text-sm">
    <thead class="bg-[#f6f8fb] text-left text-xs font-bold uppercase text-[#71839b]">
      <tr>
        <th class="px-5 py-3">Tanggal</th>
        <th class="px-5 py-3">Nama</th>
        <th class="px-5 py-3">Jam Masuk</th>
        <th class="px-5 py-3">Jam Pulang</th>
        <th class="px-5 py-3">Status</th>
        <th class="px-5 py-3 text-right">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-[#eef2f7]">
      @forelse ($absensi as $a)
        <tr>
          <td class="px-5 py-3 text-[#71839b]">{{ \Illuminate\Support\Carbon::parse($a->tanggal)->format('d M Y') }}</td>
          <td class="px-5 py-3 font-semibold text-[#102b69]">{{ $a->karyawan->nama ?? '-' }}</td>
          <td class="px-5 py-3 text-[#71839b]">{{ $a->jam_masuk ? \Illuminate\Support\Carbon::parse($a->jam_masuk)->format('H:i') : '-' }}</td>
          <td class="px-5 py-3 text-[#71839b]">{{ $a->jam_pulang ? \Illuminate\Support\Carbon::parse($a->jam_pulang)->format('H:i') : '-' }}</td>
          <td class="px-5 py-3">
            <span class="rounded-full bg-[#f6f8fb] px-2 py-1 text-xs font-bold capitalize text-[#405978]">{{ $a->status }}</span>
          </td>
          <td class="px-5 py-3 text-right space-x-3">
            <a href="{{ route('tenant.absensi-karyawan.edit', $a) }}" class="font-bold text-[#2563eb]">Ubah</a>
            <form method="POST" action="{{ route('tenant.absensi-karyawan.destroy', $a) }}" class="inline" onsubmit="return confirm('Hapus absensi ini?')">
              @csrf @method('DELETE')
              <button type="submit" class="font-bold text-[#b34040]">Hapus</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" class="px-5 py-6 text-center text-[#8192aa]">Belum ada data absensi.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $absensi->links() }}</div>
@endsection

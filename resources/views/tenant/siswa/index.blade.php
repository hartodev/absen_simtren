@extends('layouts.tenant')
@section('title', 'Siswa')
@section('nav')
  <a href="{{ route('tenant.dashboard') }}">Dashboard</a>
  <a href="{{ route('tenant.siswa.index') }}" class="text-[#2563eb]">Siswa</a>
  <a href="{{ route('tenant.kelas.index') }}">Kelas</a>
  <a href="{{ route('tenant.presensi.index') }}">Presensi</a>
@endsection
@section('content')
<div class="flex items-center justify-between">
  <h1 class="text-xl font-extrabold text-[#102b69]">Daftar Siswa</h1>
  <a href="{{ route('tenant.siswa.create') }}" class="button-lift rounded-lg bg-[#2563eb] px-4 py-2 text-xs font-bold text-white">+ Tambah Siswa</a>
</div>

<div class="mt-5 overflow-hidden rounded-xl bg-white shadow-sm">
  <table class="w-full text-sm">
    <thead class="bg-[#f6f8fb] text-left text-xs font-bold uppercase text-[#71839b]">
      <tr><th class="px-5 py-3">Nama</th><th class="px-5 py-3">NIS</th><th class="px-5 py-3">Kelas</th><th class="px-5 py-3 text-right">Aksi</th></tr>
    </thead>
    <tbody class="divide-y divide-[#eef2f7]">
      @forelse ($siswa as $s)
        <tr>
          <td class="px-5 py-3 font-semibold text-[#102b69]">{{ $s->nama }}</td>
          <td class="px-5 py-3 text-[#71839b]">{{ $s->nis ?? '-' }}</td>
          <td class="px-5 py-3 text-[#71839b]">{{ $s->kelas->nama_kelas ?? '-' }}</td>
          <td class="px-5 py-3 text-right space-x-3">
            <a href="{{ route('tenant.siswa.edit', $s) }}" class="font-bold text-[#2563eb]">Ubah</a>
            <form method="POST" action="{{ route('tenant.siswa.destroy', $s) }}" class="inline" onsubmit="return confirm('Hapus siswa ini?')">
              @csrf @method('DELETE')
              <button type="submit" class="font-bold text-[#b34040]">Hapus</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="4" class="px-5 py-6 text-center text-[#8192aa]">Belum ada siswa.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $siswa->links() }}</div>
@endsection

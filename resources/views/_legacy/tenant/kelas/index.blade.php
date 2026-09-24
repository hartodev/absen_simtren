@extends('layouts.company')
@section('title', 'Kelas')
@section('nav')
  <a href="{{ route('tenant.dashboard') }}">Dashboard</a>
  <a href="{{ route('tenant.siswa.index') }}">Siswa</a>
  <a href="{{ route('tenant.kelas.index') }}" class="text-[#2563eb]">Kelas</a>
  <a href="{{ route('tenant.presensi.index') }}">Presensi</a>
@endsection
@section('content')
<div class="flex items-center justify-between">
  <h1 class="text-xl font-extrabold text-[#102b69]">Daftar Kelas</h1>
  <a href="{{ route('tenant.kelas.create') }}" class="button-lift rounded-lg bg-[#2563eb] px-4 py-2 text-xs font-bold text-white">+ Tambah Kelas</a>
</div>

<div class="mt-5 overflow-hidden rounded-xl bg-white shadow-sm">
  <table class="w-full text-sm">
    <thead class="bg-[#f6f8fb] text-left text-xs font-bold uppercase text-[#71839b]">
      <tr><th class="px-5 py-3">Nama Kelas</th><th class="px-5 py-3">Jumlah Siswa</th><th class="px-5 py-3 text-right">Aksi</th></tr>
    </thead>
    <tbody class="divide-y divide-[#eef2f7]">
      @forelse ($kelas as $k)
        <tr>
          <td class="px-5 py-3 font-semibold text-[#102b69]">{{ $k->nama_kelas }}</td>
          <td class="px-5 py-3 text-[#71839b]">{{ $k->siswa_count }}</td>
          <td class="px-5 py-3 text-right space-x-3">
            <a href="{{ route('tenant.kelas.edit', $k) }}" class="font-bold text-[#2563eb]">Ubah</a>
            <form method="POST" action="{{ route('tenant.kelas.destroy', $k) }}" class="inline" onsubmit="return confirm('Hapus kelas ini?')">
              @csrf @method('DELETE')
              <button type="submit" class="font-bold text-[#b34040]">Hapus</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="3" class="px-5 py-6 text-center text-[#8192aa]">Belum ada kelas.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $kelas->links() }}</div>
@endsection

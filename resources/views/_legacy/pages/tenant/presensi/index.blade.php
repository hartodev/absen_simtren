@extends('layouts.company')
@section('title', 'Presensi')
@section('nav')
  <a href="{{ route('tenant.dashboard') }}">Dashboard</a>
  <a href="{{ route('tenant.siswa.index') }}">Siswa</a>
  <a href="{{ route('tenant.kelas.index') }}">Kelas</a>
  <a href="{{ route('tenant.presensi.index') }}" class="text-[#2563eb]">Presensi</a>
@endsection
@section('content')
<div class="flex items-center justify-between">
  <h1 class="text-xl font-extrabold text-[#102b69]">Presensi</h1>
  <a href="{{ route('tenant.presensi.create') }}" class="button-lift rounded-lg bg-[#2563eb] px-4 py-2 text-xs font-bold text-white">+ Catat Presensi</a>
</div>

<div class="mt-5 overflow-hidden rounded-xl bg-white shadow-sm">
  <table class="w-full text-sm">
    <thead class="bg-[#f6f8fb] text-left text-xs font-bold uppercase text-[#71839b]">
      <tr><th class="px-5 py-3">Tanggal</th><th class="px-5 py-3">Siswa</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Aksi</th></tr>
    </thead>
    <tbody class="divide-y divide-[#eef2f7]">
      @forelse ($presensi as $p)
        <tr>
          <td class="px-5 py-3 text-[#71839b]">{{ \Illuminate\Support\Carbon::parse($p->tanggal)->format('d M Y') }}</td>
          <td class="px-5 py-3 font-semibold text-[#102b69]">{{ $p->siswa->nama ?? '-' }}</td>
          <td class="px-5 py-3">
            <span class="rounded-full px-2.5 py-1 text-xs font-bold
              @class([
                'bg-[#e0f4ea] text-[#198754]' => $p->status === 'hadir',
                'bg-[#fff8e6] text-[#b8860b]' => in_array($p->status, ['izin','sakit']),
                'bg-[#fff1f1] text-[#b34040]' => $p->status === 'alpa',
              ])">{{ ucfirst($p->status) }}</span>
          </td>
          <td class="px-5 py-3 text-right space-x-3">
            <a href="{{ route('tenant.presensi.edit', $p) }}" class="font-bold text-[#2563eb]">Ubah</a>
            <form method="POST" action="{{ route('tenant.presensi.destroy', $p) }}" class="inline" onsubmit="return confirm('Hapus data ini?')">
              @csrf @method('DELETE')
              <button type="submit" class="font-bold text-[#b34040]">Hapus</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="4" class="px-5 py-6 text-center text-[#8192aa]">Belum ada data presensi.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $presensi->links() }}</div>
@endsection

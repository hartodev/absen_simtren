@extends('layouts.tenant')
@section('title', 'Absensi')
@section('nav')
  <a href="{{ url('/' . request()->segment(1) . '/dashboard') }}">Dashboard</a>
  <a href="{{ url('/' . request()->segment(1) . '/attendances') }}" class="text-blue-600">Absensi</a>
  <a href="{{ url('/' . request()->segment(1) . '/employees') }}">Anggota</a>
@endsection
@section('content')
<h1 class="text-xl font-extrabold text-[#102b69]">Data Absensi</h1>
<div class="mt-5 overflow-hidden rounded-xl bg-white shadow-sm">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 text-left text-xs font-bold uppercase text-gray-400">
      <tr><th class="px-4 py-3">Nama</th><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Masuk</th><th class="px-4 py-3">Pulang</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr>
    </thead>
    <tbody class="divide-y">
      @forelse ($attendances as $a)
        <tr>
          <td class="px-4 py-3 font-semibold">{{ $a->user->name ?? '-' }}</td>
          <td class="px-4 py-3">{{ optional($a->date)->format('d M Y') }}</td>
          <td class="px-4 py-3">{{ $a->time_in ?? '-' }}</td>
          <td class="px-4 py-3">{{ $a->time_out ?? '-' }}</td>
          <td class="px-4 py-3">{{ ucfirst($a->status) }}</td>
          <td class="px-4 py-3"><a href="{{ url('/' . request()->segment(1) . '/attendances/' . $a->id) }}" class="font-bold text-blue-600">Detail</a></td>
        </tr>
      @empty
        <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Belum ada data absensi.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $attendances->links() }}</div>
@endsection

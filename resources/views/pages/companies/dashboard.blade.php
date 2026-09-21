@php
  // Prefix URL saat ini (company / pesantren / school) -- satu view ini dipakai
  // bersama oleh admin ketiga tipe lembaga, jadi link menyesuaikan otomatis.
  $prefix = request()->segment(1);
  $istilah = ['company' => 'Karyawan', 'pesantren' => 'Santri', 'school' => 'Siswa'][$prefix] ?? 'Anggota';
@endphp
@extends('layouts.tenant')
@section('title', 'Dashboard')
@section('nav')
  <a href="{{ url('/' . $prefix . '/dashboard') }}" class="text-blue-600">Dashboard</a>
  <a href="{{ url('/' . $prefix . '/attendances') }}">Absensi</a>
  <a href="{{ url('/' . $prefix . '/employees') }}">{{ $istilah }}</a>
@endsection
@section('content')
<h1 class="text-xl font-extrabold text-[#102b69]">Dashboard</h1>
<div class="mt-5 grid grid-cols-2 gap-4 md:grid-cols-4">
  <div class="rounded-xl bg-white p-5 shadow-sm">
    <p class="text-xs font-bold text-gray-400">Total {{ $istilah }}</p>
    <p class="mt-1 text-2xl font-extrabold text-[#102b69]">{{ $totalEmployees }}</p>
  </div>
  <div class="rounded-xl bg-white p-5 shadow-sm">
    <p class="text-xs font-bold text-gray-400">Hadir Hari Ini</p>
    <p class="mt-1 text-2xl font-extrabold text-green-600">{{ $todayPresent }}</p>
  </div>
  <div class="rounded-xl bg-white p-5 shadow-sm">
    <p class="text-xs font-bold text-gray-400">Terlambat</p>
    <p class="mt-1 text-2xl font-extrabold text-yellow-600">{{ $todayLate }}</p>
  </div>
  <div class="rounded-xl bg-white p-5 shadow-sm">
    <p class="text-xs font-bold text-gray-400">Izin Pending</p>
    <p class="mt-1 text-2xl font-extrabold text-blue-600">{{ $todayPermission }}</p>
  </div>
</div>

<div class="mt-6 grid gap-6 md:grid-cols-2">
  <div class="rounded-xl bg-white p-5 shadow-sm">
    <p class="mb-3 text-sm font-bold text-gray-500">Absensi Hari Ini</p>
    <div class="space-y-2">
      @forelse ($todayAttendanceList as $a)
        <div class="flex justify-between border-b py-2 text-sm">
          <span>{{ $a->user->name ?? '-' }}</span>
          <span class="text-gray-400">{{ $a->time_in ?? '-' }} · {{ ucfirst($a->status) }}</span>
        </div>
      @empty
        <p class="text-sm text-gray-400">Belum ada absensi hari ini.</p>
      @endforelse
    </div>
  </div>

  <div class="rounded-xl bg-white p-5 shadow-sm">
    <p class="mb-3 text-sm font-bold text-gray-500">Pengajuan Izin Terbaru</p>
    <div class="space-y-2">
      @forelse ($permissionList as $p)
        <div class="flex justify-between border-b py-2 text-sm">
          <span>{{ $p->user->name ?? '-' }}</span>
          <span class="text-gray-400">{{ optional($p->date_permission)->format('d M') }} · {{ $p->is_approved ? 'Disetujui' : 'Menunggu' }}</span>
        </div>
      @empty
        <p class="text-sm text-gray-400">Belum ada pengajuan izin.</p>
      @endforelse
    </div>
  </div>
</div>
@endsection

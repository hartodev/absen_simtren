@extends('layouts.tenant')
@section('title', 'Detail Absensi')
@section('nav')
  <a href="{{ url('/' . request()->segment(1) . '/dashboard') }}">Dashboard</a>
  <a href="{{ url('/' . request()->segment(1) . '/attendances') }}" class="text-blue-600">Absensi</a>
@endsection
@section('content')
<h1 class="text-xl font-extrabold text-[#102b69]">Detail Absensi</h1>
<div class="mt-5 max-w-lg rounded-xl bg-white p-6 shadow-sm">
  <dl class="space-y-3 text-sm">
    <div class="flex justify-between"><dt class="text-gray-400">Nama</dt><dd class="font-bold">{{ $attendance->user->name ?? '-' }}</dd></div>
    <div class="flex justify-between"><dt class="text-gray-400">Tanggal</dt><dd>{{ optional($attendance->date)->format('d M Y') }}</dd></div>
    <div class="flex justify-between"><dt class="text-gray-400">Jam Masuk</dt><dd>{{ $attendance->time_in ?? '-' }}</dd></div>
    <div class="flex justify-between"><dt class="text-gray-400">Jam Pulang</dt><dd>{{ $attendance->time_out ?? '-' }}</dd></div>
    <div class="flex justify-between"><dt class="text-gray-400">Terlambat</dt><dd>{{ $attendance->late_minutes }} menit</dd></div>
    <div class="flex justify-between"><dt class="text-gray-400">Status</dt><dd>{{ ucfirst($attendance->status) }}</dd></div>
    <div class="flex justify-between"><dt class="text-gray-400">Dicatat oleh</dt><dd>{{ $attendance->markedBy->name ?? 'Sistem' }}</dd></div>
  </dl>
  <a href="{{ url('/' . request()->segment(1) . '/attendances') }}" class="mt-5 inline-block text-sm font-bold text-blue-600">&larr; Kembali</a>
</div>
@endsection

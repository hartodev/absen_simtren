@extends('layouts.tenant')
@section('title', 'Dashboard')
@section('nav')
  <a href="{{ url('/' . request()->segment(1) . '/dashboard') }}" class="text-blue-600">Dashboard</a>
  <a href="{{ url('/' . request()->segment(1) . '/attendance') }}">Absensi</a>
@endsection
@section('content')
<h1 class="text-xl font-extrabold text-[#102b69]">Halo, {{ $dashboard['user']->name }}</h1>
<div class="mt-5 grid grid-cols-2 gap-4 md:grid-cols-4">
  <div class="rounded-xl bg-white p-5 shadow-sm">
    <p class="text-xs font-bold text-gray-400">Hadir Bulan Ini</p>
    <p class="mt-1 text-2xl font-extrabold text-[#102b69]">{{ $dashboard['hadir'] }}</p>
  </div>
  <div class="rounded-xl bg-white p-5 shadow-sm">
    <p class="text-xs font-bold text-gray-400">Terlambat</p>
    <p class="mt-1 text-2xl font-extrabold text-yellow-600">{{ $dashboard['terlambat'] }}</p>
  </div>
  <div class="rounded-xl bg-white p-5 shadow-sm">
    <p class="text-xs font-bold text-gray-400">Alpha</p>
    <p class="mt-1 text-2xl font-extrabold text-red-600">{{ $dashboard['alpha'] }}</p>
  </div>
  <div class="rounded-xl bg-white p-5 shadow-sm">
    <p class="text-xs font-bold text-gray-400">Izin Pending</p>
    <p class="mt-1 text-2xl font-extrabold text-blue-600">{{ $dashboard['izin_pending'] }}</p>
  </div>
</div>
<a href="{{ url('/' . request()->segment(1) . '/attendance') }}" class="mt-6 inline-block rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white">Lihat Detail Absensi</a>
@endsection

@extends('layouts.superadmin')
@section('title', 'Dashboard Superadmin')
@section('content')
<h1 class="text-xl font-extrabold text-[#102b69]">Dashboard Superadmin</h1>
<div class="mt-5 grid grid-cols-2 gap-4 md:grid-cols-4">
  <div class="rounded-xl bg-white p-5 shadow-sm"><p class="text-xs font-bold text-gray-400">Total Organisasi</p><p class="mt-1 text-2xl font-extrabold">{{ $totalTenants }}</p></div>
  <div class="rounded-xl bg-white p-5 shadow-sm"><p class="text-xs font-bold text-gray-400">Aktif</p><p class="mt-1 text-2xl font-extrabold text-green-600">{{ $totalAktif }}</p></div>
  <div class="rounded-xl bg-white p-5 shadow-sm"><p class="text-xs font-bold text-gray-400">Pending</p><p class="mt-1 text-2xl font-extrabold text-yellow-600">{{ $totalPending }}</p></div>
  <div class="rounded-xl bg-white p-5 shadow-sm"><p class="text-xs font-bold text-gray-400">Total User</p><p class="mt-1 text-2xl font-extrabold">{{ $totalUsers }}</p></div>
</div>
<div class="mt-6 rounded-xl bg-white p-5 shadow-sm">
  <p class="text-sm font-bold text-gray-500 mb-3">Organisasi per Tipe</p>
  @foreach ($byType as $type => $jumlah)
    <div class="flex justify-between border-b py-2 text-sm"><span>{{ ucfirst($type) }}</span><span class="font-bold">{{ $jumlah }}</span></div>
  @endforeach
</div>
<a href="{{ route('superadmin.tenants.index') }}" class="mt-6 inline-block rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white">Kelola Organisasi</a>
@endsection

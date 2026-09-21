@extends('layouts.tenant')

@section('title', 'Dashboard')

@section('nav')
  <a href="{{ route('tenant.dashboard') }}" class="text-[#2563eb]">Dashboard</a>
  <a href="{{ route('tenant.siswa.index') }}">Siswa</a>
  <a href="{{ route('tenant.kelas.index') }}">Kelas</a>
  <a href="{{ route('tenant.presensi.index') }}">Presensi</a>
@endsection

@php
    $tipe = config('tenant_types.' . $tenant->tipe_lembaga, config('tenant_types.company'));
@endphp

@section('content')

<div class="rounded-2xl p-6 text-white shadow-lg" style="background: var(--tenant-color)">
    <div class="flex items-center gap-2">
        <i data-lucide="{{ $tipe['icon'] }}" style="width:18px;height:18px"></i>
        <span class="text-xs font-semibold uppercase tracking-wide text-white/70">{{ $tipe['label'] }}</span>
    </div>

    <h1 class="mt-1 text-2xl font-extrabold">Selamat datang, {{ $tenant->nama_lembaga }}</h1>

    @if ($tenant->deskripsi)
    <p class="mt-2 max-w-2xl text-sm text-white/85">{{ $tenant->deskripsi }}</p>
    @endif
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-3">
    <div class="rounded-xl bg-white p-5 shadow-sm">
        <p class="text-xs font-semibold text-[#71839b]">Total {{ $tipe['istilah_anggota'] }}</p>
        <p class="mt-1 text-2xl font-extrabold text-[#102b69]">{{ $totalAnggota ?? 0 }}</p>
    </div>

    <div class="rounded-xl bg-white p-5 shadow-sm">
        <p class="text-xs font-semibold text-[#71839b]">{{ $tipe['istilah_hadir'] }}</p>
        <p class="mt-1 text-2xl font-extrabold" style="color: var(--tenant-color)">{{ $hadirHariIni ?? 0 }}</p>
    </div>

    <div class="rounded-xl bg-white p-5 shadow-sm">
        <p class="text-xs font-semibold text-[#71839b]">Tidak Hadir</p>
        <p class="mt-1 text-2xl font-extrabold text-[#b34040]">{{ $tidakHadir ?? 0 }}</p>
    </div>
</div>

<div class="mt-6 rounded-xl bg-white p-5 shadow-sm">
    <p class="text-sm font-bold text-[#102b69]">{{ $tipe['istilah_absen'] }}</p>
    <p class="mt-1 text-xs text-[#71839b]">
        Menu dan fitur tambahan bisa ditaruh di sini sesuai tipe lembaga.
    </p>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
</script>
@endpush

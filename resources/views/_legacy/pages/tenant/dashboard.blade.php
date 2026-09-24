@extends('layouts.company')

@section('title', 'Dashboard')

@php
    $tipe = config('tenant_types.' . $tenant->tipe_lembaga, config('tenant_types.company'));
    $isCompany = $tenant->tipe_lembaga === 'company';
@endphp

@section('nav')
  <a href="{{ route('tenant.dashboard') }}" class="text-[#2563eb]">Dashboard</a>
  @if ($isCompany)
    <a href="{{ route('tenant.karyawan.index') }}">Karyawan</a>
    <a href="{{ route('tenant.absensi-karyawan.index') }}">Absensi</a>
    <a href="{{ route('tenant.absensi-karyawan.absen') }}">Absen Sekarang</a>
  @else
    <a href="{{ route('tenant.siswa.index') }}">Siswa</a>
    <a href="{{ route('tenant.kelas.index') }}">Kelas</a>
    <a href="{{ route('tenant.presensi.index') }}">Presensi</a>
  @endif
@endsection

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

@if ($isCompany)
<div class="mt-6 rounded-xl bg-white p-5 shadow-sm">
    <div class="flex items-center justify-between">
        <p class="text-sm font-bold text-[#102b69]">{{ $tipe['istilah_absen'] }} Hari Ini</p>
        <a href="{{ route('tenant.absensi-karyawan.absen') }}" class="button-lift rounded-lg bg-[#2563eb] px-3 py-1.5 text-xs font-bold text-white">+ Absen Sekarang</a>
    </div>

    <div class="mt-3 overflow-hidden rounded-lg border border-[#eef2f7]">
        <table class="w-full text-sm">
            <thead class="bg-[#f6f8fb] text-left text-xs font-bold uppercase text-[#71839b]">
                <tr>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Masuk</th>
                    <th class="px-4 py-2">Pulang</th>
                    <th class="px-4 py-2">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#eef2f7]">
                @forelse (($absensiTerbaru ?? []) as $a)
                    <tr>
                        <td class="px-4 py-2 font-semibold text-[#102b69]">{{ $a->karyawan->nama ?? '-' }}</td>
                        <td class="px-4 py-2 text-[#71839b]">{{ $a->jam_masuk ? \Illuminate\Support\Carbon::parse($a->jam_masuk)->format('H:i') : '-' }}</td>
                        <td class="px-4 py-2 text-[#71839b]">{{ $a->jam_pulang ? \Illuminate\Support\Carbon::parse($a->jam_pulang)->format('H:i') : '-' }}</td>
                        <td class="px-4 py-2 text-[#71839b] capitalize">{{ $a->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-4 text-center text-[#8192aa]">Belum ada absensi hari ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@else
<div class="mt-6 rounded-xl bg-white p-5 shadow-sm">
    <p class="text-sm font-bold text-[#102b69]">{{ $tipe['istilah_absen'] }}</p>
    <p class="mt-1 text-xs text-[#71839b]">
        Menu dan fitur tambahan bisa ditaruh di sini sesuai tipe lembaga.
    </p>
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
</script>
@endpush

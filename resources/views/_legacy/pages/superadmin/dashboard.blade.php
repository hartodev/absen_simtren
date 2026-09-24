@extends('layouts.superadmin')

@section('title', 'Kelola Tenant')

@section('content')

<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-extrabold text-[#102b69]">Kelola Tenant</h1>
        <p class="mt-1 text-sm text-[#71839b]">Semua lembaga yang terdaftar di Smart Absen.</p>
    </div>
</div>

{{-- Stat cards --}}
<div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <a href="{{ route('superadmin.dashboard') }}"
       class="rounded-xl bg-white p-5 shadow-sm {{ !$status ? 'ring-2 ring-[#102b69]' : '' }}">
        <p class="text-xs font-semibold text-[#71839b]">Total Tenant</p>
        <p class="mt-1 text-2xl font-extrabold text-[#102b69]">{{ $stats['total'] }}</p>
    </a>
    <a href="{{ route('superadmin.dashboard', ['status' => 'pending']) }}"
       class="rounded-xl bg-white p-5 shadow-sm {{ $status === 'pending' ? 'ring-2 ring-[#c98a12]' : '' }}">
        <p class="text-xs font-semibold text-[#71839b]">Menunggu Persetujuan</p>
        <p class="mt-1 text-2xl font-extrabold text-[#c98a12]">{{ $stats['pending'] }}</p>
    </a>
    <a href="{{ route('superadmin.dashboard', ['status' => 'aktif']) }}"
       class="rounded-xl bg-white p-5 shadow-sm {{ $status === 'aktif' ? 'ring-2 ring-[#198754]' : '' }}">
        <p class="text-xs font-semibold text-[#71839b]">Aktif</p>
        <p class="mt-1 text-2xl font-extrabold text-[#198754]">{{ $stats['aktif'] }}</p>
    </a>
    <a href="{{ route('superadmin.dashboard', ['status' => 'nonaktif']) }}"
       class="rounded-xl bg-white p-5 shadow-sm {{ $status === 'nonaktif' ? 'ring-2 ring-[#b34040]' : '' }}">
        <p class="text-xs font-semibold text-[#71839b]">Nonaktif</p>
        <p class="mt-1 text-2xl font-extrabold text-[#b34040]">{{ $stats['nonaktif'] }}</p>
    </a>
</div>

{{-- Search --}}
<form method="GET" action="{{ route('superadmin.dashboard') }}" class="mt-6 flex gap-2">
    @if($status)
        <input type="hidden" name="status" value="{{ $status }}">
    @endif
    <div class="relative flex-1">
        <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[#a2b1c6]" style="width:15px;height:15px"></i>
        <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari nama lembaga atau subdomain..."
               class="h-11 w-full rounded-lg border border-[#dce6f3] pl-9 pr-3 text-sm">
    </div>
    <button type="submit" class="rounded-lg bg-[#102b69] px-4 text-sm font-bold text-white">Cari</button>
</form>

{{-- Tenant table --}}
<div class="mt-6 overflow-hidden rounded-xl bg-white shadow-sm">
    <table class="w-full text-left text-sm">
        <thead>
            <tr class="border-b border-[#eef2f7] text-xs font-bold uppercase tracking-wide text-[#8192aa]">
                <th class="px-5 py-3">Lembaga</th>
                <th class="px-5 py-3">Subdomain</th>
                <th class="px-5 py-3">Paket</th>
                <th class="px-5 py-3">Pengguna</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tenants as $tenant)
                <tr class="border-b border-[#f3f6fa] last:border-0">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            @if ($tenant->logo)
                                <img src="{{ asset('storage/' . $tenant->logo) }}" class="h-9 w-9 rounded-lg object-cover" alt="">
                            @else
                                <span class="flex h-9 w-9 items-center justify-center rounded-lg text-xs font-bold text-white"
                                      style="background: {{ $tenant->warna_tema ?? '#102b69' }}">
                                    {{ \Illuminate\Support\Str::of($tenant->nama_lembaga)->explode(' ')->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('') }}
                                </span>
                            @endif
                            <div>
                                <p class="font-bold text-[#102b69]">{{ $tenant->nama_lembaga }}</p>
                                <p class="text-xs text-[#8192aa]">{{ config('tenant_types.' . $tenant->tipe_lembaga . '.label', $tenant->tipe_lembaga) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-[#405978]">{{ $tenant->subdomain }}.{{ config('app.main_domain') }}</td>
                    <td class="px-5 py-4">
                        <span class="rounded-full bg-[#eef2f7] px-2.5 py-1 text-xs font-bold capitalize text-[#405978]">{{ $tenant->paket }}</span>
                    </td>
                    <td class="px-5 py-4 text-[#405978]">{{ $tenant->users_count }}</td>
                    <td class="px-5 py-4">
                        @if ($tenant->status === 'pending')
                            <span class="rounded-full bg-[#fdf2df] px-2.5 py-1 text-xs font-bold text-[#c98a12]">Menunggu</span>
                        @elseif ($tenant->status === 'aktif')
                            <span class="rounded-full bg-[#e0f4ea] px-2.5 py-1 text-xs font-bold text-[#198754]">Aktif</span>
                        @else
                            <span class="rounded-full bg-[#fff1f1] px-2.5 py-1 text-xs font-bold text-[#b34040]">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-2">
                            @if ($tenant->status !== 'aktif')
                                <form method="POST" action="{{ route('superadmin.tenants.approve', $tenant) }}">
                                    @csrf
                                    <button type="submit" class="rounded-lg bg-[#e0f4ea] px-3 py-1.5 text-xs font-bold text-[#198754] hover:bg-[#cdeddb]">
                                        Aktifkan
                                    </button>
                                </form>
                            @endif
                            @if ($tenant->status !== 'nonaktif')
                                <form method="POST" action="{{ route('superadmin.tenants.nonaktifkan', $tenant) }}">
                                    @csrf
                                    <button type="submit" class="rounded-lg bg-[#fdf2df] px-3 py-1.5 text-xs font-bold text-[#c98a12] hover:bg-[#fbe7c2]">
                                        Nonaktifkan
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('superadmin.tenants.destroy', $tenant) }}"
                                  onsubmit="return confirm('Hapus tenant &quot;{{ $tenant->nama_lembaga }}&quot;? Semua data terkait akan ikut terhapus.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg bg-[#fff1f1] px-3 py-1.5 text-xs font-bold text-[#b34040] hover:bg-[#ffe1e1]">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-10 text-center text-sm text-[#8192aa]">
                        Belum ada tenant yang cocok dengan filter ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">
    {{ $tenants->links() }}
</div>

@endsection

@extends('layouts.admin')
@section('title', $theme === 'green' ? 'Profil & Akun' : 'Profil')
@section('width', 'max-w-3xl')

@section('content')
<div class="card flex items-center gap-4 p-6">
    <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full text-xl font-bold text-white" style="background: var(--brand)">
        {{ strtoupper(mb_substr($user->name, 0, 2)) }}
    </span>
    <div class="min-w-0">
        <p class="truncate text-lg font-bold text-slate-800">{{ $user->name }}</p>
        <p class="truncate text-sm text-slate-500">{{ $user->email }}</p>
        <p class="mt-1 text-xs font-semibold" style="color: var(--brand)">
            {{ ['ustadz' => 'Ustadz / Admin', 'teacher' => 'Admin Sekolah'][$user->role] ?? ucfirst($user->role) }} · {{ $company->name }}
        </p>
    </div>
</div>

@if ($theme === 'green')
    <div class="card mt-4 overflow-hidden">
        @foreach ([
            ['Pengaturan Absensi', 'settings',  route($rp.'attendances.settings')],
            ['Data Santri',        'users',     route($rp.'employees.index')],
            ["Rekap Mutaba'ah",   'book-open', route($rp.'mutabaah.index')],
            ['Ekspor Data Santri', 'download',  route($rp.'employees.export')],
        ] as [$label, $icon, $url])
            <a href="{{ $url }}" class="flex items-center gap-3 border-b border-slate-100 px-5 py-3.5 last:border-0 hover:bg-slate-50">
                <i data-lucide="{{ $icon }}" class="h-5 w-5 text-slate-400"></i>
                <span class="flex-1 text-sm font-semibold text-slate-700">{{ $label }}</span>
                <i data-lucide="chevron-right" class="h-4 w-4 text-slate-300"></i>
            </a>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('tenant.logout') }}" class="mt-4">
    @csrf
    <button type="submit" class="btn btn-danger"><i data-lucide="log-out" class="h-4 w-4"></i> Keluar</button>
</form>
@endsection

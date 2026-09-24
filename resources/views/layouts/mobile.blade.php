{{--
    Layout mobile untuk halaman admin pesantren & sekolah.
    Variabel dari TenantAdminController::view():
      $theme  = 'green' (TPQ) | 'blue' (sekolah umum / pondok pesantren)
      $rp     = prefix nama route: 'school.' | 'pesantren.'
      $company
--}}
@php
    $isGreen = $theme === 'green';
    $brand   = $isGreen ? '#106f57' : '#2145a8';
    $bg      = $isGreen ? '#eff2f6' : '#f6f7fb';

    $nav = $isGreen
        ? [
            ['Beranda',   'home',           $rp.'dashboard',        [$rp.'dashboard']],
            ['Kehadiran', 'fingerprint',    $rp.'attendances.index',[$rp.'attendances.*']],
            ["Mutaba'ah", 'book-open-check',$rp.'mutabaah.index',   [$rp.'mutabaah.*']],
            ['Pesan',     'message-square', [$rp.'modules.show', 'pesan'], []],
            ['Santri',    'user',           $rp.'employees.index',  [$rp.'employees.*']],
            ['Lainnya',   'layout-grid',    $rp.'profile',          [$rp.'profile']],
          ]
        : [
            ['Beranda',    'layout-dashboard', $rp.'dashboard',                [$rp.'dashboard', $rp.'students.*', $rp.'classes.*', $rp.'devices.*', $rp.'student-attendances.*', $rp.'boarding.*', $rp.'modules.*']],
            ['Izin Siswa', 'clipboard-list',   $rp.'student-permissions.index',[$rp.'student-permissions.*']],
            ['Profil',     'user',             $rp.'profile',                  [$rp.'profile']],
          ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'Beranda') - {{ $company->name ?? 'Smart Absen' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@0.294.0/dist/umd/lucide.min.js"></script>
    <style>
        :root { --brand: {{ $brand }}; }
        body { background: #dfe3ea; font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }
        .app { background: {{ $bg }}; }
        .input { width: 100%; border-radius: .75rem; border: 1px solid #d5dbe5; background: #fff; padding: .7rem .85rem; font-size: .9rem; }
        .input:focus { outline: 2px solid var(--brand); outline-offset: -1px; border-color: transparent; }
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:.4rem; border-radius:.75rem; padding:.65rem 1rem; font-size:.85rem; font-weight:700; }
        .btn-brand { background: var(--brand); color:#fff; }
        .btn-line { border:1px solid #d5dbe5; background:#fff; color:#475569; }
        .btn-danger { background:#fee2e2; color:#b91c1c; }
    </style>
    @stack('styles')
</head>
<body>
<div class="app relative mx-auto min-h-screen max-w-md pb-24 shadow-xl">

    @hasSection('header')
        @yield('header')
    @else
        <header class="sticky top-0 z-30 flex h-14 items-center gap-2 px-3 text-white" style="background: var(--brand)">
            @hasSection('back')
                <a href="@yield('back')" class="flex h-9 w-9 items-center justify-center rounded-full hover:bg-white/10">
                    <i data-lucide="arrow-left" class="h-5 w-5"></i>
                </a>
            @endif
            <h1 class="flex-1 truncate text-base font-bold {{ View::hasSection('back') ? '' : 'text-center' }}">@yield('title', 'Beranda')</h1>
            <div class="flex h-9 w-9 items-center justify-center">@yield('header_action')</div>
        </header>
    @endif

    <main class="px-4 py-4">
        @if (session('success'))
            <div class="mb-3 flex items-center gap-2 rounded-xl bg-emerald-50 px-3 py-2.5 text-sm font-semibold text-emerald-700">
                <i data-lucide="check-circle-2" class="h-4 w-4 shrink-0"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-3 flex items-center gap-2 rounded-xl bg-red-50 px-3 py-2.5 text-sm font-semibold text-red-700">
                <i data-lucide="alert-circle" class="h-4 w-4 shrink-0"></i> {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-3 rounded-xl bg-red-50 px-3 py-2.5 text-sm text-red-700">
                <ul class="list-disc pl-4">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Bottom navigation --}}
    <nav class="fixed bottom-0 left-1/2 z-40 w-full max-w-md -translate-x-1/2 border-t border-slate-200 bg-white/95 backdrop-blur"
         style="padding-bottom: env(safe-area-inset-bottom)">
        <ul class="flex">
            @foreach ($nav as [$label, $icon, $target, $patterns])
                @php
                    $url = is_array($target) ? route($target[0], $target[1]) : route($target);
                    $active = $patterns ? request()->routeIs(...$patterns)
                        : (request()->routeIs($rp.'modules.show') && request()->route('slug') === ($target[1] ?? null));
                @endphp
                <li class="flex-1">
                    <a href="{{ $url }}" class="flex flex-col items-center gap-0.5 py-2.5 text-[11px] font-semibold"
                       style="color: {{ $active ? $brand : '#8a94a6' }}">
                        <i data-lucide="{{ $icon }}" class="h-5 w-5"></i>{{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</div>

@stack('scripts')
<script>lucide.createIcons();</script>
</body>
</html>

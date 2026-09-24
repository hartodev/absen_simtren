{{--
    Layout WEB untuk halaman admin sekolah & pesantren/TPQ (sidebar + topbar, responsif).
    Di layar kecil sidebar jadi drawer yang dibuka lewat tombol menu.

    Variabel dari TenantAdminController::view():
      $theme   = 'green' (TPQ) | 'blue' (sekolah / pondok pesantren)
      $rp      = prefix nama route: 'school.' | 'pesantren.'
      $company
    Section: title, subtitle (opsional), actions (tombol di topbar), width (opsional), content
--}}
@php
    $isGreen = $theme === 'green';
    $brand   = $isGreen ? '#106f57' : '#2145a8';
    $soft    = $isGreen ? '#e6f3ee' : '#e8eefc';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Beranda') - {{ $company->name ?? 'Smart Absen' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@0.294.0/dist/umd/lucide.min.js"></script>
    <style>
        :root { --brand: {{ $brand }}; --brand-soft: {{ $soft }}; }
        body { background: #f4f6fa; font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; color: #1e293b; }

        .card    { background:#fff; border-radius:1rem; border:1px solid #e6eaf1; box-shadow:0 1px 2px rgba(15,23,42,.04); }
        .input   { width:100%; border-radius:.6rem; border:1px solid #d5dbe5; background:#fff; padding:.55rem .8rem; font-size:.875rem; }
        .input:focus { outline:2px solid var(--brand); outline-offset:-1px; border-color:transparent; }
        .label   { display:block; margin-bottom:.3rem; font-size:.75rem; font-weight:600; color:#64748b; }
        .btn     { display:inline-flex; align-items:center; justify-content:center; gap:.4rem; border-radius:.6rem; padding:.5rem .9rem; font-size:.8125rem; font-weight:600; white-space:nowrap; transition:filter .15s; }
        .btn:hover { filter:brightness(.95); }
        .btn-brand  { background:var(--brand); color:#fff; }
        .btn-line   { border:1px solid #d5dbe5; background:#fff; color:#475569; }
        .btn-danger { background:#fee2e2; color:#b91c1c; }
        .btn-sm  { padding:.35rem .65rem; font-size:.75rem; }

        .tbl { width:100%; font-size:.875rem; }
        .tbl th { padding:.65rem 1rem; text-align:left; font-size:.6875rem; font-weight:700; letter-spacing:.04em; text-transform:uppercase; color:#64748b; background:#f8fafc; border-bottom:1px solid #e6eaf1; white-space:nowrap; }
        .tbl td { padding:.7rem 1rem; border-bottom:1px solid #eef1f6; vertical-align:middle; }
        .tbl tbody tr:last-child td { border-bottom:0; }
        .tbl tbody tr:hover { background:#fafbfd; }

        .badge { display:inline-flex; align-items:center; border-radius:999px; padding:.15rem .6rem; font-size:.6875rem; font-weight:700; }
        .empty { padding:3rem 1rem; text-align:center; font-size:.875rem; color:#94a3b8; }
    </style>
    @stack('styles')
</head>
<body>
    @include('layouts.partials.admin-sidebar')
    <div id="overlay" class="fixed inset-0 z-30 hidden bg-slate-900/40 lg:hidden"></div>

    <div class="lg:pl-64">
        <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-slate-200 bg-white/90 px-4 backdrop-blur lg:px-8">
            <button id="menu-btn" type="button" class="flex h-9 w-9 items-center justify-center rounded-lg hover:bg-slate-100 lg:hidden" aria-label="Menu">
                <i data-lucide="menu" class="h-5 w-5"></i>
            </button>
            <div class="min-w-0 flex-1">
                <h1 class="truncate text-lg font-bold leading-tight text-slate-800">@yield('title', 'Beranda')</h1>
                @hasSection('subtitle')
                    <p class="truncate text-xs text-slate-500">@yield('subtitle')</p>
                @endif
            </div>
            <div class="flex items-center gap-2">@yield('actions')</div>
            <div class="ml-1 hidden items-center gap-2 border-l border-slate-200 pl-4 sm:flex">
                <span class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold text-white" style="background:var(--brand)">
                    {{ strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}
                </span>
                <div class="hidden leading-tight md:block">
                    <p class="max-w-[10rem] truncate text-sm font-semibold">{{ auth()->user()->name }}</p>
                    <p class="max-w-[10rem] truncate text-[11px] text-slate-500">{{ $company->name }}</p>
                </div>
            </div>
        </header>

        <main class="mx-auto w-full px-4 py-6 lg:px-8 @yield('width', 'max-w-6xl')">
            @include('layouts.partials.admin-alerts')
            @yield('content')
        </main>
    </div>

    @stack('scripts')
    <script>
        lucide.createIcons();
        (function () {
            const sb = document.getElementById('sidebar'), ov = document.getElementById('overlay');
            const toggle = open => { sb.classList.toggle('-translate-x-full', !open); ov.classList.toggle('hidden', !open); };
            document.getElementById('menu-btn').addEventListener('click', () => toggle(true));
            ov.addEventListener('click', () => toggle(false));
        })();
    </script>
</body>
</html>

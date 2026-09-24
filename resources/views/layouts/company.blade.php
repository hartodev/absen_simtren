<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $tenant->name ?? 'Dashboard') - Smart Absen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>

<body class="min-h-screen bg-[#f3f7fc]">

    <header class="sticky top-0 z-40 border-b border-black/5 bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-5">
            <div class="flex items-center gap-3">
                <span
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#102b69] text-sm font-bold text-white">
                    {{ \Illuminate\Support\Str::of($tenant->name ?? 'T')->explode(' ')->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('') }}
                </span>
                <div>
                    <p class="text-sm font-bold leading-tight text-[#102b69]">{{ $tenant->name ?? '' }}</p>
                    <p class="text-xs text-[#8192aa]">{{ ucfirst($tenant->type ?? '') }}</p>
                </div>
            </div>

            <nav class="hidden items-center gap-1 text-sm font-semibold text-[#71839b] md:flex">
                @php
                    $__prefix = request()->segment(1);
                    $__anggotaLabel = ['company' => 'Karyawan', 'pesantren' => 'Santri', 'school' => 'Siswa'][$__prefix] ?? 'Anggota';
                    $__navLinks = [
                        "/{$__prefix}/dashboard" => 'Dashboard',
                        "/{$__prefix}/attendances" => 'Absensi',
                        "/{$__prefix}/employees" => $__anggotaLabel,
                    ];
                @endphp
                @foreach($__navLinks as $__path => $__label)
                <a href="{{ url($__path) }}"
                    class="nav-link px-3 py-2 rounded-lg {{ request()->is(ltrim($__path, '/') . '*') ? 'nav-active bg-blue-50' : '' }}">
                    {{ $__label }}
                </a>
                @endforeach
                @yield('nav')
            </nav>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center gap-1.5 rounded-lg border border-[#e3e8f0] px-3 py-2 text-xs font-bold text-[#8192aa] transition-colors hover:border-[#f0c4c4] hover:text-[#b34040]">
                    <i data-lucide="log-out" style="width:13px;height:13px"></i> Keluar
                </button>
            </form>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-5 py-8">
        @if (session('success'))
        <div
            class="mb-5 flex items-center gap-2 rounded-lg bg-[#e0f4ea] px-4 py-3 text-sm font-semibold text-[#198754]">
            <i data-lucide="check-circle-2" style="width:16px;height:16px"></i> {{ session('success') }}
        </div>
        @endif
        @if (session('error'))
        <div
            class="mb-5 flex items-center gap-2 rounded-lg bg-[#fff1f1] px-4 py-3 text-sm font-semibold text-[#b34040]">
            <i data-lucide="alert-circle" style="width:16px;height:16px"></i> {{ session('error') }}
        </div>
        @endif
        @yield('content')
    </main>

    <script>
    lucide.createIcons();
    </script>
</body>

</html>
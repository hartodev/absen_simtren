<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard HR') — {{ auth()->user()?->company?->name ?? 'HR Attendance' }}</title>

    {{-- Ganti dengan @vite(['resources/css/app.css']) begitu Tailwind sudah
         dicompile lewat build pipeline proyek ini. CDN dipakai di scaffold
         ini supaya file bisa langsung dijalankan. --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Manrope', 'sans-serif']
                },
                colors: {
                    canvas: '#F6F7FB',
                    ink: '#16213E',
                    brand: {
                        DEFAULT: '#2F4B9E',
                        dark: '#1D2B64',
                        light: '#E9ECFA',
                    },
                    good: {
                        DEFAULT: '#1F9D6B',
                        bg: '#E6F7EF'
                    },
                    warn: {
                        DEFAULT: '#C77D12',
                        bg: '#FCF1DE'
                    },
                    bad: {
                        DEFAULT: '#C43D4B',
                        bg: '#FBE7E9'
                    },
                },
            },
        },
    };
    </script>
    @stack('styles')
</head>

<body class="bg-canvas font-sans text-ink antialiased">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="hidden lg:flex w-64 shrink-0 flex-col bg-ink text-white">
            <div class="px-6 py-6 border-b border-white/10">
                <p class="text-xs uppercase tracking-wide text-white/50">HR Attendance</p>
                <p class="mt-1 font-semibold leading-snug">{{ auth()->user()?->company?->name ?? '—' }}</p>
            </div>
            <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
                @php
                $nav = [
                ['label' => 'Dashboard', 'route' => 'company.dashboard', 'icon' => '🏠'],
                ['label' => 'Karyawan', 'route' => 'company.employees.index', 'icon' => '👥'],
                ['label' => 'Chat', 'route' => 'company.chat.index', 'icon' => '💬'],
                ['label' => 'Absensi', 'route' => 'company.absensi.index', 'icon' => '🕘'],
                ['label' => 'More', 'route' => 'company.more.index', 'icon' => '⚙️'],
                ];
                @endphp
                @foreach ($nav as $item)
                @php $active = Route::currentRouteName() === $item['route']; @endphp
                <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition
                              {{ $active ? 'bg-brand text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                    <span class="text-base leading-none">{{ $item['icon'] }}</span>
                    <span>{{ $item['label'] }}</span>
                </a>
                @endforeach
            </nav>
            <div class="px-6 py-4 border-t border-white/10 text-xs text-white/50">
                Masuk sebagai {{ auth()->user()?->name }} · HR
            </div>
        </aside>

        {{-- Main --}}
        <div class="flex-1 flex flex-col min-w-0">
            @yield('content')
        </div>
    </div>
    @stack('scripts')
</body>

</html>
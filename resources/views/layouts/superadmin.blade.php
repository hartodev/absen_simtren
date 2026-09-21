<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Superadmin') - Smart Absen</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
  @stack('styles')
</head>
<body class="min-h-screen bg-[#f6f8fb]">

  <div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="hidden w-64 shrink-0 flex-col border-r border-black/5 bg-[#0b1730] text-white md:flex">
      <div class="flex items-center gap-2.5 px-6 py-6">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#f6b51e] text-[#122e70]">
          <i data-lucide="shield-check" style="width:19px;height:19px;stroke-width:2.5"></i>
        </span>
        <div>
          <p class="text-sm font-extrabold leading-tight tracking-[-.03em]">Superadmin</p>
          <p class="text-[11px] leading-tight text-white/50">Smart Absen</p>
        </div>
      </div>

      <nav class="mt-4 flex-1 space-y-1 px-3 text-sm font-semibold">
        <a href="{{ route('superadmin.dashboard') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ request()->routeIs('superadmin.dashboard') || request()->routeIs('superadmin.tenants') ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
          <i data-lucide="building-2" style="width:17px;height:17px"></i>
          Kelola Tenant
        </a>
      </nav>

      <div class="border-t border-white/10 px-3 py-4">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold text-white/60 hover:bg-white/5 hover:text-white">
            <i data-lucide="log-out" style="width:17px;height:17px"></i>
            Keluar
          </button>
        </form>
      </div>
    </aside>

    {{-- Main --}}
    <div class="flex flex-1 flex-col">

      {{-- Topbar (mobile) --}}
      <header class="flex items-center justify-between border-b border-black/5 bg-white px-5 py-4 md:hidden">
        <div class="flex items-center gap-2">
          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#0b1730] text-[#f6b51e]">
            <i data-lucide="shield-check" style="width:16px;height:16px"></i>
          </span>
          <span class="text-sm font-extrabold text-[#102b69]">Superadmin</span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="text-xs font-bold text-[#b34040]">Keluar</button>
        </form>
      </header>

      <main class="mx-auto w-full max-w-6xl flex-1 px-5 py-8">
        @if (session('success'))
          <div class="mb-5 flex items-center gap-2 rounded-lg bg-[#e0f4ea] px-3 py-2.5 text-xs font-semibold text-[#198754]">
            <i data-lucide="check-circle-2" style="width:15px;height:15px"></i>
            {{ session('success') }}
          </div>
        @endif

        @yield('content')
      </main>
    </div>
  </div>

  @stack('scripts')
  <script>
    lucide.createIcons();
  </script>
</body>
</html>

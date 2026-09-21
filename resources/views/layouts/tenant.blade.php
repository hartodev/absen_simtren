<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', $tenant->name ?? 'Dashboard') - Smart Absen</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
@stack('styles')
</head>
<body class="min-h-screen bg-[#f6f8fb]">

<header class="sticky top-0 z-40 border-b border-black/5 bg-white/95 backdrop-blur">
  <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-5">
    <div class="flex items-center gap-3">
      <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#2563eb] text-sm font-bold text-white">
        {{ \Illuminate\Support\Str::of($tenant->name ?? 'T')->explode(' ')->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('') }}
      </span>
      <div>
        <p class="text-sm font-bold text-[#102b69]">{{ $tenant->name ?? '' }}</p>
        <p class="text-xs text-[#8192aa]">{{ ucfirst($tenant->type ?? '') }}</p>
      </div>
    </div>

    <nav class="hidden items-center gap-5 text-sm font-semibold text-[#405978] md:flex">
      @yield('nav')
    </nav>

    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="rounded-lg border border-[#e3e8f0] px-3 py-2 text-xs font-bold text-[#8192aa]">Keluar</button>
    </form>
  </div>
</header>

<main class="mx-auto max-w-6xl px-5 py-8">
  @if (session('success'))
    <div class="mb-5 rounded-lg bg-[#e0f4ea] px-4 py-3 text-sm font-semibold text-[#198754]">{{ session('success') }}</div>
  @endif
  @if (session('error'))
    <div class="mb-5 rounded-lg bg-[#fff1f1] px-4 py-3 text-sm font-semibold text-[#b34040]">{{ session('error') }}</div>
  @endif
  @yield('content')
</main>

</body>
</html>

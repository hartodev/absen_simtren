<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk — {{ ($tenant ?? null) ? $tenant->name : 'Superadmin' }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body style="background:#f3f7fc; min-height:100dvh;">

{{--
  Dipakai untuk DUA jenis login (dibedakan variabel $tenant):
   - Subdomain lembaga  ($tenant ada)  -> "Masuk ke {nama lembaga}"  [langkah 4 alur]
   - Domain utama /superadmin/login ($tenant kosong) -> login superadmin
  Warna tema & logo opsional: dipakai kalau kolom warna_tema / logo ada di companies.
--}}
@php
  $isTenant = (bool) ($tenant ?? null);
  $brand    = $isTenant ? ($tenant->warna_tema ?? '#102b69') : '#102b69';
  $name     = $isTenant ? $tenant->name : 'Smart Absen';
  $mainUrl  = request()->getScheme() . '://' . config('app.main_domain')
              . (in_array(request()->getPort(), [80, 443]) ? '' : ':' . request()->getPort());
@endphp

<main class="flex min-h-[100dvh] items-center justify-center bg-[#f3f7fc] px-4 py-10">
  <div class="grid w-full max-w-4xl overflow-hidden rounded-3xl border border-[#dce6f3] bg-white shadow-[0_24px_70px_rgba(22,60,125,.12)] md:grid-cols-[.85fr_1.15fr]">

    <div class="hidden p-10 text-white md:block" style="background:{{ $brand }}">
      <div class="flex items-center gap-2.5">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#f6b51e] text-[#122e70]"><i data-lucide="{{ $isTenant ? 'building-2' : 'shield-check' }}" style="width:19px;height:19px;stroke-width:2.5"></i></span>
        <span class="text-[17px] font-extrabold tracking-[-.06em] text-white">{{ $name }}</span>
      </div>
      <div class="mt-24">
        <p class="eyebrow text-[#9dbfff]">{{ $isTenant ? 'Selamat datang kembali' : 'Panel pusat' }}</p>
        <h1 class="mt-4 text-4xl font-extrabold leading-tight tracking-[-.05em]">Kehadiran yang tertata, dimulai dari sini.</h1>
        <p class="mt-5 text-sm leading-6 text-blue-100/65">
          {{ $isTenant ? 'Masuk untuk mengelola dashboard ' . $tenant->name . '.' : 'Masuk untuk mengelola seluruh lembaga.' }}
        </p>
      </div>
      <div class="mt-24 flex items-center gap-2 text-xs text-blue-100/50"><i data-lucide="shield-check" style="width:15px;height:15px"></i> Data lembaga Anda terlindungi.</div>
    </div>

    <div class="p-7 sm:p-12">
      <a href="{{ $isTenant ? $mainUrl . '/login' : route('landing') }}" class="mb-10 inline-block text-sm font-bold text-[#8192aa] hover:text-[#1e40af]">
        {{ $isTenant ? 'Bukan lembaga ini? Cari subdomain lain' : 'Kembali ke beranda' }}
      </a>

      <h2 class="mt-8 text-3xl font-extrabold tracking-tight text-[#102b69] md:mt-0">
        {{ $isTenant ? 'Masuk ke ' . $tenant->name : 'Masuk sebagai superadmin' }}
      </h2>
      <p class="mt-3 text-sm leading-6 text-[#71839b]">
        {{ $isTenant ? 'Gunakan email admin lembaga Anda untuk melanjutkan.' : 'Halaman ini khusus superadmin. Pengguna lembaga masuk lewat subdomain lembaganya.' }}
      </p>

      @if (session('success'))
        <div class="mt-5 rounded-lg bg-[#e0f4ea] px-3 py-2 text-xs font-semibold text-[#198754]">{{ session('success') }}</div>
      @endif

      @if ($errors->any())
        <div class="mt-5 rounded-lg bg-[#fff1f1] px-3 py-2 text-xs font-semibold text-[#b34040]">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ $isTenant ? url('/login') : route('superadmin.login.post') }}" class="mt-8 space-y-4">
        @csrf
        <label class="block">
          <span class="mb-2 block text-xs font-bold text-[#405978]">Email</span>
          <input type="email" name="email" required autofocus placeholder="nama@institusi.id" class="form-control h-12" value="{{ old('email') }}">
        </label>
        <label class="block">
          <span class="mb-2 block text-xs font-bold text-[#405978]">Kata sandi</span>
          <input type="password" name="password" required placeholder="Masukkan kata sandi" class="form-control h-12">
        </label>
        <button type="submit" class="button-lift mt-3 w-full rounded-xl py-3.5 text-sm font-bold text-white" style="background:{{ $isTenant ? $brand : '#2563eb' }}">
          Masuk ke dashboard <i data-lucide="arrow-right" style="width:15px;height:15px;margin-left:4px;display:inline"></i>
        </button>
      </form>

      @unless ($isTenant)
        <p class="mt-7 text-center text-xs text-[#8192aa]">
          Bukan superadmin? <a href="{{ route('login.form') }}" class="font-bold text-[#2563eb]">Cari lembaga Anda</a>
        </p>
      @endunless
    </div>
  </div>
</main>

<script>
  lucide.createIcons();
</script>
</body>
</html>

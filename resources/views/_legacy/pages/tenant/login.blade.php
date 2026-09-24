<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk — {{ $tenant->nama_lembaga }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body style="background:#f3f7fc; min-height:100dvh;">

<main class="flex min-h-[100dvh] items-center justify-center bg-[#f3f7fc] px-4 py-10">
  <div class="grid w-full max-w-4xl overflow-hidden rounded-3xl border border-[#dce6f3] bg-white shadow-[0_24px_70px_rgba(22,60,125,.12)] md:grid-cols-[.85fr_1.15fr]">

    <div class="hidden p-10 text-white md:block" style="background:{{ $tenant->warna_tema }}">
      <div class="flex items-center gap-2.5">
        @if($tenant->logo)
          <img src="{{ asset('storage/' . $tenant->logo) }}" class="h-9 w-9 rounded-xl object-cover" alt="{{ $tenant->nama_lembaga }}">
        @else
          <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#f6b51e] text-[#122e70]"><i data-lucide="building-2" style="width:19px;height:19px;stroke-width:2.5"></i></span>
        @endif
        <span class="text-[17px] font-extrabold tracking-[-.06em] text-white">{{ $tenant->nama_lembaga }}</span>
      </div>
      <div class="mt-24">
        <p class="eyebrow text-[#9dbfff]">Selamat datang kembali</p>
        <h1 class="mt-4 text-4xl font-extrabold leading-tight tracking-[-.05em]">Kehadiran yang tertata, dimulai dari sini.</h1>
        <p class="mt-5 text-sm leading-6 text-blue-100/65">Masuk untuk mengelola dashboard {{ $tenant->nama_lembaga }}.</p>
      </div>
      <div class="mt-24 flex items-center gap-2 text-xs text-blue-100/50"><i data-lucide="shield-check" style="width:15px;height:15px"></i> Data lembaga Anda terlindungi.</div>
    </div>

    <div class="p-7 sm:p-12">
      <a href="{{ request()->getScheme() }}://{{ config('app.main_domain') }}{{ in_array(request()->getPort(), [80, 443]) ? '' : ':' . request()->getPort() }}" class="mb-10 inline-block text-sm font-bold text-[#8192aa] hover:text-[#1e40af]">Kembali ke beranda</a>
      <div class="md:hidden mb-6 flex items-center gap-2.5">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl text-white" style="background:{{ $tenant->warna_tema }}"><i data-lucide="building-2" style="width:19px;height:19px;stroke-width:2.5"></i></span>
        <span class="text-[17px] font-extrabold tracking-[-.06em] text-[#102b69]">{{ $tenant->nama_lembaga }}</span>
      </div>
      <h2 class="mt-8 text-3xl font-extrabold tracking-tight text-[#102b69] md:mt-0">Masuk ke {{ $tenant->nama_lembaga }}</h2>
      <p class="mt-3 text-sm leading-6 text-[#71839b]">Gunakan email admin lembaga Anda untuk melanjutkan.</p>

      @if(session('success'))
        <div class="mb-4 rounded-lg bg-[#e0f4ea] px-3 py-2 text-xs font-semibold text-[#198754]">
          {{ session('success') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="mb-4 rounded-lg bg-[#fff1f1] px-3 py-2 text-xs font-semibold text-[#b34040]">
          {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="{{ route('tenant.login.submit') }}" class="mt-8 space-y-4">
        @csrf
        <label class="block">
          <span class="mb-2 block text-xs font-bold text-[#405978]">Email kerja</span>
          <input type="email" name="email" required placeholder="nama@institusi.id" class="form-control h-12" value="{{ old('email') }}">
        </label>
        <label class="block">
          <span class="mb-2 block text-xs font-bold text-[#405978]">Kata sandi</span>
          <input type="password" name="password" required placeholder="Masukkan kata sandi" class="form-control h-12">
        </label>
        <button type="submit" class="button-lift mt-3 w-full rounded-xl py-3.5 text-sm font-bold text-white" style="background:{{ $tenant->warna_tema }}">Masuk ke dashboard <i data-lucide="arrow-right" style="width:15px;height:15px;margin-left:4px;display:inline"></i></button>
      </form>
    </div>
  </div>
</main>

<script>
  lucide.createIcons();
</script>
</body>
</html>

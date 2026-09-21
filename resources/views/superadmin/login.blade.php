<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk — Superadmin</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body style="background:#0b1730; min-height:100dvh;">

<main class="flex min-h-[100dvh] items-center justify-center px-4 py-10">
  <div class="w-full max-w-sm rounded-3xl border border-white/10 bg-white p-8 shadow-[0_24px_70px_rgba(0,0,0,.35)]">

    <div class="flex items-center gap-2.5">
      <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#0b1730] text-[#f6b51e]">
        <i data-lucide="shield-check" style="width:20px;height:20px;stroke-width:2.5"></i>
      </span>
      <div>
        <p class="text-sm font-extrabold leading-tight text-[#102b69]">Superadmin</p>
        <p class="text-[11px] leading-tight text-[#71839b]">Smart Absen</p>
      </div>
    </div>

    <h2 class="mt-8 text-2xl font-extrabold tracking-tight text-[#102b69]">Masuk ke panel superadmin</h2>
    <p class="mt-2 text-sm leading-6 text-[#71839b]">Khusus untuk pengelola pusat Smart Absen.</p>

    @if ($errors->any())
      <div class="mt-5 rounded-lg bg-[#fff1f1] px-3 py-2 text-xs font-semibold text-[#b34040]">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('superadmin.login.submit') }}" class="mt-6 space-y-4">
      @csrf
      <label class="block">
        <span class="mb-2 block text-xs font-bold text-[#405978]">Email</span>
        <input type="email" name="email" required autofocus placeholder="superadmin@smartabsen.id"
               class="form-control h-12 w-full rounded-lg border border-[#dce6f3] px-3 text-sm"
               value="{{ old('email') }}">
      </label>
      <label class="block">
        <span class="mb-2 block text-xs font-bold text-[#405978]">Kata sandi</span>
        <input type="password" name="password" required placeholder="Masukkan kata sandi"
               class="form-control h-12 w-full rounded-lg border border-[#dce6f3] px-3 text-sm">
      </label>
      <button type="submit"
              class="mt-3 w-full rounded-xl bg-[#0b1730] py-3.5 text-sm font-bold text-white hover:bg-[#132348]">
        Masuk <i data-lucide="arrow-right" style="width:15px;height:15px;margin-left:4px;display:inline"></i>
      </button>
    </form>

    <a href="{{ route('login') }}" class="mt-6 block text-center text-xs font-semibold text-[#8192aa] hover:text-[#1e40af]">
      Bukan superadmin? Kembali ke halaman login lembaga
    </a>
  </div>
</main>

<script>
  lucide.createIcons();
</script>
</body>
</html>

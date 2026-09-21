<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lembaga Nonaktif — Smart Absen</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body style="background:#f3f7fc; min-height:100dvh;">
<main class="flex min-h-[100dvh] items-center justify-center bg-[#f3f7fc] px-4 py-10">
  <div class="w-full max-w-md rounded-3xl border border-[#dce6f3] bg-white p-8 text-center shadow-[0_24px_70px_rgba(22,60,125,.12)] sm:p-10">
    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#fff1f1]">
      <i data-lucide="ban" style="width:26px;height:26px;color:#b34040"></i>
    </span>
    <h1 class="mt-6 text-2xl font-extrabold tracking-tight text-[#102b69]">Lembaga sedang nonaktif</h1>
    <p class="mt-3 text-sm leading-6 text-[#71839b]">
      Akses untuk <span class="font-bold text-[#102b69]">{{ $tenant->nama_lembaga }}</span> saat ini dinonaktifkan. Hubungi admin Smart Absen untuk informasi lebih lanjut.
    </p>
    <a href="{{ url('/') }}" class="mt-6 inline-block button-lift rounded-xl bg-[#2563eb] px-6 py-3 text-sm font-bold text-white">Kembali ke beranda</a>
  </div>
</main>
<script>lucide.createIcons();</script>
</body>
</html>

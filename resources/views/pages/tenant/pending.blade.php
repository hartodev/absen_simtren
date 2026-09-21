<!-- <!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Menunggu Aktivasi — Smart Absen</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body style="background:#f3f7fc; min-height:100dvh;">
<main class="flex min-h-[100dvh] items-center justify-center bg-[#f3f7fc] px-4 py-10">
  <div class="w-full max-w-md rounded-3xl border border-[#dce6f3] bg-white p-8 text-center shadow-[0_24px_70px_rgba(22,60,125,.12)] sm:p-10">
    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#fff8e6]">
      <i data-lucide="clock" style="width:26px;height:26px;color:#b8860b"></i>
    </span>
    <h1 class="mt-6 text-2xl font-extrabold tracking-tight text-[#102b69]">Menunggu aktivasi</h1>
    <p class="mt-3 text-sm leading-6 text-[#71839b]">
      Lembaga <span class="font-bold text-[#102b69]">{{ $tenant->nama_lembaga }}</span> belum diaktivasi.
      Cek email admin yang didaftarkan untuk link aktivasi, atau kirim ulang di bawah ini.
    </p>
    <form method="POST" action="{{ route('register.resend') }}" class="mt-6">
      @csrf
      <input type="email" name="email" required placeholder="Email admin lembaga" class="form-control h-12 mb-3">
      <button type="submit" class="button-lift w-full rounded-xl bg-[#2563eb] py-3 text-sm font-bold text-white">Kirim ulang email aktivasi</button>
    </form>
    <a href="{{ url('/') }}" class="mt-5 inline-block text-sm font-bold text-[#8192aa] hover:text-[#1e40af]">Kembali ke beranda</a>
  </div>
</main>
<script>lucide.createIcons();</script>
</body>
</html> -->





/////versi manual
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Aktivasi — Smart Absen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body style="background:#f3f7fc; min-height:100dvh;">
    <main class="flex min-h-[100dvh] items-center justify-center bg-[#f3f7fc] px-4 py-10">
        <div
            class="w-full max-w-md rounded-3xl border border-[#dce6f3] bg-white p-8 text-center shadow-[0_24px_70px_rgba(22,60,125,.12)] sm:p-10">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#fff8e6]">
                <i data-lucide="clock" style="width:26px;height:26px;color:#b8860b"></i>
            </span>
            <h1 class="mt-6 text-2xl font-extrabold tracking-tight text-[#102b69]">Menunggu aktivasi</h1>
            <p class="mt-3 text-sm leading-6 text-[#71839b]">
                Lembaga <span class="font-bold text-[#102b69]">{{ $tenant->nama_lembaga }}</span> belum diaktivasi.
                Cek email admin yang didaftarkan untuk link aktivasi, atau kirim ulang di bawah ini.
            </p>
            <form method="POST" action="{{ route('register.resend') }}" class="mt-6">
                @csrf
                <input type="email" name="email" required placeholder="Email admin lembaga"
                    class="form-control h-12 mb-3">
                <button type="submit"
                    class="button-lift w-full rounded-xl bg-[#2563eb] py-3 text-sm font-bold text-white">Kirim ulang
                    email aktivasi</button>
            </form>
            <a href="{{ url('/') }}"
                class="mt-5 inline-block text-sm font-bold text-[#8192aa] hover:text-[#1e40af]">Kembali ke beranda</a>

            @if (app()->environment(['local', 'testing']))
            <div class="mt-6 rounded-xl border border-dashed border-amber-300 bg-amber-50 p-4 text-left">
                <p class="text-xs font-bold text-amber-700">MODE LOKAL — tombol ini tidak tampil di production</p>
                <p class="mt-1 text-xs text-amber-700">Aktifkan manual lewat terminal:</p>
                <code
                    class="mt-1 block rounded bg-white px-2 py-1 text-xs text-amber-800">php artisan tenant:activate {{ $tenant->subdomain }}</code>
            </div>
            @endif
        </div>
    </main>
    <script>
    lucide.createIcons();
    </script>
</body>

</html>
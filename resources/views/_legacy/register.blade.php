<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftarkan Lembaga — Smart Absen</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body style="background:#f3f7fc; min-height:100dvh;">

<main class="flex min-h-[100dvh] items-center justify-center bg-[#f3f7fc] px-4 py-10">
  <div class="grid w-full max-w-4xl overflow-hidden rounded-3xl border border-[#dce6f3] bg-white shadow-[0_24px_70px_rgba(22,60,125,.12)] md:grid-cols-[.85fr_1.15fr]">

    <div class="hidden bg-[#102b69] p-10 text-white md:block">
      <div class="flex items-center gap-2.5">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#f6b51e] text-[#122e70]"><i data-lucide="calendar-check-2" style="width:19px;height:19px;stroke-width:2.5"></i></span>
        <span class="text-[17px] font-extrabold tracking-[-.06em] text-white">smart<span class="text-[#f0a914]">absen</span></span>
      </div>
      <div class="mt-24">
        <p class="eyebrow text-[#9dbfff]">Mulai gratis 14 hari</p>
        <h1 class="mt-4 text-4xl font-extrabold leading-tight tracking-[-.05em]">Ruang digital untuk lembaga Anda, siap dalam menit.</h1>
        <p class="mt-5 text-sm leading-6 text-blue-100/65">Setiap lembaga mendapat subdomain dan dashboard sendiri, langsung setelah daftar.</p>
      </div>
      <div class="mt-24 space-y-3 text-xs text-blue-100/60">
        <p class="flex items-center gap-2"><i data-lucide="check" style="width:14px;height:14px;color:#f6b51e"></i> Tanpa kartu kredit</p>
        <p class="flex items-center gap-2"><i data-lucide="check" style="width:14px;height:14px;color:#f6b51e"></i> Subdomain aktif otomatis</p>
        <p class="flex items-center gap-2"><i data-lucide="check" style="width:14px;height:14px;color:#f6b51e"></i> Bisa ganti logo & warna tema</p>
      </div>
    </div>

    <div class="p-7 sm:p-12">
      <a href="{{ route('landing') }}" class="mb-8 inline-block text-sm font-bold text-[#8192aa] hover:text-[#1e40af]">Kembali ke beranda</a>
      <div class="md:hidden mb-6 flex items-center gap-2.5">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#f6b51e] text-[#122e70]"><i data-lucide="calendar-check-2" style="width:19px;height:19px;stroke-width:2.5"></i></span>
        <span class="text-[17px] font-extrabold tracking-[-.06em] text-[#102b69]">smart<span class="text-[#f0a914]">absen</span></span>
      </div>
      <h2 class="mt-6 text-3xl font-extrabold tracking-tight text-[#102b69] md:mt-0">Daftarkan lembaga Anda</h2>
      <p class="mt-3 text-sm leading-6 text-[#71839b]">Isi data di bawah, subdomain Anda langsung aktif.</p>

      @if ($errors->any())
        <div class="mb-4 rounded-lg bg-[#fff1f1] px-3 py-2 text-xs font-semibold text-[#b34040]">
          <ul class="list-disc pl-4">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-4">
        @csrf
        <label class="block">
          <span class="mb-2 block text-xs font-bold text-[#405978]">Nama Lembaga</span>
          <input type="text" name="nama_lembaga" required placeholder="Contoh: TPQ Al-Hikmah" class="form-control h-12" value="{{ old('nama_lembaga') }}">
        </label>

        <label class="block">
          <span class="mb-2 block text-xs font-bold text-[#405978]">Jenis Lembaga</span>
          <select name="tipe_lembaga" required class="form-control h-12">
            @foreach (config('tenant_types') as $key => $t)
              <option value="{{ $key }}" @selected(old('tipe_lembaga') == $key)>{{ $t['label'] }}</option>
            @endforeach
          </select>
        </label>

        <label class="block">
          <span class="mb-2 block text-xs font-bold text-[#405978]">Subdomain</span>
          <div class="flex items-center overflow-hidden rounded-xl border border-[#cfd9e6] focus-within:border-[#2563eb]">
            <input type="text" name="subdomain" required placeholder="tpq" class="h-12 w-full border-0 px-4 text-sm focus:ring-0" value="{{ old('subdomain') }}">
            <span class="whitespace-nowrap bg-[#f3f7fc] px-3 text-xs font-semibold text-[#71839b]">.{{ config('app.main_domain') }}</span>
          </div>
        </label>

        <label class="block">
          <span class="mb-2 block text-xs font-bold text-[#405978]">Email Admin</span>
          <input type="email" name="email" required placeholder="admin@lembaga.id" class="form-control h-12" value="{{ old('email') }}">
        </label>

        <div class="grid gap-4 sm:grid-cols-2">
          <label class="block">
            <span class="mb-2 block text-xs font-bold text-[#405978]">Kata Sandi</span>
            <input type="password" name="password" required placeholder="Minimal 8 karakter" class="form-control h-12">
          </label>
          <label class="block">
            <span class="mb-2 block text-xs font-bold text-[#405978]">Konfirmasi Kata Sandi</span>
            <input type="password" name="password_confirmation" required placeholder="Ulangi kata sandi" class="form-control h-12">
          </label>
        </div>

        <button type="submit" class="button-lift mt-3 w-full rounded-xl bg-[#2563eb] py-3.5 text-sm font-bold text-white">Daftar & aktifkan subdomain <i data-lucide="arrow-right" style="width:15px;height:15px;margin-left:4px;display:inline"></i></button>
      </form>
      <p class="mt-6 text-center text-xs text-[#8192aa]">Sudah punya ruang institusi? <a href="{{ route('login') }}" class="font-bold text-[#2563eb]">Masuk di sini</a></p>
    </div>
  </div>
</main>

<script>
  lucide.createIcons();
</script>
</body>
</html>

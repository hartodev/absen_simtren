<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Organisasi — Smart Absen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body style="background:#f3f7fc; min-height:100dvh;">

    <main class="flex min-h-[100dvh] items-center justify-center bg-[#f3f7fc] px-4 py-10">
        <div
            class="grid w-full max-w-4xl overflow-hidden rounded-3xl border border-[#dce6f3] bg-white shadow-[0_24px_70px_rgba(22,60,125,.12)] md:grid-cols-[.85fr_1.15fr]">

            <div class="hidden bg-[#102b69] p-10 text-white md:block">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#f6b51e] text-[#122e70]"><i
                            data-lucide="calendar-check-2" style="width:19px;height:19px;stroke-width:2.5"></i></span>
                    <span class="text-[17px] font-extrabold tracking-[-.06em] text-white">smart<span
                            class="text-[#f0a914]">absen</span></span>
                </div>
                <div class="mt-24">
                    <p class="eyebrow text-[#9dbfff]">Mulai gratis</p>
                    <h1 class="mt-4 text-4xl font-extrabold leading-tight tracking-[-.05em]">Satu ruang khusus untuk
                        lembaga Anda.</h1>
                    <p class="mt-5 text-sm leading-6 text-blue-100/65">Daftarkan organisasi, dapatkan subdomain sendiri,
                        dan langsung kelola kehadiran dari dashboard Anda.</p>
                </div>
                <ul class="mt-24 space-y-3 text-xs text-blue-100/70">
                    <li class="flex items-center gap-2"><i data-lucide="check-circle-2"
                            style="width:15px;height:15px;color:#6fe3a1"></i> Subdomain khusus lembaga Anda</li>
                    <li class="flex items-center gap-2"><i data-lucide="check-circle-2"
                            style="width:15px;height:15px;color:#6fe3a1"></i> Aktivasi cepat lewat email</li>
                    <li class="flex items-center gap-2"><i data-lucide="check-circle-2"
                            style="width:15px;height:15px;color:#6fe3a1"></i> Data lembaga Anda terlindungi</li>
                </ul>
            </div>

            <div class="p-7 sm:p-12">
                <a href="{{ route('landing') }}"
                    class="mb-8 inline-block text-sm font-bold text-[#8192aa] hover:text-[#1e40af]">Kembali ke
                    beranda</a>
                <div class="md:hidden mb-6 flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#f6b51e] text-[#122e70]"><i
                            data-lucide="calendar-check-2" style="width:19px;height:19px;stroke-width:2.5"></i></span>
                    <span class="text-[17px] font-extrabold tracking-[-.06em] text-[#102b69]">smart<span
                            class="text-[#f0a914]">absen</span></span>
                </div>

                <h2 class="text-3xl font-extrabold tracking-tight text-[#102b69]">Daftar organisasi baru</h2>
                <p class="mt-2 text-sm leading-6 text-[#71839b]">Isi data lembaga dan akun admin Anda di bawah ini.</p>

                @if ($errors->any())
                <div class="mt-5 rounded-lg bg-[#fff1f1] px-3 py-2.5 text-xs font-semibold text-[#b34040]">
                    <ul class="list-disc space-y-0.5 pl-4">
                        @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('register.store') }}" class="mt-7 space-y-4">
                    @csrf

                    <label class="block">
                        <span class="mb-2 block text-xs font-bold text-[#405978]">Jenis organisasi</span>
                        <select name="type" required class="form-control h-12">
                            <option value="">-- Pilih --</option>
                            @foreach ($types as $key => $t)
                            <option value="{{ $key }}" @selected(old('type')==$key)>{{ $t['label'] }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="block">
                        <span class="mb-2 block text-xs font-bold text-[#405978]">Nama organisasi</span>
                        <input type="text" name="name" required value="{{ old('name') }}" placeholder="TPQ Al-Hikmah"
                            class="form-control h-12">
                    </label>

                    <label class="block">
                        <span class="mb-2 block text-xs font-bold text-[#405978]">Subdomain</span>
                        <div
                            class="flex items-center overflow-hidden rounded-xl border border-[#cfd9e6] bg-[#fbfcfe] focus-within:border-[#2563eb] focus-within:shadow-[0_0_0_4px_rgba(37,99,235,.1)]">
                            <input type="text" name="subdomain" required value="{{ old('subdomain') }}"
                                placeholder="tpqalhikmah"
                                class="h-12 w-full border-0 bg-transparent px-4 text-sm text-[#17386f] outline-none placeholder:text-[#9aabc0] focus:ring-0">
                            <span
                                class="whitespace-nowrap bg-[#f3f7fc] px-3 text-xs font-semibold text-[#71839b]">.{{ config('app.tenant_domain') }}</span>
                        </div>
                    </label>

                    <div class="flex items-center gap-3 pt-1">
                        <span class="h-px flex-1 bg-[#e5edf7]"></span>
                        <span class="text-[11px] font-bold uppercase tracking-wide text-[#9aabc0]">Akun admin
                            Anda</span>
                        <span class="h-px flex-1 bg-[#e5edf7]"></span>
                    </div>

                    <label class="block">
                        <span class="mb-2 block text-xs font-bold text-[#405978]">Nama lengkap</span>
                        <input type="text" name="admin_name" required value="{{ old('admin_name') }}"
                            class="form-control h-12">
                    </label>

                    <label class="block">
                        <span class="mb-2 block text-xs font-bold text-[#405978]">Email login</span>
                        <input type="email" name="admin_email" required value="{{ old('admin_email') }}"
                            placeholder="nama@institusi.id" class="form-control h-12">
                    </label>

                    <div class="grid grid-cols-2 gap-3">
                        <label class="block">
                            <span class="mb-2 block text-xs font-bold text-[#405978]">Password</span>
                            <input type="password" name="password" required minlength="6" class="form-control h-12">
                        </label>
                        <label class="block">
                            <span class="mb-2 block text-xs font-bold text-[#405978]">Konfirmasi</span>
                            <input type="password" name="password_confirmation" required minlength="6"
                                class="form-control h-12">
                        </label>
                    </div>

                    <button type="submit"
                        class="button-lift mt-3 w-full rounded-xl bg-[#2563eb] py-3.5 text-sm font-bold text-white">
                        Daftar sekarang <i data-lucide="arrow-right"
                            style="width:15px;height:15px;margin-left:4px;display:inline"></i>
                    </button>
                </form>

                <p class="mt-6 text-center text-xs text-[#8192aa]">
                    Sudah punya organisasi? <a href="{{ route('login.form') }}"
                        class="font-bold text-[#2563eb]">Login</a>
                </p>
            </div>
        </div>
    </main>

    <script>
    lucide.createIcons();
    </script>
</body>

</html>
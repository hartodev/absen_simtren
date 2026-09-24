<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body style="background:#f3f7fc;">
    <main class="flex min-h-screen items-center justify-center px-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-10 text-center shadow-lg">
            <h1 class="text-3xl font-extrabold text-gray-800">{{ config('app.name') }}</h1>
            <p class="mt-3 text-sm text-gray-500">
                Sistem absensi &amp; manajemen anggota untuk Perusahaan, TPQ/Pesantren, maupun Sekolah —
                masing-masing organisasi punya alamat &amp; data sendiri, terpisah aman satu sama lain.
            </p>

            <div class="mt-8 flex flex-col gap-3">
                <a href="{{ route('register.form') }}" class="rounded-lg bg-blue-600 py-3 text-sm font-bold text-white">
                    Daftarkan Organisasi Baru
                </a>
                <a href="{{ route('login.form') }}"
                    class="rounded-lg border border-gray-300 py-3 text-sm font-bold text-gray-600">
                    login setalah dapat sub domain
                </a>
            </div>

            <p class="mt-6 text-xs text-gray-400">
                Sudah punya organisasi? Login lewat alamat khusus organisasi Anda:
                <br><span class="font-mono">namaorganisasi.{{ config('app.tenant_domain') }}</span>
            </p>
        </div>
    </main>
</body>

</html>
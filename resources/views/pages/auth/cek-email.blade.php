<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cek Email Anda</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body style="background:#f3f7fc;">
<main class="flex min-h-screen items-center justify-center px-4">
  <div class="w-full max-w-md rounded-2xl bg-white p-8 text-center shadow-lg">
    <h1 class="text-2xl font-extrabold text-gray-800">Cek Email Anda</h1>
    <p class="mt-3 text-sm text-gray-500">
      Kami sudah mengirim link aktivasi ke
      @if ($email) <strong>{{ $email }}</strong> @else alamat email yang Anda daftarkan @endif.
      Klik link tersebut untuk mengaktifkan organisasi Anda. Link berlaku 24 jam.
    </p>

    @if (session('success'))
      <div class="mt-4 rounded-lg bg-green-50 px-3 py-2 text-sm font-semibold text-green-600">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('register.resend') }}" class="mt-6">
      @csrf
      <input type="hidden" name="email" value="{{ $email }}">
      <button type="submit" class="w-full rounded-lg border border-gray-300 py-3 text-sm font-bold text-blue-600">Kirim ulang email aktivasi</button>
    </form>

    <a href="{{ url('/') }}" class="mt-4 inline-block text-sm text-gray-500">Kembali ke beranda</a>
  </div>
</main>
</body>
</html>

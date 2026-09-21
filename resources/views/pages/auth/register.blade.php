<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Organisasi - Smart Absen</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body style="background:#f3f7fc;">
<main class="flex min-h-screen items-center justify-center px-4 py-10">
  <div class="w-full max-w-lg rounded-2xl bg-white p-8 shadow-lg">
    <h1 class="text-2xl font-extrabold text-gray-800">Daftar Organisasi Baru</h1>

    @if ($errors->any())
      <div class="mt-4 rounded-lg bg-red-50 px-3 py-2 text-sm font-semibold text-red-600">
        <ul class="list-disc pl-4">
          @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-4">
      @csrf

      <div>
        <label class="mb-1 block text-xs font-bold text-gray-500">Jenis Organisasi</label>
        <select name="type" required class="h-12 w-full rounded-lg border border-gray-300 px-3">
          <option value="">-- Pilih --</option>
          @foreach ($types as $key => $t)
            <option value="{{ $key }}" @selected(old('type') == $key)>{{ $t['label'] }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="mb-1 block text-xs font-bold text-gray-500">Nama Organisasi</label>
        <input type="text" name="name" required value="{{ old('name') }}" class="h-12 w-full rounded-lg border border-gray-300 px-3">
      </div>

      <div>
        <label class="mb-1 block text-xs font-bold text-gray-500">Subdomain</label>
        <div class="flex items-center gap-2">
          <input type="text" name="subdomain" required value="{{ old('subdomain') }}" placeholder="tpqalhikmah" class="h-12 w-full rounded-lg border border-gray-300 px-3">
          <span class="whitespace-nowrap text-sm text-gray-400">.{{ config('app.tenant_domain') }}</span>
        </div>
      </div>

      <hr>
      <p class="text-xs font-bold text-gray-500">Akun Admin Anda</p>

      <div>
        <label class="mb-1 block text-xs font-bold text-gray-500">Nama Lengkap</label>
        <input type="text" name="admin_name" required value="{{ old('admin_name') }}" class="h-12 w-full rounded-lg border border-gray-300 px-3">
      </div>

      <div>
        <label class="mb-1 block text-xs font-bold text-gray-500">Email Login</label>
        <input type="email" name="admin_email" required value="{{ old('admin_email') }}" class="h-12 w-full rounded-lg border border-gray-300 px-3">
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="mb-1 block text-xs font-bold text-gray-500">Password</label>
          <input type="password" name="password" required minlength="6" class="h-12 w-full rounded-lg border border-gray-300 px-3">
        </div>
        <div>
          <label class="mb-1 block text-xs font-bold text-gray-500">Konfirmasi</label>
          <input type="password" name="password_confirmation" required minlength="6" class="h-12 w-full rounded-lg border border-gray-300 px-3">
        </div>
      </div>

      <button type="submit" class="w-full rounded-lg bg-blue-600 py-3 text-sm font-bold text-white">Daftar Sekarang</button>
    </form>

    <p class="mt-5 text-center text-sm text-gray-500">
      Sudah punya organisasi? <a href="{{ url('/login') }}" class="font-bold text-blue-600">Login</a>
    </p>
  </div>
</main>
</body>
</html>

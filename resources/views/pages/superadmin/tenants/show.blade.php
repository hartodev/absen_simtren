@extends('layouts.superadmin')
@section('title', 'Detail Organisasi')
@section('content')
<h1 class="text-xl font-extrabold text-[#102b69]">{{ $tenant->name }}</h1>
<div class="mt-5 max-w-lg rounded-xl bg-white p-6 shadow-sm">
  <dl class="space-y-3 text-sm">
    <div class="flex justify-between"><dt class="text-gray-400">Subdomain</dt><dd class="font-mono">{{ $tenant->subdomain }}.{{ config('app.tenant_domain') }}</dd></div>
    <div class="flex justify-between"><dt class="text-gray-400">Tipe</dt><dd>{{ ucfirst($tenant->type) }}</dd></div>
    <div class="flex justify-between"><dt class="text-gray-400">Status</dt><dd>{{ ucfirst($tenant->status) }}</dd></div>
    <div class="flex justify-between"><dt class="text-gray-400">Jumlah User</dt><dd>{{ $tenant->users->count() }}</dd></div>
  </dl>
  <a href="{{ route('superadmin.tenants.index') }}" class="mt-5 inline-block text-sm font-bold text-blue-600">&larr; Kembali</a>
</div>

@if (in_array($tenant->type, ['pesantren', 'school'], true))
<form method="POST" action="{{ route('superadmin.tenants.style', $tenant->id) }}" class="mt-4 max-w-lg space-y-3 rounded-xl bg-white p-6 shadow-sm">
  @csrf
  <p class="text-sm font-bold text-[#102b69]">Tampilan aplikasi admin</p>
  <select name="dashboard_style" class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm">
    <option value="">Otomatis</option>
    <option value="simple" @selected($tenant->dashboard_style === 'simple')>Hijau — TPQ</option>
    <option value="full" @selected($tenant->dashboard_style === 'full')>Biru — sekolah umum / pondok pesantren</option>
  </select>
  <label class="flex items-center gap-2 text-sm text-gray-600">
    <input type="checkbox" name="is_boarding" value="1" @checked($tenant->is_boarding)> Punya layanan asrama
  </label>
  <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white">Simpan tampilan</button>
</form>
@endif
@endsection

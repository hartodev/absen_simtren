@extends('layouts.superadmin')
@section('title', 'Kelola Organisasi')
@section('content')
<div class="flex items-center justify-between">
  <h1 class="text-xl font-extrabold text-[#102b69]">Kelola Organisasi</h1>
  <a href="{{ route('superadmin.tenants.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-bold text-white">+ Tambah Manual</a>
</div>

@if (session('success'))
  <div class="mt-4 rounded-lg bg-green-50 px-3 py-2 text-sm font-semibold text-green-600">{{ session('success') }}</div>
@endif

<div class="mt-5 overflow-hidden rounded-xl bg-white shadow-sm">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 text-left text-xs font-bold uppercase text-gray-400">
      <tr><th class="px-4 py-3">Nama</th><th class="px-4 py-3">Subdomain</th><th class="px-4 py-3">Tipe</th><th class="px-4 py-3">Status</th><th class="px-4 py-3 text-right">Aksi</th></tr>
    </thead>
    <tbody class="divide-y">
      @forelse ($tenants as $t)
        <tr>
          <td class="px-4 py-3 font-semibold"><a href="{{ route('superadmin.tenants.show', $t->id) }}" class="hover:underline">{{ $t->name }}</a></td>
          <td class="px-4 py-3 font-mono text-xs">{{ $t->subdomain }}.{{ config('app.tenant_domain') }}</td>
          <td class="px-4 py-3">{{ ucfirst($t->type) }}</td>
          <td class="px-4 py-3">
            <span @class([
              'rounded-full px-2 py-1 text-xs font-bold',
              'bg-yellow-100 text-yellow-700' => $t->status == 'pending',
              'bg-green-100 text-green-700' => $t->status == 'aktif',
              'bg-red-100 text-red-700' => $t->status == 'nonaktif',
            ])>{{ ucfirst($t->status) }}</span>
          </td>
          <td class="px-4 py-3 text-right space-x-2">
            @if ($t->status !== 'aktif')
              <form method="POST" action="{{ route('superadmin.tenants.activate', $t->id) }}" class="inline">@csrf<button class="text-xs font-bold text-green-600">Aktifkan</button></form>
            @endif
            @if ($t->status !== 'nonaktif')
              <form method="POST" action="{{ route('superadmin.tenants.suspend', $t->id) }}" class="inline">@csrf<button class="text-xs font-bold text-red-600">Nonaktifkan</button></form>
            @endif
          </td>
        </tr>
      @empty
        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Belum ada organisasi terdaftar.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $tenants->links() }}</div>
@endsection

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
@endsection

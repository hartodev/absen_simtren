@extends('layouts.mobile')
@section('title', $module['label'])
@section('back', route($rp.'dashboard'))

@section('content')
<div class="flex flex-col items-center rounded-3xl bg-white px-6 py-12 text-center shadow-sm ring-1 ring-black/5">
    <span class="flex h-16 w-16 items-center justify-center rounded-full"
          style="background: {{ $module['color'] }}1f; color: {{ $module['color'] }}">
        <i data-lucide="{{ $module['icon'] }}" class="h-7 w-7"></i>
    </span>
    <h2 class="mt-4 text-lg font-bold text-slate-800">{{ $module['label'] }}</h2>
    <p class="mt-1 text-sm text-slate-500">Halaman ini sedang disiapkan.</p>

    @if ($count !== null)
        <div class="mt-5 rounded-2xl bg-slate-50 px-6 py-3">
            <p class="text-2xl font-extrabold text-slate-800">{{ number_format($count) }}</p>
            <p class="text-xs text-slate-500">data tersimpan</p>
        </div>
    @endif

    <a href="{{ route($rp.'dashboard') }}" class="btn btn-line mt-6">Kembali ke Beranda</a>
</div>
@endsection

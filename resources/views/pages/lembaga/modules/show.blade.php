@extends('layouts.admin')
@section('title', $module['label'])
@section('width', 'max-w-2xl')

@section('content')
<div class="card flex flex-col items-center px-6 py-14 text-center">
    <span class="flex h-16 w-16 items-center justify-center rounded-2xl"
          style="background: {{ $module['color'] }}1a; color: {{ $module['color'] }}">
        <i data-lucide="{{ $module['icon'] }}" class="h-7 w-7"></i>
    </span>
    <h2 class="mt-4 text-lg font-bold text-slate-800">{{ $module['label'] }}</h2>
    <p class="mt-1 text-sm text-slate-500">Halaman ini sedang disiapkan.</p>

    @if ($count !== null)
        <div class="mt-5 rounded-xl bg-slate-50 px-8 py-3">
            <p class="text-2xl font-extrabold text-slate-800">{{ number_format($count) }}</p>
            <p class="text-xs text-slate-500">data tersimpan</p>
        </div>
    @endif

    <a href="{{ route($rp.'dashboard') }}" class="btn btn-line mt-6"><i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali ke Beranda</a>
</div>
@endsection

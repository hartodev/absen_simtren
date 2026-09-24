{{-- Beranda BIRU: grid modul sekolah umum & pondok pesantren --}}
@extends('layouts.admin')
@section('title', 'Beranda')
@section('subtitle', 'Pilih modul yang ingin dikelola')
@section('width', 'max-w-7xl')

@section('content')
<div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
    @foreach ($modules as $m)
        <a href="{{ $m['url'] }}"
           class="card group flex min-h-[8.5rem] flex-col items-center justify-center gap-3 p-4 text-center transition hover:-translate-y-0.5 hover:shadow-md">
            <span class="flex h-14 w-14 items-center justify-center rounded-2xl transition group-hover:scale-105"
                  style="background: {{ $m['color'] }}1a; color: {{ $m['color'] }}">
                <i data-lucide="{{ $m['icon'] }}" class="h-6 w-6"></i>
            </span>
            <span class="text-[13px] font-semibold leading-tight text-slate-700">{{ $m['label'] }}</span>
        </a>
    @endforeach
</div>
@endsection

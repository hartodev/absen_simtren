{{-- Beranda BIRU: grid modul sekolah umum & pondok pesantren --}}
@extends('layouts.mobile')
@section('title', 'Beranda Admin')

@section('content')
<div class="grid grid-cols-2 gap-3">
    @foreach ($modules as $m)
        <a href="{{ $m['url'] }}"
           class="flex min-h-[9.5rem] flex-col items-center justify-center gap-3 rounded-3xl bg-white p-3 text-center shadow-sm ring-1 ring-black/5 transition active:scale-[.97]">
            <span class="flex h-14 w-14 items-center justify-center rounded-full"
                  style="background: {{ $m['color'] }}1f; color: {{ $m['color'] }}">
                <i data-lucide="{{ $m['icon'] }}" class="h-6 w-6"></i>
            </span>
            <span class="text-[13px] font-semibold leading-tight text-slate-800">{{ $m['label'] }}</span>
        </a>
    @endforeach
</div>
@endsection

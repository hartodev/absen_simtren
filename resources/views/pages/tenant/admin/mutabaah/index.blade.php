@extends('layouts.mobile')
@section('title', "Mutaba'ah")
@section('back', route($rp.'dashboard'))

@section('content')
<form method="GET"><input type="date" name="date" value="{{ $date->toDateString() }}" onchange="this.form.submit()" class="input"></form>

<h2 class="mt-4 text-sm font-bold text-slate-700">Sesi {{ $date->translatedFormat('d F Y') }}</h2>
<div class="mt-2 space-y-2">
    @forelse ($records as $r)
        <div class="rounded-2xl bg-white p-3 shadow-sm ring-1 ring-black/5">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-slate-800">{{ $r->santri->name ?? '-' }}</p>
                    <p class="text-xs text-slate-500">{{ ucfirst($r->sesi) }} · {{ strtoupper($r->kitab) }} jilid {{ $r->jilid }} · hal {{ $r->halaman_dari }}@if ($r->halaman_sampai)–{{ $r->halaman_sampai }}@endif</p>
                    <p class="text-[11px] text-slate-400">Ustadz {{ $r->ustadz->name ?? '-' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-bold" style="color: var(--brand)">{{ $r->keterangan }}</p>
                    <p class="text-[10px] font-semibold {{ $r->is_lanjut ? 'text-emerald-600' : 'text-amber-600' }}">{{ $r->is_lanjut ? 'Lanjut' : 'Ulang' }}</p>
                </div>
            </div>
            @if ($r->catatan)<p class="mt-1 text-xs text-slate-500">{{ $r->catatan }}</p>@endif
            <div class="mt-2">
                @if ($r->signed_by)
                    <span class="text-xs font-semibold text-emerald-700">✓ Diparaf {{ $r->signer->name ?? '' }}</span>
                @else
                    <form method="POST" action="{{ route($rp.'mutabaah.sign', $r->id) }}">@csrf
                        <button class="btn btn-brand !py-1.5 text-xs">Beri paraf</button></form>
                @endif
            </div>
        </div>
    @empty
        <div class="rounded-2xl bg-white px-4 py-8 text-center text-sm text-slate-400">Belum ada catatan di tanggal ini.</div>
    @endforelse
</div>

<details class="mt-4 rounded-2xl bg-white p-3 shadow-sm ring-1 ring-black/5" {{ $errors->any() ? 'open' : '' }}>
    <summary class="cursor-pointer text-sm font-bold" style="color: var(--brand)">+ Catat mutaba'ah manual</summary>
    <form method="POST" action="{{ route($rp.'mutabaah.store') }}" class="mt-3 space-y-2">
        @csrf
        <select name="santri_id" required class="input">
            <option value="">Pilih santri…</option>
            @foreach ($santri as $s)<option value="{{ $s->id }}" @selected(old('santri_id') == $s->id)>{{ $s->name }}</option>@endforeach
        </select>
        <div class="grid grid-cols-2 gap-2">
            <input type="date" name="tanggal" value="{{ old('tanggal', $date->toDateString()) }}" required class="input">
            <select name="sesi" class="input"><option value="pagi">Pagi</option><option value="sore">Sore</option></select>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <select name="kitab" class="input"><option value="iqro">Iqro</option><option value="quran">Al-Qur'an</option></select>
            <input type="number" name="jilid" min="1" max="7" placeholder="Jilid" value="{{ old('jilid') }}" required class="input">
        </div>
        <div class="grid grid-cols-2 gap-2">
            <input type="number" name="halaman_dari" min="1" placeholder="Halaman dari" value="{{ old('halaman_dari') }}" required class="input">
            <input type="number" name="halaman_sampai" min="1" placeholder="Sampai (opsional)" value="{{ old('halaman_sampai') }}" class="input">
        </div>
        <select name="keterangan" required class="input">
            @foreach (['A+','A','A-','B+','B','B-','C+','C','C-','D+','D','D-'] as $n)<option value="{{ $n }}" @selected(old('keterangan') === $n)>Nilai {{ $n }}</option>@endforeach
        </select>
        <input name="catatan" placeholder="Catatan (opsional)" value="{{ old('catatan') }}" class="input">
        <button class="btn btn-brand w-full">Simpan</button>
    </form>
</details>

<h2 class="mt-6 text-sm font-bold text-slate-700">Rekap {{ $date->translatedFormat('F Y') }}</h2>
<div class="mt-2 divide-y divide-slate-100 rounded-2xl bg-white shadow-sm ring-1 ring-black/5">
    @forelse ($rekap as $r)
        <div class="flex items-center justify-between px-3 py-2.5">
            <p class="truncate text-sm font-semibold text-slate-800">{{ $r->santri->name ?? '-' }}</p>
            <p class="shrink-0 text-xs text-slate-500">{{ $r->sesi }} sesi · {{ (int) $r->lanjut }} lanjut · {{ (int) $r->diparaf }} paraf</p>
        </div>
    @empty
        <p class="px-3 py-5 text-center text-sm text-slate-400">Belum ada data bulan ini.</p>
    @endforelse
</div>
@endsection

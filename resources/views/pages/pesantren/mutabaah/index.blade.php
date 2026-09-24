@extends('layouts.admin')
@section('title', "Mutaba'ah")
@section('subtitle', 'Progres ngaji santri — '.$date->translatedFormat('d F Y'))
@section('width', 'max-w-7xl')
@section('actions')
    <form method="GET"><input type="date" name="date" value="{{ $date->toDateString() }}" onchange="this.form.submit()" class="input !w-44"></form>
@endsection

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    {{-- Kiri: sesi hari ini + rekap --}}
    <div class="space-y-6 lg:col-span-2">
        <section>
            <h2 class="mb-2 text-sm font-bold text-slate-700">Sesi {{ $date->translatedFormat('d F Y') }}</h2>
            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="tbl">
                        <thead><tr><th>Santri</th><th>Sesi</th><th>Bacaan</th><th>Nilai</th><th>Status</th><th>Paraf</th></tr></thead>
                        <tbody>
                            @forelse ($records as $r)
                                <tr>
                                    <td>
                                        <p class="font-semibold text-slate-800">{{ $r->santri->name ?? '-' }}</p>
                                        <p class="text-[11px] text-slate-400">Ustadz {{ $r->ustadz->name ?? '-' }}@if ($r->catatan) · {{ $r->catatan }}@endif</p>
                                    </td>
                                    <td class="text-slate-600">{{ ucfirst($r->sesi) }}</td>
                                    <td class="text-slate-600">{{ strtoupper($r->kitab) }} jilid {{ $r->jilid }} · hal {{ $r->halaman_dari }}@if ($r->halaman_sampai)–{{ $r->halaman_sampai }}@endif</td>
                                    <td class="text-base font-bold" style="color: var(--brand)">{{ $r->keterangan }}</td>
                                    <td><span class="badge {{ $r->is_lanjut ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ $r->is_lanjut ? 'Lanjut' : 'Ulang' }}</span></td>
                                    <td>
                                        @if ($r->signed_by)
                                            <span class="text-xs font-semibold text-emerald-700">✓ {{ $r->signer->name ?? '' }}</span>
                                        @else
                                            <form method="POST" action="{{ route($rp.'mutabaah.sign', $r->id) }}">@csrf
                                                <button class="btn btn-brand btn-sm">Beri paraf</button></form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="empty">Belum ada catatan di tanggal ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section>
            <h2 class="mb-2 text-sm font-bold text-slate-700">Rekap {{ $date->translatedFormat('F Y') }}</h2>
            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="tbl">
                        <thead><tr><th>Santri</th><th>Sesi</th><th>Lanjut</th><th>Diparaf</th></tr></thead>
                        <tbody>
                            @forelse ($rekap as $r)
                                <tr>
                                    <td class="font-semibold text-slate-800">{{ $r->santri->name ?? '-' }}</td>
                                    <td class="text-slate-600">{{ $r->sesi }}</td>
                                    <td class="text-slate-600">{{ (int) $r->lanjut }}</td>
                                    <td class="text-slate-600">{{ (int) $r->diparaf }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="empty">Belum ada data bulan ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    {{-- Kanan: form input manual --}}
    <aside>
        <div class="card p-5 lg:sticky lg:top-24">
            <h2 class="text-sm font-bold" style="color: var(--brand)">+ Catat mutaba'ah manual</h2>
            <form method="POST" action="{{ route($rp.'mutabaah.store') }}" class="mt-4 space-y-3">
                @csrf
                <div><label class="label">Santri</label>
                    <select name="santri_id" required class="input">
                        <option value="">Pilih santri…</option>
                        @foreach ($santri as $s)<option value="{{ $s->id }}" @selected(old('santri_id') == $s->id)>{{ $s->name }}</option>@endforeach
                    </select></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="label">Tanggal</label><input type="date" name="tanggal" value="{{ old('tanggal', $date->toDateString()) }}" required class="input"></div>
                    <div><label class="label">Sesi</label><select name="sesi" class="input"><option value="pagi">Pagi</option><option value="sore">Sore</option></select></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="label">Kitab</label><select name="kitab" class="input"><option value="iqro">Iqro</option><option value="quran">Al-Qur'an</option></select></div>
                    <div><label class="label">Jilid</label><input type="number" name="jilid" min="1" max="7" value="{{ old('jilid') }}" required class="input"></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="label">Halaman dari</label><input type="number" name="halaman_dari" min="1" value="{{ old('halaman_dari') }}" required class="input"></div>
                    <div><label class="label">Sampai</label><input type="number" name="halaman_sampai" min="1" placeholder="opsional" value="{{ old('halaman_sampai') }}" class="input"></div>
                </div>
                <div><label class="label">Nilai</label>
                    <select name="keterangan" required class="input">
                        @foreach (['A+','A','A-','B+','B','B-','C+','C','C-','D+','D','D-'] as $n)<option value="{{ $n }}" @selected(old('keterangan') === $n)>{{ $n }}</option>@endforeach
                    </select></div>
                <div><label class="label">Catatan</label><input name="catatan" placeholder="opsional" value="{{ old('catatan') }}" class="input"></div>
                <button class="btn btn-brand w-full">Simpan</button>
            </form>
        </div>
    </aside>
</div>
@endsection

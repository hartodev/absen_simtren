@extends('layouts.mobile')
@section('title', 'Device Kiosk')
@section('back', route($rp.'dashboard'))
@section('header_action')
    <a href="{{ route($rp.'devices.create') }}" class="flex h-9 w-9 items-center justify-center rounded-full bg-white/15"><i data-lucide="plus" class="h-5 w-5"></i></a>
@endsection

@section('content')
@if (session('new_token'))
    <div class="mb-3 rounded-2xl border border-amber-200 bg-amber-50 p-3">
        <p class="text-xs font-bold text-amber-800">Token device — salin sekarang, tidak akan ditampilkan lagi</p>
        <div class="mt-2 flex items-center gap-2">
            <code id="tok" class="min-w-0 flex-1 break-all rounded-lg bg-white px-2 py-1.5 text-xs">{{ session('new_token') }}</code>
            <button type="button" class="btn btn-brand !px-3 !py-1.5"
                    onclick="navigator.clipboard.writeText(document.getElementById('tok').textContent); this.textContent='Tersalin'">Salin</button>
        </div>
    </div>
@endif

<div class="space-y-2">
    @forelse ($devices as $d)
        <div class="rounded-2xl bg-white p-3 shadow-sm ring-1 ring-black/5">
            <div class="flex items-start gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-orange-50 text-orange-600"><i data-lucide="tablet" class="h-5 w-5"></i></span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-slate-800">{{ $d->name }}</p>
                    <p class="text-xs text-slate-500">{{ $d->classRoom ? 'Kelas '.$d->classRoom->name : 'Umum (semua kelas)' }}</p>
                    <p class="text-[11px] text-slate-400">Terakhir aktif: {{ $d->last_seen_at ? $d->last_seen_at->diffForHumans() : 'belum pernah' }}</p>
                </div>
                <span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ $d->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $d->is_active ? 'Aktif' : 'Nonaktif' }}</span>
            </div>
            <div class="mt-3 flex gap-2">
                <a href="{{ route($rp.'devices.edit', $d->id) }}" class="btn btn-line flex-1 !py-2">Edit</a>
                <form method="POST" action="{{ route($rp.'devices.regenerate', $d->id) }}" class="flex-1"
                      onsubmit="return confirm('Buat token baru? Token lama langsung tidak berlaku.')">
                    @csrf <button class="btn btn-line w-full !py-2">Reset token</button>
                </form>
                <form method="POST" action="{{ route($rp.'devices.destroy', $d->id) }}" onsubmit="return confirm('Hapus device ini?')">
                    @csrf @method('DELETE') <button class="btn btn-danger !px-3 !py-2"><i data-lucide="trash-2" class="h-4 w-4"></i></button>
                </form>
            </div>
        </div>
    @empty
        <div class="rounded-2xl bg-white px-4 py-10 text-center text-sm text-slate-400">Belum ada device kiosk.</div>
    @endforelse
</div>
@endsection

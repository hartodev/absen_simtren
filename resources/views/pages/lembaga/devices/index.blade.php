@extends('layouts.admin')
@section('title', 'Device Kiosk')
@section('subtitle', 'Tablet / perangkat absen mandiri untuk murid')
@section('actions')
    <a href="{{ route($rp.'devices.create') }}" class="btn btn-brand"><i data-lucide="plus" class="h-4 w-4"></i> <span class="hidden sm:inline">Tambah Device</span></a>
@endsection

@section('content')
@if (session('new_token'))
    <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 p-4">
        <p class="text-sm font-bold text-amber-800">Token device — salin sekarang, tidak akan ditampilkan lagi</p>
        <div class="mt-2 flex items-center gap-2">
            <code id="tok" class="min-w-0 flex-1 break-all rounded-lg bg-white px-3 py-2 text-xs">{{ session('new_token') }}</code>
            <button type="button" class="btn btn-brand" onclick="navigator.clipboard.writeText(document.getElementById('tok').textContent); this.textContent='Tersalin'">Salin</button>
        </div>
    </div>
@endif

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="tbl">
            <thead><tr><th>Device</th><th>Untuk</th><th>Terakhir aktif</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse ($devices as $d)
                    <tr>
                        <td><div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-50 text-orange-600"><i data-lucide="tablet" class="h-4 w-4"></i></span>
                            <span class="font-semibold text-slate-800">{{ $d->name }}</span></div></td>
                        <td class="text-slate-600">{{ $d->classRoom ? 'Kelas '.$d->classRoom->name : 'Umum (semua kelas)' }}</td>
                        <td class="text-slate-500">{{ $d->last_seen_at ? $d->last_seen_at->diffForHumans() : 'belum pernah' }}</td>
                        <td><span class="badge {{ $d->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $d->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td>
                            <div class="flex justify-end gap-2">
                                <a href="{{ route($rp.'devices.edit', $d->id) }}" class="btn btn-line btn-sm">Edit</a>
                                <form method="POST" action="{{ route($rp.'devices.regenerate', $d->id) }}" onsubmit="return confirm('Buat token baru? Token lama langsung tidak berlaku.')">
                                    @csrf <button class="btn btn-line btn-sm">Reset token</button></form>
                                <form method="POST" action="{{ route($rp.'devices.destroy', $d->id) }}" onsubmit="return confirm('Hapus device ini?')">
                                    @csrf @method('DELETE') <button class="btn btn-danger btn-sm" aria-label="Hapus"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i></button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">Belum ada device kiosk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

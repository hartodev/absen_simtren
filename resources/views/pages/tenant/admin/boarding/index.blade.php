@extends('layouts.mobile')
@section('title', 'Izin Keluar/Pulang')
@section('back', route($rp.'dashboard'))

@section('content')
@php $tabs = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'sudah_kembali' => 'Kembali', 'rejected' => 'Ditolak']; @endphp

<div class="flex rounded-xl bg-white p-1 shadow-sm ring-1 ring-black/5">
    @foreach ($tabs as $k => $l)
        <a href="{{ route($rp.'boarding.index', ['status' => $k]) }}"
           class="flex-1 rounded-lg py-2 text-center text-[11px] font-bold {{ $status === $k ? 'text-white' : 'text-slate-500' }}"
           style="{{ $status === $k ? 'background: var(--brand)' : '' }}">
            {{ $l }}@if (($counts[$k] ?? 0) > 0) <span class="opacity-70">({{ $counts[$k] }})</span>@endif
        </a>
    @endforeach
</div>

<div class="mt-3 space-y-2">
    @forelse ($permissions as $p)
        <div class="rounded-2xl bg-white p-3 shadow-sm ring-1 ring-black/5">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-slate-800">{{ $p->student->name ?? '-' }}</p>
                    <p class="text-xs text-slate-500">diajukan {{ $p->submitter->name ?? '-' }}</p>
                </div>
                <span class="shrink-0 rounded-full bg-violet-50 px-2 py-0.5 text-[11px] font-bold text-violet-700">{{ $p->jenis === 'izin_pulang' ? 'Pulang' : 'Keluar' }}</span>
            </div>
            <p class="mt-2 text-xs text-slate-600">
                {{ $p->tanggal_keluar->format('d M Y') }} → kembali {{ $p->tanggal_kembali_rencana->format('d M Y') }}
                @if ($p->tanggal_kembali_aktual) <span class="text-emerald-700">(aktual {{ $p->tanggal_kembali_aktual->format('d M') }})</span>@endif
            </p>
            <p class="mt-0.5 text-sm text-slate-600">{{ $p->alasan }}</p>
            @if ($p->nama_penjemput)
                <p class="mt-1 text-xs text-slate-400">Penjemput: {{ $p->nama_penjemput }} ({{ $p->hubungan_penjemput }}) {{ $p->kontak_penjemput }}</p>
            @endif

            @if (in_array($p->status, ['pending', 'approved'], true))
                <form method="POST" action="{{ route($rp.'boarding.review', $p->id) }}" class="mt-3">
                    @csrf
                    @if ($p->status === 'pending')
                        <input name="catatan_review" placeholder="Catatan (opsional)" class="input !py-2 text-sm">
                        <div class="mt-2 flex gap-2">
                            <button name="action" value="reject" class="btn btn-danger flex-1 !py-2">Tolak</button>
                            <button name="action" value="approve" class="btn btn-brand flex-1 !py-2">Setujui</button>
                        </div>
                    @else
                        <button name="action" value="return" class="btn btn-brand w-full !py-2">Tandai sudah kembali</button>
                    @endif
                </form>
            @endif
        </div>
    @empty
        <div class="rounded-2xl bg-white px-4 py-10 text-center text-sm text-slate-400">Tidak ada data.</div>
    @endforelse
</div>
<div class="mt-4">{{ $permissions->links() }}</div>
@endsection

@extends('layouts.admin')
@section('title', 'Izin Keluar/Pulang')
@section('subtitle', 'Perizinan santri keluar atau pulang dari asrama')

@section('content')
@php $tabs = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'sudah_kembali' => 'Kembali', 'rejected' => 'Ditolak']; @endphp

<div class="mb-4 inline-flex max-w-full flex-wrap rounded-xl border border-slate-200 bg-white p-1">
    @foreach ($tabs as $k => $l)
        <a href="{{ route($rp.'boarding.index', ['status' => $k]) }}"
           class="rounded-lg px-4 py-1.5 text-sm font-semibold {{ $status === $k ? 'text-white' : 'text-slate-500 hover:text-slate-700' }}"
           style="{{ $status === $k ? 'background: var(--brand)' : '' }}">
            {{ $l }}@if (($counts[$k] ?? 0) > 0) <span class="opacity-70">({{ $counts[$k] }})</span>@endif
        </a>
    @endforeach
</div>

<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    @forelse ($permissions as $p)
        <div class="card flex flex-col p-4">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="truncate font-semibold text-slate-800">{{ $p->student->name ?? '-' }}</p>
                    <p class="text-xs text-slate-500">diajukan {{ $p->submitter->name ?? '-' }}</p>
                </div>
                <span class="badge shrink-0 bg-violet-50 text-violet-700">{{ $p->jenis === 'izin_pulang' ? 'Pulang' : 'Keluar' }}</span>
            </div>
            <p class="mt-3 text-xs font-medium text-slate-600">
                {{ $p->tanggal_keluar->format('d M Y') }} → kembali {{ $p->tanggal_kembali_rencana->format('d M Y') }}
                @if ($p->tanggal_kembali_aktual) <span class="text-emerald-700">(aktual {{ $p->tanggal_kembali_aktual->format('d M') }})</span>@endif
            </p>
            <p class="mt-1 flex-1 text-sm text-slate-600">{{ $p->alasan }}</p>
            @if ($p->nama_penjemput)
                <p class="mt-2 text-xs text-slate-400">Penjemput: {{ $p->nama_penjemput }} ({{ $p->hubungan_penjemput }}) {{ $p->kontak_penjemput }}</p>
            @endif

            @if (in_array($p->status, ['pending', 'approved'], true))
                <form method="POST" action="{{ route($rp.'boarding.review', $p->id) }}" class="mt-4 space-y-2">
                    @csrf
                    @if ($p->status === 'pending')
                        <input name="catatan_review" placeholder="Catatan (opsional)" class="input">
                        <div class="flex gap-2">
                            <button name="action" value="reject" class="btn btn-danger flex-1">Tolak</button>
                            <button name="action" value="approve" class="btn btn-brand flex-1">Setujui</button>
                        </div>
                    @else
                        <button name="action" value="return" class="btn btn-brand w-full">Tandai sudah kembali</button>
                    @endif
                </form>
            @endif
        </div>
    @empty
        <div class="card empty md:col-span-2 xl:col-span-3">Tidak ada data.</div>
    @endforelse
</div>
<div class="mt-4">{{ $permissions->links() }}</div>
@endsection

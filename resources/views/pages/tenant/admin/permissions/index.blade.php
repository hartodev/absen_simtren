@extends('layouts.mobile')
@section('title', 'Izin Siswa')

@section('content')
@php $tabs = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak']; @endphp

<div class="flex rounded-xl bg-white p-1 shadow-sm ring-1 ring-black/5">
    @foreach ($tabs as $k => $l)
        <a href="{{ route($rp.'student-permissions.index', ['status' => $k]) }}"
           class="flex-1 rounded-lg py-2 text-center text-xs font-bold {{ $status === $k ? 'text-white' : 'text-slate-500' }}"
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
                    <p class="text-xs text-slate-500">Kelas {{ $p->student->classRoom->name ?? '-' }} · diajukan {{ $p->submitter->name ?? '-' }}</p>
                </div>
                <span class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-bold {{ $p->type === 'sakit' ? 'bg-violet-50 text-violet-700' : 'bg-sky-50 text-sky-700' }}">{{ ucfirst($p->type) }}</span>
            </div>
            <p class="mt-2 text-xs font-semibold text-slate-600">{{ $p->date_permission->translatedFormat('l, d F Y') }}</p>
            <p class="mt-0.5 text-sm text-slate-600">{{ $p->reason }}</p>

            @if ($p->status === 'pending')
                <form method="POST" action="{{ route($rp.'student-permissions.review', $p->id) }}" class="mt-3 flex gap-2">
                    @csrf
                    <button name="action" value="reject" class="btn btn-danger flex-1 !py-2">Tolak</button>
                    <button name="action" value="approve" class="btn btn-brand flex-1 !py-2">Setujui</button>
                </form>
            @else
                <p class="mt-2 text-[11px] text-slate-400">Diproses {{ optional($p->reviewed_at)->diffForHumans() }}</p>
            @endif
        </div>
    @empty
        <div class="rounded-2xl bg-white px-4 py-10 text-center text-sm text-slate-400">Tidak ada izin {{ strtolower($tabs[$status]) }}.</div>
    @endforelse
</div>
<div class="mt-4">{{ $permissions->links() }}</div>
@endsection

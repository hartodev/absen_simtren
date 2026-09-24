@extends('layouts.admin')
@section('title', 'Izin Siswa')
@section('subtitle', 'Pengajuan izin & sakit dari wali murid')

@section('content')
@php $tabs = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak']; @endphp

<div class="mb-4 inline-flex rounded-xl border border-slate-200 bg-white p-1">
    @foreach ($tabs as $k => $l)
        <a href="{{ route($rp.'student-permissions.index', ['status' => $k]) }}"
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
                    <p class="text-xs text-slate-500">Kelas {{ $p->student->classRoom->name ?? '-' }} · diajukan {{ $p->submitter->name ?? '-' }}</p>
                </div>
                <span class="badge shrink-0 {{ $p->type === 'sakit' ? 'bg-violet-50 text-violet-700' : 'bg-sky-50 text-sky-700' }}">{{ ucfirst($p->type) }}</span>
            </div>
            <p class="mt-3 text-xs font-semibold text-slate-600">{{ $p->date_permission->translatedFormat('l, d F Y') }}</p>
            <p class="mt-1 flex-1 text-sm text-slate-600">{{ $p->reason }}</p>

            @if ($p->status === 'pending')
                <form method="POST" action="{{ route($rp.'student-permissions.review', $p->id) }}" class="mt-4 flex gap-2">
                    @csrf
                    <button name="action" value="reject" class="btn btn-danger flex-1">Tolak</button>
                    <button name="action" value="approve" class="btn btn-brand flex-1">Setujui</button>
                </form>
            @else
                <p class="mt-3 text-[11px] text-slate-400">Diproses {{ optional($p->reviewed_at)->diffForHumans() }}</p>
            @endif
        </div>
    @empty
        <div class="card empty md:col-span-2 xl:col-span-3">Tidak ada izin {{ strtolower($tabs[$status]) }}.</div>
    @endforelse
</div>
<div class="mt-4">{{ $permissions->links() }}</div>
@endsection

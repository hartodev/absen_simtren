@extends('layouts.mobile')
@section('title', 'Data Murid')
@section('back', route($rp.'dashboard'))
@section('header_action')
    <a href="{{ route($rp.'students.create') }}" class="flex h-9 w-9 items-center justify-center rounded-full bg-white/15"><i data-lucide="plus" class="h-5 w-5"></i></a>
@endsection

@section('content')
<form method="GET">
    <div class="flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / NIS / NISN" class="input">
        <button class="btn btn-brand px-3"><i data-lucide="search" class="h-4 w-4"></i></button>
    </div>
    <div class="mt-2 flex gap-2">
        <select name="class_id" onchange="this.form.submit()" class="input !py-2 text-sm">
            <option value="">Semua kelas</option>
            @foreach ($classes as $c)
                <option value="{{ $c->id }}" @selected(request('class_id') == $c->id)>{{ $c->name }} ({{ $c->academic_year }})</option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()" class="input !w-32 !py-2 text-sm">
            <option value="">Aktif</option>
            <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
        </select>
    </div>
</form>

<p class="mt-3 text-xs text-slate-500">{{ $total }} murid aktif</p>

<div class="mt-2 space-y-2">
    @forelse ($students as $s)
        <a href="{{ route($rp.'students.edit', $s->id) }}" class="flex items-center gap-3 rounded-2xl bg-white p-3 shadow-sm ring-1 ring-black/5">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-sm font-bold text-white"
                  style="background: {{ $s->gender === 'P' ? '#d81b60' : 'var(--brand)' }}">{{ strtoupper(mb_substr($s->name, 0, 1)) }}</span>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-slate-800">{{ $s->name }}</p>
                <p class="truncate text-xs text-slate-500">NIS {{ $s->nis }} · {{ $s->classRoom->name ?? 'Belum ada kelas' }}
                    @if ($s->is_boarding) · <span class="font-semibold text-amber-600">Mondok</span>@endif</p>
            </div>
            <i data-lucide="chevron-right" class="h-4 w-4 text-slate-300"></i>
        </a>
    @empty
        <div class="rounded-2xl bg-white px-4 py-10 text-center text-sm text-slate-400">Belum ada data murid.</div>
    @endforelse
</div>

<div class="mt-4">{{ $students->links() }}</div>
@endsection

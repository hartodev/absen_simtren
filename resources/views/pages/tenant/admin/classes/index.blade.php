@extends('layouts.mobile')
@section('title', 'Data Kelas')
@section('back', route($rp.'dashboard'))
@section('header_action')
    <a href="{{ route($rp.'classes.create') }}" class="flex h-9 w-9 items-center justify-center rounded-full bg-white/15"><i data-lucide="plus" class="h-5 w-5"></i></a>
@endsection

@section('content')
@if ($years->count() > 1)
    <form method="GET"><select name="year" onchange="this.form.submit()" class="input !py-2 text-sm">
        <option value="">Semua tahun ajaran</option>
        @foreach ($years as $y)<option value="{{ $y }}" @selected($year === $y)>{{ $y }}</option>@endforeach
    </select></form>
@endif

<div class="mt-3 space-y-2">
    @forelse ($classes as $c)
        <a href="{{ route($rp.'classes.edit', $c->id) }}" class="flex items-center gap-3 rounded-2xl bg-white p-3 shadow-sm ring-1 ring-black/5 {{ $c->is_active ? '' : 'opacity-60' }}">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-teal-50 text-sm font-bold text-teal-700">{{ $c->name }}</span>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-slate-800">Kelas {{ $c->name }} <span class="font-normal text-slate-400">· tingkat {{ $c->grade_level }}</span></p>
                <p class="truncate text-xs text-slate-500">{{ $c->academic_year }} · {{ $c->students_count }} murid · Wali: {{ $c->homeroomTeacher->name ?? '—' }}</p>
            </div>
            <i data-lucide="chevron-right" class="h-4 w-4 text-slate-300"></i>
        </a>
    @empty
        <div class="rounded-2xl bg-white px-4 py-10 text-center text-sm text-slate-400">Belum ada kelas. Tekan + untuk menambah.</div>
    @endforelse
</div>
@endsection

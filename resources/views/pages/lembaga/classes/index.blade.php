@extends('layouts.admin')
@section('title', 'Data Kelas')
@section('subtitle', $classes->count().' kelas')
@section('actions')
    <a href="{{ route($rp.'classes.create') }}" class="btn btn-brand"><i data-lucide="plus" class="h-4 w-4"></i> <span class="hidden sm:inline">Tambah Kelas</span></a>
@endsection

@section('content')
@if ($years->count() > 1)
    <form method="GET" class="mb-4"><select name="year" onchange="this.form.submit()" class="input sm:w-64">
        <option value="">Semua tahun ajaran</option>
        @foreach ($years as $y)<option value="{{ $y }}" @selected($year === $y)>{{ $y }}</option>@endforeach
    </select></form>
@endif

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="tbl">
            <thead><tr><th>Kelas</th><th>Tingkat</th><th>Tahun ajaran</th><th>Murid</th><th>Wali kelas</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse ($classes as $c)
                    <tr>
                        <td><a href="{{ route($rp.'classes.edit', $c->id) }}" class="flex items-center gap-3">
                            <span class="flex h-9 min-w-[2.25rem] items-center justify-center rounded-lg bg-teal-50 px-2 text-xs font-bold text-teal-700">{{ $c->name }}</span>
                            <span class="font-semibold text-slate-800">Kelas {{ $c->name }}</span></a></td>
                        <td class="text-slate-600">{{ $c->grade_level }}</td>
                        <td class="text-slate-600">{{ $c->academic_year }}</td>
                        <td class="text-slate-600">{{ $c->students_count }}</td>
                        <td class="text-slate-600">{{ $c->homeroomTeacher->name ?? '—' }}</td>
                        <td><span class="badge {{ $c->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $c->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td class="text-right"><a href="{{ route($rp.'classes.edit', $c->id) }}" class="btn btn-line btn-sm">Edit</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Belum ada kelas. Klik "Tambah Kelas" untuk memulai.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

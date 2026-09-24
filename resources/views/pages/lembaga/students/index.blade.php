@extends('layouts.admin')
@section('title', 'Data Murid')
@section('subtitle', $total.' murid aktif')
@section('actions')
    <a href="{{ route($rp.'students.create') }}" class="btn btn-brand"><i data-lucide="plus" class="h-4 w-4"></i> <span class="hidden sm:inline">Tambah Murid</span></a>
@endsection

@section('content')
<form method="GET" class="card mb-4 flex flex-col gap-3 p-4 md:flex-row">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / NIS / NISN" class="input md:flex-1">
    <select name="class_id" onchange="this.form.submit()" class="input md:w-64">
        <option value="">Semua kelas</option>
        @foreach ($classes as $c)
            <option value="{{ $c->id }}" @selected(request('class_id') == $c->id)>{{ $c->name }} ({{ $c->academic_year }})</option>
        @endforeach
    </select>
    <select name="status" onchange="this.form.submit()" class="input md:w-36">
        <option value="">Aktif</option>
        <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
    </select>
    <button class="btn btn-brand"><i data-lucide="search" class="h-4 w-4"></i> Cari</button>
</form>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="tbl">
            <thead><tr><th>Murid</th><th>NIS</th><th>NISN</th><th>Kelas</th><th>L/P</th><th></th></tr></thead>
            <tbody>
                @forelse ($students as $s)
                    <tr>
                        <td>
                            <a href="{{ route($rp.'students.edit', $s->id) }}" class="flex items-center gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-xs font-bold text-white"
                                      style="background: {{ $s->gender === 'P' ? '#d81b60' : 'var(--brand)' }}">{{ strtoupper(mb_substr($s->name, 0, 1)) }}</span>
                                <span class="font-semibold text-slate-800">{{ $s->name }}</span>
                                @if ($s->is_boarding)<span class="badge bg-amber-50 text-amber-700">Mondok</span>@endif
                            </a>
                        </td>
                        <td class="text-slate-600">{{ $s->nis }}</td>
                        <td class="text-slate-500">{{ $s->nisn ?: '—' }}</td>
                        <td class="text-slate-600">{{ $s->classRoom->name ?? 'Belum ada kelas' }}</td>
                        <td class="text-slate-600">{{ $s->gender }}</td>
                        <td class="text-right"><a href="{{ route($rp.'students.edit', $s->id) }}" class="btn btn-line btn-sm">Edit</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Belum ada data murid.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $students->links() }}</div>
@endsection

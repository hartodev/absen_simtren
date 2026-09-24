@extends('layouts.admin')
@php $edit = $class->exists; @endphp
@section('title', $edit ? 'Edit Kelas' : 'Tambah Kelas')
@section('width', 'max-w-2xl')
@section('actions')
    <a href="{{ route($rp.'classes.index') }}" class="btn btn-line"><i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali</a>
@endsection

@section('content')
<form method="POST" action="{{ $edit ? route($rp.'classes.update', $class->id) : route($rp.'classes.store') }}" class="card space-y-4 p-6">
    @csrf
    @if ($edit) @method('PUT') @endif

    <div class="grid gap-4 md:grid-cols-2">
        <div><label class="label">Nama kelas *</label><input name="name" value="{{ old('name', $class->name) }}" placeholder="mis. 1A" required class="input"></div>
        <div><label class="label">Tingkat (1–12) *</label><input type="number" min="1" max="12" name="grade_level" value="{{ old('grade_level', $class->grade_level) }}" required class="input"></div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div><label class="label">Tahun ajaran *</label><input name="academic_year" value="{{ old('academic_year', $class->academic_year) }}" placeholder="2026/2027" required class="input"></div>
        <div><label class="label">Wali kelas</label>
            <select name="homeroom_teacher_id" class="input">
                <option value="">— belum ditentukan —</option>
                @foreach ($teachers as $t)
                    <option value="{{ $t->id }}" @selected(old('homeroom_teacher_id', $class->homeroom_teacher_id) == $t->id)>{{ $t->name }}</option>
                @endforeach
            </select></div>
    </div>

    <label class="flex w-fit items-center gap-2 rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $class->is_active)) class="h-4 w-4"> Kelas aktif
    </label>

    <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
        <a href="{{ route($rp.'classes.index') }}" class="btn btn-line">Batal</a>
        <button class="btn btn-brand px-6">Simpan</button>
    </div>
</form>

@if ($edit)
    <form method="POST" action="{{ route($rp.'classes.destroy', $class->id) }}" class="mt-4 text-right" onsubmit="return confirm('Hapus kelas ini?')">
        @csrf @method('DELETE')
        <button class="btn btn-danger"><i data-lucide="trash-2" class="h-4 w-4"></i> Hapus kelas</button>
    </form>
@endif
@endsection

@extends('layouts.mobile')
@php $edit = $class->exists; @endphp
@section('title', $edit ? 'Edit Kelas' : 'Tambah Kelas')
@section('back', route($rp.'classes.index'))

@section('content')
<form method="POST" action="{{ $edit ? route($rp.'classes.update', $class->id) : route($rp.'classes.store') }}" class="space-y-3">
    @csrf
    @if ($edit) @method('PUT') @endif

    <div class="grid grid-cols-2 gap-3">
        <div><label class="mb-1 block text-xs font-semibold text-slate-500">Nama kelas *</label>
            <input name="name" value="{{ old('name', $class->name) }}" placeholder="mis. 1A" required class="input"></div>
        <div><label class="mb-1 block text-xs font-semibold text-slate-500">Tingkat (1–12) *</label>
            <input type="number" min="1" max="12" name="grade_level" value="{{ old('grade_level', $class->grade_level) }}" required class="input"></div>
    </div>

    <div><label class="mb-1 block text-xs font-semibold text-slate-500">Tahun ajaran *</label>
        <input name="academic_year" value="{{ old('academic_year', $class->academic_year) }}" placeholder="2026/2027" required class="input"></div>

    <div><label class="mb-1 block text-xs font-semibold text-slate-500">Wali kelas</label>
        <select name="homeroom_teacher_id" class="input">
            <option value="">— belum ditentukan —</option>
            @foreach ($teachers as $t)
                <option value="{{ $t->id }}" @selected(old('homeroom_teacher_id', $class->homeroom_teacher_id) == $t->id)>{{ $t->name }}</option>
            @endforeach
        </select></div>

    <label class="flex items-center gap-2 rounded-xl bg-white px-3 py-3 text-sm ring-1 ring-slate-200">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $class->is_active)) class="h-4 w-4"> Kelas aktif
    </label>

    <button class="btn btn-brand w-full py-3">Simpan</button>
</form>

@if ($edit)
    <form method="POST" action="{{ route($rp.'classes.destroy', $class->id) }}" class="mt-3" onsubmit="return confirm('Hapus kelas ini?')">
        @csrf @method('DELETE')
        <button class="btn btn-danger w-full">Hapus kelas</button>
    </form>
@endif
@endsection

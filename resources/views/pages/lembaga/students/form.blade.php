@extends('layouts.admin')
@php $edit = $student->exists; @endphp
@section('title', $edit ? 'Edit Murid' : 'Tambah Murid')
@section('width', 'max-w-3xl')
@section('actions')
    <a href="{{ route($rp.'students.index') }}" class="btn btn-line"><i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali</a>
@endsection

@section('content')
<form method="POST" action="{{ $edit ? route($rp.'students.update', $student->id) : route($rp.'students.store') }}" class="card space-y-4 p-6">
    @csrf
    @if ($edit) @method('PUT') @endif

    <div class="grid gap-4 md:grid-cols-2">
        <div><label class="label">NIS *</label><input name="nis" value="{{ old('nis', $student->nis) }}" required class="input"></div>
        <div><label class="label">NISN</label><input name="nisn" value="{{ old('nisn', $student->nisn) }}" class="input"></div>
    </div>

    <div><label class="label">Nama lengkap *</label><input name="name" value="{{ old('name', $student->name) }}" required class="input"></div>

    <div class="grid gap-4 md:grid-cols-2">
        <div><label class="label">Jenis kelamin *</label>
            <select name="gender" class="input">
                <option value="L" @selected(old('gender', $student->gender) === 'L')>Laki-laki</option>
                <option value="P" @selected(old('gender', $student->gender) === 'P')>Perempuan</option>
            </select></div>
        <div><label class="label">Kelas</label>
            <select name="class_id" class="input">
                <option value="">— belum ada —</option>
                @foreach ($classes as $c)
                    <option value="{{ $c->id }}" @selected(old('class_id', $student->class_id) == $c->id)>{{ $c->name }} ({{ $c->academic_year }})</option>
                @endforeach
            </select></div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div><label class="label">Tempat lahir</label><input name="birth_place" value="{{ old('birth_place', $student->birth_place) }}" class="input"></div>
        <div><label class="label">Tanggal lahir</label><input type="date" name="birth_date" value="{{ old('birth_date', optional($student->birth_date)->format('Y-m-d')) }}" class="input"></div>
        <div><label class="label">Tanggal masuk</label><input type="date" name="enrolled_at" value="{{ old('enrolled_at', optional($student->enrolled_at)->format('Y-m-d')) }}" class="input"></div>
    </div>

    <div><label class="label">Alamat</label><textarea name="address" rows="2" class="input">{{ old('address', $student->address) }}</textarea></div>

    <div class="flex flex-wrap gap-3">
        @if ($company->hasBoarding())
            <label class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
                <input type="checkbox" name="is_boarding" value="1" @checked(old('is_boarding', $student->is_boarding)) class="h-4 w-4"> Tinggal di asrama (mondok)
            </label>
        @endif
        <label class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $student->is_active)) class="h-4 w-4"> Murid aktif
        </label>
    </div>

    <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
        <a href="{{ route($rp.'students.index') }}" class="btn btn-line">Batal</a>
        <button class="btn btn-brand px-6">Simpan</button>
    </div>
</form>

@if ($edit)
    <form method="POST" action="{{ route($rp.'students.destroy', $student->id) }}" class="mt-4 text-right"
          onsubmit="return confirm('Hapus murid ini? Histori absennya tetap tersimpan.')">
        @csrf @method('DELETE')
        <button class="btn btn-danger"><i data-lucide="trash-2" class="h-4 w-4"></i> Hapus murid</button>
    </form>
@endif
@endsection

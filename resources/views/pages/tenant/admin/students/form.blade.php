@extends('layouts.mobile')
@php $edit = $student->exists; @endphp
@section('title', $edit ? 'Edit Murid' : 'Tambah Murid')
@section('back', route($rp.'students.index'))

@section('content')
<form method="POST" action="{{ $edit ? route($rp.'students.update', $student->id) : route($rp.'students.store') }}" class="space-y-3">
    @csrf
    @if ($edit) @method('PUT') @endif

    <div class="grid grid-cols-2 gap-3">
        <div><label class="mb-1 block text-xs font-semibold text-slate-500">NIS *</label>
            <input name="nis" value="{{ old('nis', $student->nis) }}" required class="input"></div>
        <div><label class="mb-1 block text-xs font-semibold text-slate-500">NISN</label>
            <input name="nisn" value="{{ old('nisn', $student->nisn) }}" class="input"></div>
    </div>

    <div><label class="mb-1 block text-xs font-semibold text-slate-500">Nama lengkap *</label>
        <input name="name" value="{{ old('name', $student->name) }}" required class="input"></div>

    <div class="grid grid-cols-2 gap-3">
        <div><label class="mb-1 block text-xs font-semibold text-slate-500">Jenis kelamin *</label>
            <select name="gender" class="input">
                <option value="L" @selected(old('gender', $student->gender) === 'L')>Laki-laki</option>
                <option value="P" @selected(old('gender', $student->gender) === 'P')>Perempuan</option>
            </select></div>
        <div><label class="mb-1 block text-xs font-semibold text-slate-500">Kelas</label>
            <select name="class_id" class="input">
                <option value="">— belum ada —</option>
                @foreach ($classes as $c)
                    <option value="{{ $c->id }}" @selected(old('class_id', $student->class_id) == $c->id)>{{ $c->name }} ({{ $c->academic_year }})</option>
                @endforeach
            </select></div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div><label class="mb-1 block text-xs font-semibold text-slate-500">Tempat lahir</label>
            <input name="birth_place" value="{{ old('birth_place', $student->birth_place) }}" class="input"></div>
        <div><label class="mb-1 block text-xs font-semibold text-slate-500">Tanggal lahir</label>
            <input type="date" name="birth_date" value="{{ old('birth_date', optional($student->birth_date)->format('Y-m-d')) }}" class="input"></div>
    </div>

    <div><label class="mb-1 block text-xs font-semibold text-slate-500">Alamat</label>
        <textarea name="address" rows="2" class="input">{{ old('address', $student->address) }}</textarea></div>

    <div><label class="mb-1 block text-xs font-semibold text-slate-500">Tanggal masuk</label>
        <input type="date" name="enrolled_at" value="{{ old('enrolled_at', optional($student->enrolled_at)->format('Y-m-d')) }}" class="input"></div>

    @if ($company->hasBoarding())
        <label class="flex items-center gap-2 rounded-xl bg-white px-3 py-3 text-sm ring-1 ring-slate-200">
            <input type="checkbox" name="is_boarding" value="1" @checked(old('is_boarding', $student->is_boarding)) class="h-4 w-4"> Tinggal di asrama (mondok)
        </label>
    @endif
    <label class="flex items-center gap-2 rounded-xl bg-white px-3 py-3 text-sm ring-1 ring-slate-200">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $student->is_active)) class="h-4 w-4"> Murid aktif
    </label>

    <button class="btn btn-brand w-full py-3">Simpan</button>
</form>

@if ($edit)
    <form method="POST" action="{{ route($rp.'students.destroy', $student->id) }}" class="mt-3"
          onsubmit="return confirm('Hapus murid ini? Histori absennya tetap tersimpan.')">
        @csrf @method('DELETE')
        <button class="btn btn-danger w-full">Hapus murid</button>
    </form>
@endif
@endsection

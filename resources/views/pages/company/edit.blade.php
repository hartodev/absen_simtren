@extends('layouts.company')

@section('title', 'Edit Karyawan')

@section('content')
<header class="sticky top-0 z-10 bg-ink px-5 py-5 text-white lg:px-8">
    <div class="flex items-center gap-3">
        <a href="{{ route('company.employees.index') }}" class="text-xl leading-none" aria-label="Kembali">←</a>
        <h1 class="text-xl font-bold">Edit Karyawan</h1>
    </div>
</header>

<main class="flex-1 px-5 py-6 lg:px-8">
    <div class="max-w-xl rounded-xl border border-black/5 bg-white p-6">
        @if ($errors->any())
        <div class="mb-5 rounded-lg border border-bad/20 bg-bad-bg px-4 py-3 text-sm text-bad">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('company.employees.update', $karyawan->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="mb-1 block text-sm font-medium">Nama</label>
                <input type="text" name="name" value="{{ old('name', $karyawan->name) }}" required
                    class="w-full rounded-lg border border-black/10 px-3.5 py-2.5 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email', $karyawan->email) }}" required
                    class="w-full rounded-lg border border-black/10 px-3.5 py-2.5 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $karyawan->phone) }}"
                    class="w-full rounded-lg border border-black/10 px-3.5 py-2.5 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-sm font-medium">Department</label>
                    <input type="text" name="department" value="{{ old('department', $karyawan->department) }}" required
                        class="w-full rounded-lg border border-black/10 px-3.5 py-2.5 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Position</label>
                    <input type="text" name="position" value="{{ old('position', $karyawan->position) }}" required
                        class="w-full rounded-lg border border-black/10 px-3.5 py-2.5 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20">
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">Password baru</label>
                <input type="password" name="password" placeholder="Kosongkan kalau tidak ingin mengubah password"
                    class="w-full rounded-lg border border-black/10 px-3.5 py-2.5 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20">
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="rounded-lg bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-dark">
                    Simpan Perubahan
                </button>
                <a href="{{ route('company.employees.index') }}" class="text-sm text-ink/60 hover:underline">Batal</a>
            </div>
        </form>
    </div>
</main>
@endsection
@extends('layouts.company')

@section('title', 'Settings Attendance')

@section('content')
<div class="min-h-screen bg-gray-50">

    <div class="bg-blue-800 px-5 pt-6 pb-8 rounded-b-3xl">
        <div class="flex items-center gap-3">
            <a href="{{ route('company.dashboard') }}" class="text-white">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-white text-xl font-bold">Settings Attendance</h1>
        </div>
        <p class="text-blue-200 text-sm mt-1 ml-8">Atur lokasi company, radius, dan jam kerja</p>
    </div>

    <div class="px-5 -mt-4 pb-6">

        @if(session('success'))
        <div class="bg-green-100 text-green-700 text-sm rounded-xl px-4 py-3 mb-4">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-100 text-red-700 text-sm rounded-xl px-4 py-3 mb-4">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('company.attendances.settings.update') }}"
            class="bg-white rounded-2xl p-5 shadow-sm space-y-5">
            @csrf
            @method('PUT')

            <div>
                <h3 class="font-semibold text-gray-900 mb-3">Lokasi Company (Geofence)</h3>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Latitude</label>
                        <input type="text" name="latitude" value="{{ old('latitude', $company->latitude) }}"
                            placeholder="-7.797068" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm"
                            required>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Longitude</label>
                        <input type="text" name="longitude" value="{{ old('longitude', $company->longitude) }}"
                            placeholder="110.370529" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm"
                            required>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="block text-xs text-gray-500 mb-1">Radius (km)</label>
                    <input type="number" step="0.01" min="0.01" max="50" name="radius_km"
                        value="{{ old('radius_km', $company->radius_km) }}"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm" required>
                    <p class="text-xs text-gray-400 mt-1">Karyawan hanya bisa check-in/out dalam radius ini dari titik
                        lokasi di atas.</p>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4">
                <h3 class="font-semibold text-gray-900 mb-3">Jam Kerja Default</h3>
                <p class="text-xs text-gray-400 mb-3">Dipakai kalau karyawan tidak punya shift khusus.</p>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Jam Masuk</label>
                        <input type="time" name="time_in"
                            value="{{ old('time_in', $company->time_in ? \Illuminate\Support\Str::substr($company->time_in, 0, 5) : '') }}"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Jam Pulang</label>
                        <input type="time" name="time_out"
                            value="{{ old('time_out', $company->time_out ? \Illuminate\Support\Str::substr($company->time_out, 0, 5) : '') }}"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-800 text-white rounded-xl px-4 py-3 text-sm font-semibold">
                Simpan Pengaturan
            </button>
        </form>
    </div>
</div>
@endsection
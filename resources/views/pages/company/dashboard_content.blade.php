{{-- Stat Cards --}}
<div class="grid grid-cols-2 gap-3">
    <div class="bg-white rounded-2xl p-4 shadow-sm">
        <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-3-6.65" />
            </svg>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_karyawan'] }}</p>
        <p class="text-sm text-gray-500">Total Karyawan</p>
    </div>

    <div class="bg-white rounded-2xl p-4 shadow-sm">
        <div class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ $stats['hadir_hari_ini'] }}</p>
        <p class="text-sm text-gray-500">Hadir Hari Ini</p>
    </div>

    <div class="bg-white rounded-2xl p-4 shadow-sm">
        <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ $stats['izin_pending'] }}</p>
        <p class="text-sm text-gray-500">Izin Pending</p>
    </div>

    <div class="bg-white rounded-2xl p-4 shadow-sm">
        <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ $stats['terlambat'] }}</p>
        <p class="text-sm text-gray-500">Terlambat</p>
    </div>
</div>

{{-- Absensi Terbaru --}}
<div class="mt-6">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-lg font-bold text-gray-900">Absensi Terbaru</h2>
        <a href="{{ route('company.attendances.index') }}" class="text-sm font-medium text-blue-700">Lihat Semua</a>
    </div>

    <div class="space-y-3">
        @forelse($recentAttendances as $item)
        <div class="bg-white rounded-2xl p-4 shadow-sm flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-full bg-blue-800 text-white flex items-center justify-center font-semibold shrink-0">
                {{ strtoupper(substr($item['name'], 0, 1)) }}
            </div>

            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-900 truncate">{{ $item['name'] }}</p>

                <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $item['checked_out'] ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ $item['checked_out'] ? 'Sudah Checkout' : 'Belum Checkout' }}
                </span>
            </div>

            <div class="text-right shrink-0">
                <span class="inline-flex items-center gap-1 text-green-600 text-sm font-medium">
                    &rarr; {{ \Illuminate\Support\Str::substr($item['time_in'], 0, 8) }}
                </span>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-400 text-center py-6">Belum ada karyawan yang check-in hari ini.</p>
        @endforelse
    </div>
</div>

{{-- Izin Menunggu Approval --}}
<div class="mt-6 pb-6">
    <div class="flex items-center gap-2 mb-3">
        <h2 class="text-lg font-bold text-gray-900">Izin Menunggu Approval</h2>
        @if(count($pendingPermissions) > 0 || $stats['izin_pending'] > 0)
        <span class="bg-red-500 text-white text-xs font-semibold rounded-full w-5 h-5 flex items-center justify-center">
            {{ $stats['izin_pending'] }}
        </span>
        @endif
    </div>

    <div class="space-y-3">
        @forelse($pendingPermissions as $perm)
        <a href="{{ route('company.permissions.show', $perm['id']) }}" class="block bg-white rounded-2xl p-4 shadow-sm">
            <div class="flex items-center justify-between mb-1">
                <p class="font-semibold text-gray-900">{{ $perm['name'] }}</p>
                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                    Menunggu
                </span>
            </div>
            <p class="text-sm text-gray-500">{{ $perm['reason'] }}</p>
        </a>
        @empty
        <p class="text-sm text-gray-400 text-center py-6">Tidak ada izin yang menunggu approval.</p>
        @endforelse
    </div>
</div>
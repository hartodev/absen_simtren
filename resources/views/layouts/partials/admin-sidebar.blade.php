{{-- Menu samping. Ubah daftar menu di sini (satu tempat). Format: [label, icon, route, pola-route-aktif] --}}
@php
    $nav = $isGreen
        ? [
            'Utama' => [
                ['Beranda',    'home',            $rp.'dashboard',          [$rp.'dashboard']],
                ['Kehadiran',  'fingerprint',     $rp.'attendances.index',  [$rp.'attendances.index', $rp.'attendances.show']],
                ["Mutaba'ah",  'book-open-check', $rp.'mutabaah.index',     [$rp.'mutabaah.*']],
                ['Santri',     'users',           $rp.'employees.index',    [$rp.'employees.*']],
            ],
            'Pengaturan' => [
                ['Pengaturan Absensi', 'settings', $rp.'attendances.settings', [$rp.'attendances.settings*']],
                ['Profil & Akun',      'user',     $rp.'profile',              [$rp.'profile']],
            ],
        ]
        : array_filter([
            'Utama' => [
                ['Beranda', 'layout-dashboard', $rp.'dashboard', [$rp.'dashboard', $rp.'modules.*']],
            ],
            'Akademik' => [
                ['Data Murid',    'graduation-cap', $rp.'students.index',            [$rp.'students.*']],
                ['Data Kelas',    'book-marked',    $rp.'classes.index',             [$rp.'classes.*']],
                ['Rekap Absensi', 'bar-chart-3',    $rp.'student-attendances.index', [$rp.'student-attendances.*']],
                ['Izin Siswa',    'clipboard-list', $rp.'student-permissions.index', [$rp.'student-permissions.*']],
            ],
            'Kesantrian' => $company->hasBoarding() ? [
                ['Izin Keluar/Pulang', 'footprints', $rp.'boarding.index', [$rp.'boarding.*']],
            ] : null,
            'Perangkat' => [
                ['Device Kiosk', 'tablet', $rp.'devices.index', [$rp.'devices.*']],
            ],
            'Akun' => [
                ['Profil', 'user', $rp.'profile', [$rp.'profile']],
            ],
        ]);
@endphp

<aside id="sidebar" class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform duration-200 lg:translate-x-0">
    <div class="flex h-16 shrink-0 items-center gap-3 border-b border-slate-100 px-5">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl text-white" style="background:var(--brand)">
            <i data-lucide="{{ $isGreen ? 'book-open-check' : 'school' }}" class="h-5 w-5"></i>
        </span>
        <div class="min-w-0 leading-tight">
            <p class="truncate text-sm font-bold text-slate-800">{{ $company->name }}</p>
            <p class="text-[11px] text-slate-500">{{ $isGreen ? 'Admin TPQ' : ($company->type === 'school' ? 'Admin Sekolah' : 'Admin Pesantren') }}</p>
        </div>
    </div>

    <nav class="flex-1 space-y-5 overflow-y-auto px-3 py-4">
        @foreach ($nav as $group => $items)
            <div>
                <p class="mb-1.5 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $group }}</p>
                <ul class="space-y-0.5">
                    @foreach ($items as [$label, $icon, $target, $patterns])
                        @php $active = request()->routeIs(...$patterns); @endphp
                        <li>
                            <a href="{{ route($target) }}"
                               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition {{ $active ? '' : 'text-slate-600 hover:bg-slate-50' }}"
                               style="{{ $active ? 'background:var(--brand-soft);color:var(--brand)' : '' }}">
                                <i data-lucide="{{ $icon }}" class="h-[18px] w-[18px] shrink-0"></i>
                                <span class="truncate">{{ $label }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>

    <form method="POST" action="{{ route('tenant.logout') }}" class="shrink-0 border-t border-slate-100 p-3">
        @csrf
        <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
            <i data-lucide="log-out" class="h-[18px] w-[18px]"></i> Keluar
        </button>
    </form>
</aside>

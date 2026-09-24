<?php

namespace App\Support;

use App\Models\Company;

/**
 * Daftar kartu di grid biru (Beranda Admin sekolah umum & pondok pesantren).
 *
 * Urutan sama dengan screenshot. Mau tambah / hapus / ganti urutan kartu?
 * Cukup edit array di all() ini -- tidak perlu menyentuh view.
 *
 *  - route : nama route TANPA prefix tipe (mis. 'students.index'). Kalau null,
 *            kartu membuka halaman placeholder rapi (modules/{slug}).
 *  - table : tabel sumber untuk hitung jumlah data di halaman placeholder.
 *  - boarding : true = hanya tampil kalau lembaga punya asrama.
 */
class SchoolModules
{
    public static function all(): array
    {
        return [
            ['slug' => 'guru-wali',          'label' => 'Guru & Wali',                 'icon' => 'users',           'color' => '#3949ab', 'route' => null, 'table' => 'class_teachers'],
            ['slug' => 'data-kelas',         'label' => 'Data Kelas',                  'icon' => 'book-marked',     'color' => '#00897b', 'route' => 'classes.index'],
            ['slug' => 'data-murid',         'label' => 'Data Murid',                  'icon' => 'graduation-cap',  'color' => '#3949ab', 'route' => 'students.index'],
            ['slug' => 'mutasi-siswa',       'label' => 'Mutasi Siswa',                'icon' => 'arrow-left-right','color' => '#546e7a', 'route' => null, 'table' => 'student_mutations'],
            ['slug' => 'ppdb',               'label' => 'PPDB',                        'icon' => 'user-plus',       'color' => '#00897b', 'route' => null, 'table' => 'ppdb_applicants'],
            ['slug' => 'mata-pelajaran',     'label' => 'Mata Pelajaran',              'icon' => 'book-open',       'color' => '#0097a7', 'route' => null, 'table' => 'subjects'],
            ['slug' => 'kurikulum',          'label' => 'Kurikulum',                   'icon' => 'library',         'color' => '#f4511e', 'route' => null, 'table' => 'curricula'],
            ['slug' => 'kalender-akademik',  'label' => 'Kalender Akademik',           'icon' => 'calendar-days',   'color' => '#d81b60', 'route' => null, 'table' => 'academic_events'],
            // Dua kartu di antara "Kalender Akademik" dan "Kesantrian" tidak terlihat penuh di screenshot; ini tebakan dari tabel yang sudah ada. Ganti kalau beda.
            ['slug' => 'jadwal-pelajaran',   'label' => 'Jadwal Pelajaran',            'icon' => 'clock',           'color' => '#6d4c41', 'route' => null, 'table' => 'lesson_schedules'],
            ['slug' => 'materi-tugas',       'label' => 'Materi & Tugas',              'icon' => 'clipboard-list',  'color' => '#43a047', 'route' => null, 'table' => 'assignments'],
            ['slug' => 'kesantrian-asrama',  'label' => 'Kesantrian & Asrama',         'icon' => 'bed-double',      'color' => '#6d4c41', 'route' => null, 'table' => 'dormitories', 'boarding' => true],
            ['slug' => 'izin-keluar-pulang', 'label' => 'Izin Keluar/Pulang Santri',   'icon' => 'footprints',      'color' => '#5e35b1', 'route' => 'boarding.index', 'boarding' => true],
            ['slug' => 'device-kiosk',       'label' => 'Device Kiosk',                'icon' => 'tablet',          'color' => '#f4511e', 'route' => 'devices.index'],
            ['slug' => 'mode-kiosk',         'label' => 'Mode Kiosk',                  'icon' => 'smartphone',      'color' => '#546e7a', 'route' => null, 'table' => 'attendance_devices'],
            ['slug' => 'rekap-absensi',      'label' => 'Rekap Absensi',               'icon' => 'bar-chart-3',     'color' => '#43a047', 'route' => 'student-attendances.index'],
            ['slug' => 'rekap-nilai',        'label' => 'Rekap Nilai',                 'icon' => 'star',            'color' => '#f9a825', 'route' => null, 'table' => 'grades'],
            ['slug' => 'kenaikan-kelas',     'label' => 'Kenaikan Kelas',              'icon' => 'trending-up',     'color' => '#5e35b1', 'route' => null, 'table' => 'class_promotions'],
            ['slug' => 'pengumuman',         'label' => 'Pengumuman',                  'icon' => 'megaphone',       'color' => '#e53935', 'route' => null, 'table' => null],
            ['slug' => 'jenis-tagihan',      'label' => 'Jenis Tagihan',               'icon' => 'shapes',          'color' => '#795548', 'route' => null, 'table' => 'bill_types'],
            ['slug' => 'tagihan-siswa',      'label' => 'Tagihan Siswa',               'icon' => 'receipt',         'color' => '#43a047', 'route' => null, 'table' => 'student_bills'],
            ['slug' => 'laporan-keuangan',   'label' => 'Laporan Keuangan',            'icon' => 'bar-chart-3',     'color' => '#607d8b', 'route' => null, 'table' => 'bill_payments'],
            ['slug' => 'uks-kesehatan',      'label' => 'UKS / Kesehatan',             'icon' => 'cross',           'color' => '#e53935', 'route' => null, 'table' => 'uks_visits'],
            ['slug' => 'ekstrakurikuler',    'label' => 'Ekstrakurikuler',             'icon' => 'users',           'color' => '#3949ab', 'route' => null, 'table' => 'extracurriculars'],
            ['slug' => 'pesan',              'label' => 'Pesan',                       'icon' => 'message-square',  'color' => '#546e7a', 'route' => null, 'table' => 'chat_conversations', 'hidden' => true],
            ['slug' => 'prestasi-siswa',     'label' => 'Prestasi Siswa',              'icon' => 'trophy',          'color' => '#f9a825', 'route' => null, 'table' => 'student_achievements'],
        ];
    }

    /** Modul yang boleh tampil untuk lembaga ini (sembunyikan asrama untuk sekolah umum). */
    public static function forCompany(Company $company): array
    {
        $hasBoarding = $company->hasBoarding();
        $prefix = $company->type;

        return collect(self::all())
            ->reject(fn ($m) => ! empty($m['boarding']) && ! $hasBoarding)
            ->reject(fn ($m) => ! empty($m['hidden']))
            ->map(function ($m) use ($prefix) {
                $m['url'] = $m['route']
                    ? route($prefix . '.' . $m['route'])
                    : route($prefix . '.modules.show', $m['slug']);
                return $m;
            })
            ->values()
            ->all();
    }

    public static function find(string $slug): ?array
    {
        return collect(self::all())->firstWhere('slug', $slug);
    }
}

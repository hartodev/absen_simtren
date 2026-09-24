# Ringkasan Perbaikan Migration

Dari 133 file migration, ditemukan **5 migration `create_table` yang duplikat** —
mencoba membuat tabel yang sebenarnya sudah dibuat sebelumnya oleh migration lain
(dengan timestamp lebih lama). Migration seperti ini akan **error** saat dijalankan
(`Schema::create` pada tabel yang sudah ada), jadi tidak pernah benar-benar sukses jalan.

## Tabel yang dibuat dua kali & keputusan untuk masing-masing

| Tabel | Migration lama (asli) | Migration baru (duplikat) | Ada field penting baru? | Tindakan |
|---|---|---|---|---|
| `companies` | 2024_10_11 | 2026_09_17_500000 | Tidak — `type` & `timezone` sudah ditambahkan lewat `2025_11_09_add_typecompany_to_companies_table` | **Dihapus** |
| `shifts` | 2025_11_03 | 2026_09_18_200000 | Tidak — hanya beda default value | **Dihapus** |
| `attendances` | 2025_04_20 | 2026_09_18_200001 | Tidak — semua kolom (`company_id`, `shift_id`, `scheduled_in/out`, dst) sudah ada lewat `add_attendance_fields_complete` & migration alter lain | **Dihapus** |
| `permissions` | 2025_04_20 | 2026_09_18_200002 | **Ya** — kolom `type`, `approved_by`, `approved_at` belum pernah ada | Dihapus, field pentingnya dipindah ke migration baru: `2026_09_19_000001_add_type_and_approval_fields_to_permissions_table.php` |
| `user_shift_overrides` | 2026_02_28 | 2026_09_18_200003 | Tidak — versi baru malah kehilangan kolom `company_id`, `created_by`, `reason` | **Dihapus** |

## Temuan tambahan (bonus, di luar 5 duplikat di atas)

Migration `2026_09_18_100000_rename_tenant_id_to_company_id_on_users_table.php` dibuat
dengan asumsi kolom `company_id` di tabel `users` belum ada — padahal kolom itu sudah
dibuat sejak `2025_11_03_155515_add_companyid_to_users_table.php`. Karena ada guard
`if (... && !Schema::hasColumn('users', 'company_id'))`, migration rename tersebut
**selalu no-op** dan kolom `tenant_id` yang seharusnya di-merge malah tetap tertinggal
sebagai kolom terpisah dan redundan.

Migration lama tidak diubah (karena bisa saja sudah pernah jalan di production).
Sebagai gantinya ditambahkan migration baru:
`2026_09_19_000002_merge_tenant_id_into_company_id_on_users_table.php` — ini memindahkan
data dari `tenant_id` ke `company_id` (untuk baris yang `company_id`-nya masih kosong),
lalu menghapus kolom `tenant_id` yang sudah tidak dipakai.

## Hasil akhir

- 133 file → **130 file** (5 dihapus, 2 ditambahkan)
- Tidak ada lagi `Schema::create()` ganda untuk tabel yang sama
- Urutan timestamp semua migration lain sudah benar secara dependency (sudah dicek
  otomatis: tidak ada migration yang mengubah tabel sebelum tabel itu dibuat)

## Bug urutan kolom (`->after('kolom_yang_belum_ada')`) — ditemukan dari error asli

Setelah dites langsung (`php artisan migrate`), ketemu 2 migration yang menaruh kolom baru
dengan `->after('nama_kolom')` padahal kolom itu **belum dibuat** di titik waktu tersebut
(baru dibuat oleh migration lain yang timestamp-nya lebih belakangan):

| File | `->after(...)` yang bermasalah | Kolom itu baru dibuat di | Perbaikan |
|---|---|---|---|
| `2026_03_01_141538_add_salary_in_to_users_table.php` | `after('department')` | **tidak pernah dibuat sama sekali** | Kolom `department` dibuat di migration ini juga (sebelum `salary`), keduanya dibungkus `hasColumn` guard |
| `2026_08_20_145219_add_is_active_to_users_table.php` | `after('role')` | `2026_09_16_000000_add_role_to_users_table.php` (lebih belakangan) | `->after('role')` dihapus (posisi kolom tidak krusial) |

Semua 133 file juga sudah disimulasikan urutan pembuatan tabel & kolomnya secara program
(bukan cuma dibaca manual) untuk memastikan tidak ada lagi migration yang mereferensikan
kolom/tabel yang belum ada di titik waktu itu.

## Bug kolom wajib isi tanpa default (`companies` table)

Setelah migration jalan lancar, muncul error baru saat **daftar perusahaan**:
`Field 'email' doesn't have a default value`. Ternyata tabel `companies` dibuat dengan
7 kolom **NOT NULL tanpa default**: `email`, `address`, `latitude`, `longitude`,
`radius_km`, `time_in`, `time_out` — sedangkan form pendaftaran (`RegisterController`)
sekarang hanya mengisi `name`, `subdomain`, `type`, `status` saat company dibuat (data
profil lengkap diisi belakangan).

Ditambahkan migration baru: `2026_09_20_000000_make_optional_company_profile_fields_nullable.php`
- `email`, `address`, `latitude`, `longitude` → dibuat **nullable**
- `radius_km`, `time_in`, `time_out` → diberi **default** (`0.5`, `08:00`, `17:00`) karena
  dipakai untuk logika absensi (radius lokasi & jam kerja), jadi lebih aman punya nilai
  default daripada `NULL`.

## Catatan penting

Migration yang dihapus (`create_companies`, `create_shifts`, `create_attendances`,
`create_permissions`, `create_user_shift_overrides` yang duplikat) hampir pasti **belum
pernah berhasil jalan** di database manapun, karena `Schema::create()` pada tabel yang
sudah ada akan langsung melempar error — jadi menghapusnya aman.

Jika di server production ada baris di tabel `migrations` yang mencatat file-file
tersebut sebagai "sudah jalan" (misalnya karena batch migrate gagal di tengah), cek dulu
riwayatnya sebelum deploy, supaya tidak bingung saat `php artisan migrate` tidak
menemukan filenya lagi.

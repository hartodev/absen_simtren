<?php

// Tipe organisasi yang bisa mendaftar mandiri lewat /daftar.
// 'admin_role' = role user pertama (pendaftar/pemilik akun organisasi).
return [
    'company' => [
        'label' => 'Perusahaan / Kantor',
        'admin_role' => 'hr',
        'istilah_anggota' => 'Karyawan',
    ],
    'pesantren' => [
        'label' => 'TPQ / Pesantren',
        'admin_role' => 'ustadz',
        'istilah_anggota' => 'Santri',
    ],
    'school' => [
        'label' => 'Sekolah',
        'admin_role' => 'teacher',
        'istilah_anggota' => 'Siswa',
    ],
];

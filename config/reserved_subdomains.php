<?php

// Subdomain yang tidak boleh dipakai/didaftarkan oleh tenant baru,
// karena dipakai sistem, berpotensi disalahgunakan, atau membingungkan.
return [
    'www', 'mail', 'ftp', 'smtp', 'pop', 'imap', 'webmail',
    'admin', 'administrator', 'superadmin', 'root',
    'api', 'app', 'apps', 'portal', 'dashboard',
    'login', 'logout', 'register', 'daftar', 'auth',
    'static', 'cdn', 'assets', 'storage', 'files', 'media',
    'test', 'staging', 'dev', 'demo', 'beta', 'localhost',
    'support', 'help', 'docs', 'blog', 'status', 'ns1', 'ns2',
];

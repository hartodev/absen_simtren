# Panduan Setup VPS Production — Wildcard Subdomain absen_simtren

Domain: **ptutamacta.com** (wildcard: `*.ptutamacta.com`)

## 1. Siapkan kode di server

```bash
cd /var/www
sudo mkdir absen-simtren && sudo chown $USER:$USER absen-simtren
# upload/clone project ke sini, lalu:
cd absen-simtren
composer install --optimize-autoloader --no-dev
cp .env.production .env
php artisan key:generate
```

## 2. Isi `.env`

Pakai `deploy/.env.production.example` sebagai contoh, lalu sesuaikan minimal:

- `DB_*` — kredensial MySQL production
- `MAIL_*` — SMTP asli (WAJIB, karena aktivasi tenant lewat email)
- `APP_URL=https://ptutamacta.com`
- `APP_MAIN_DOMAIN=ptutamacta.com`

## 3. Migrasi & permission

```bash
php artisan migrate --force
php artisan storage:link
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 4. Nginx + PHP-FPM

```bash
sudo cp deploy/nginx-absen-simtren.conf /etc/nginx/sites-available/absen-simtren
sudo ln -s /etc/nginx/sites-available/absen-simtren /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

Sesuaikan path socket PHP-FPM di file config (`php8.3-fpm.sock`) dengan versi PHP yang terpasang di VPS (`php -v`).

## 5. DNS wildcard di Cloudflare

Di dashboard Cloudflare, tab **DNS**, tambahkan (kalau pakai Cloudflare Tunnel, ini otomatis dibuat sebagai CNAME ke tunnel — lihat langkah 6). Kalau TIDAK pakai tunnel dan langsung expose IP VPS:

- Type `A`, Name `@`, Content `<IP_VPS>`, Proxy: ON
- Type `A`, Name `*`, Content `<IP_VPS>`, Proxy: ON

## 6. Cloudflare Tunnel (disarankan — tidak perlu buka port firewall)

```bash
cloudflared tunnel login
cloudflared tunnel create absen-simtren
cloudflared tunnel route dns absen-simtren ptutamacta.com
cloudflared tunnel route dns absen-simtren "*.ptutamacta.com"
```

Salin `deploy/cloudflared-config.yml` ke `/etc/cloudflared/config.yml`, isi `tunnel:` dan `credentials-file:` sesuai hasil `cloudflared tunnel create`, lalu:

```bash
sudo cloudflared service install
sudo systemctl start cloudflared
sudo systemctl enable cloudflared
```

## 7. Cek SSL/TLS mode di Cloudflare

Dashboard → SSL/TLS → set ke **Full** (bukan Flexible), karena origin (Nginx) melayani via HTTP lokal di belakang tunnel — Cloudflare yang menangani HTTPS ke publik.

## 8. Verifikasi

```bash
curl -I https://ptutamacta.com
curl -I https://tespendaftaran.ptutamacta.com   # harus dapat halaman "tidak ditemukan" (404), bukan error 5xx
```

Lalu coba alur nyata: daftar di `https://ptutamacta.com/daftar` → cek email aktivasi masuk → klik link → otomatis diarahkan ke `https://<subdomain>.ptutamacta.com/login` → login.

## 9. Superadmin pertama

Karena belum ada UI untuk membuat superadmin pertama, buat manual sekali via tinker:

```bash
php artisan tinker
>>> $t = App\Models\Tenant::create(['nama_lembaga' => 'Superadmin', 'subdomain' => 'internal-superadmin', 'status' => 'aktif']);
>>> App\Models\User::create(['name' => 'Superadmin', 'email' => 'superadmin@ptutamacta.com', 'password' => bcrypt('GANTI_PASSWORD_INI'), 'tenant_id' => $t->id, 'role' => 'superadmin']);
```

<!-- Login superadmin -->

Login afer sub domain
lewat `https://ptutamacta.com/superadmin/login`.

## Catatan penting

- `subdomain` di config `reserved_subdomains.php` sudah memblokir `www`, `admin`, `api`, dll — tambahkan kata lain di situ kalau perlu.
- `TrustProxies` sudah diset `'*'` karena cloudflared jalan lokal (percaya proxy lokal itu aman); JANGAN pakai setelan ini kalau Nginx nanti diekspos langsung ke publik tanpa tunnel/CDN di depannya.
- Untuk dev lokal di Laragon (Windows), tambahkan di `hosts` file: `127.0.0.1 ptutamacta.test` dan buat virtual host wildcard `*.ptutamacta.test` di Laragon (Menu kanan-klik tray Laragon → Apache/Nginx → sites-enabled, atau pakai fitur "Auto Virtual Hosts" bawaan Laragon yang otomatis wildcard per folder).

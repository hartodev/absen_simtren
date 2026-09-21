# Panduan Wildcard Subdomain — absen_simtren

## Kabar baik dulu

Kode routing di `absen_simtren` **sudah dinamis dari awal**, tidak ada subdomain
yang hardcode:

```php
Route::domain('{subdomain}.' . $mainDomain)->middleware('detect.tenant')->group(...)
```

`DetectTenant` middleware baca `$request->getHost()` langsung, cari tenant di
database berdasarkan itu. Jadi berbeda dengan project `wildcard-subdomain-test`
yang kamu upload (yang memang dibuat dari nol buat *membuktikan* pola ini bisa
jalan), **kamu tidak perlu ubah kode Laravel sama sekali** supaya subdomain
baru otomatis dikenali. Yang selama ini "manual" itu murni soal **DNS** —
komputer/servermu belum tahu ke mana harus mengarahkan `apapun.ptutamacta.test`
atau `apapun.ptutamacta.com`, makanya harus diberitahu satu-satu lewat file
hosts.

Jadi ada 2 pekerjaan terpisah di bawah ini:
1. **Local dev (Windows)** — supaya kamu berhenti edit hosts file tiap ada tenant baru.
2. **Production** — pola yang sama seperti `wildcard-subdomain-test`, tapi diarahkan ke Laravel (Nginx + PHP-FPM), bukan ke Node.js.

Saya juga sekalian nambahin validasi subdomain reserved (`www`, `api`, `admin`,
`superadmin`, dkk) di `RegisterController`, terinspirasi dari `utils.js` di
project test kamu — supaya tenant baru tidak bisa daftar pakai nama yang
harusnya dicadangkan.

---

## 1. Local dev (Windows) — biar tidak edit hosts lagi

Hosts file Windows **tidak mendukung wildcard** (`*.ptutamacta.test` tidak
bisa ditulis di sana). Solusinya: pasang DNS resolver lokal yang mendukung
wildcard, namanya **Acrylic DNS Proxy** (gratis, ringan, khusus Windows).

### Langkah install

1. Download Acrylic DNS Proxy: https://mayakron.altervista.org/support/acrylic/Home.htm
   → pilih installer sesuai versi Windows kamu (32/64-bit), install seperti biasa.

2. Buka file konfigurasi hosts Acrylic (biasanya lewat Start Menu →
   *Acrylic DNS Proxy → Edit Acrylic Hosts File*, atau langsung di
   `C:\Program Files (x86)\Acrylic DNS Proxy\AcrylicHosts.txt`).

3. Tambahkan baris ini di paling bawah:
   ```
   127.0.0.1   ptutamacta.test
   127.0.0.1   *.ptutamacta.test
   ```

4. Restart service Acrylic:
   - Start Menu → *Acrylic DNS Proxy → Restart Acrylic Service*
   - atau lewat Services.msc, cari "Acrylic DNS Proxy Service", klik Restart.

5. Arahkan DNS komputer kamu ke Acrylic (yang jalan di `127.0.0.1`):
   - Control Panel → Network and Sharing Center → Change adapter settings
   - Klik kanan adapter aktif (WiFi/Ethernet) → Properties
   - Pilih "Internet Protocol Version 4 (TCP/IPv4)" → Properties
   - Preferred DNS server: `127.0.0.1`
   - Alternate DNS server: `8.8.8.8` (fallback, biar internet lain tetap jalan)

6. Flush DNS cache:
   ```
   ipconfig /flushdns
   ```

7. Tes — subdomain APAPUN yang belum pernah kamu daftarkan pun harus resolve:
   ```
   ping subdomain-ngasal-apa-saja.ptutamacta.test
   ```
   Kalau muncul balasan dari `127.0.0.1`, berarti wildcard-nya sudah jalan.
   Sekarang tiap ada tenant baru daftar lewat `/daftar`, subdomain-nya
   langsung bisa diakses tanpa sentuh hosts file lagi.

> **Kalau kamu pakai Laragon** (bukan XAMPP polos): Laragon punya fitur
> *Auto Virtual Hosts* dengan resolver DNS bawaan yang sebenarnya sudah
> mendukung wildcard `*.test` secara otomatis begitu diaktifkan
> (Laragon menu → Preferences → Services → Auto Virtual Hosts). Kalau ini
> menyala, kamu mungkin tidak perlu Acrylic sama sekali — coba dulu opsi ini
> kalau pakai Laragon.

---

## 2. Production — domain asli + Cloudflare Tunnel

Ini pola yang sama persis dengan `wildcard-subdomain-test`, tapi diarahkan
ke Laravel (lewat Nginx + PHP-FPM) alih-alih langsung ke proses Node.js.
File template sudah saya siapkan di folder `deploy/`:

- `deploy/nginx-absen-simtren.conf` — vhost Nginx dengan wildcard subdomain
- `deploy/cloudflared-config.yml` — konfigurasi tunnel
- `deploy/.env.production.example` — nilai `.env` yang perlu diubah

### Langkah-langkah

1. **Deploy project ke server/VPS** (misal di `/var/www/absen_simtren`), pastikan
   PHP 8.1+, Composer, Nginx, MySQL sudah terpasang, lalu `composer install --no-dev`,
   `php artisan migrate`, `php artisan config:cache`, dst seperti deploy Laravel biasa.

2. **Pasang vhost Nginx** — copy `deploy/nginx-absen-simtren.conf` ke
   `/etc/nginx/sites-available/`, sesuaikan path socket PHP-FPM
   (`$php_fpm_socket`), lalu aktifkan:
   ```bash
   sudo ln -s /etc/nginx/sites-available/nginx-absen-simtren.conf /etc/nginx/sites-enabled/
   sudo nginx -t && sudo systemctl reload nginx
   ```
   Baris `server_name ptutamacta.com *.ptutamacta.com;` inilah yang bikin
   Nginx menerima **semua** subdomain sekaligus, tanpa perlu daftar satu-satu.

3. **Update `.env` di server** — ambil nilainya dari
   `deploy/.env.production.example` (terutama `APP_URL`, `APP_MAIN_DOMAIN`,
   `APP_ENV=production`, `APP_DEBUG=false`). **Jangan** set `SESSION_DOMAIN`
   ke wildcard — alasannya ada di komentar file itu (biar sesi login satu
   tenant tidak bocor ke tenant lain).

4. **Install & konfigurasi cloudflared** (kalau belum):
   ```bash
   curl -L https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64 -o /usr/local/bin/cloudflared
   chmod +x /usr/local/bin/cloudflared
   cloudflared tunnel login
   cloudflared tunnel create absen-simtren
   ```
   Copy `deploy/cloudflared-config.yml` ke `~/.cloudflared/config.yml`, ganti
   `<TUNNEL_ID>` sesuai output `tunnel create` di atas.

5. **Arahkan DNS wildcard ke tunnel** (sekali saja per hostname):
   ```bash
   cloudflared tunnel route dns absen-simtren "ptutamacta.com"
   cloudflared tunnel route dns absen-simtren "*.ptutamacta.com"
   ```

6. **Jalankan tunnel**:
   ```bash
   cloudflared tunnel run absen-simtren
   # atau sebagai service:
   sudo cloudflared service install
   sudo systemctl start cloudflared
   ```

7. **SSL**: tidak perlu urus sertifikat wildcard manual — selama domain
   `ptutamacta.com` sudah masuk Cloudflare (orange cloud / proxied), sertifikat
   Universal SSL Cloudflare otomatis meng-cover `*.ptutamacta.com` juga.

8. **Tes**: daftar tenant baru lewat `https://ptutamacta.com/daftar`, lalu
   langsung buka `https://<subdomain-baru>.ptutamacta.com/login` — harus
   langsung bisa diakses tanpa konfigurasi tambahan apa pun, sama seperti
   yang kamu buktikan lewat `wildcard-subdomain-test`.

---

## 3. Halaman daftar sekarang cek subdomain secara real-time

Sebelumnya, subdomain baru divalidasi cuma waktu form di-submit. Sekarang
halaman `/daftar` sudah dilengkapi live-check:

- Saat user mengetik di kolom subdomain, JS otomatis merapikan input
  (huruf kecil, spasi jadi `-`, karakter aneh dibuang).
- ~450ms setelah berhenti mengetik, browser memanggil
  `GET /daftar/cek-subdomain?subdomain=...` (route baru, `RegisterController::checkSubdomain`).
- Endpoint itu mengecek: format valid, bukan kata reserved
  (`config/reserved_subdomains.php`), dan belum dipakai tenant lain — lalu
  balas JSON `{ available, message, url }`.
- Kotak subdomain berubah warna (abu → hijau/merah) dan tombol submit
  otomatis nonaktif kalau subdomain belum valid, jadi user tidak perlu
  submit dulu baru tahu ada masalah.
- Aturan validasinya sekarang dipusatkan di satu method
  (`validateSubdomainValue()` di `RegisterController`), dipakai bareng oleh
  live-check maupun submit form asli — jadi tidak ada celah beda aturan
  antara keduanya. Validasi server-side saat submit tetap jadi penjaga
  akhir kalau JS gagal jalan (mis. JS di-disable browser).

File yang berubah: `app/Http/Controllers/Auth/RegisterController.php`,
`resources/views/register.blade.php`, `routes/web.php` (tambah 1 route GET).

## 4. Perubahan kode lain (dari sesi sebelumnya)

- `config/reserved_subdomains.php` — daftar kata yang tidak boleh dipakai
  sebagai subdomain tenant (`www`, `api`, `mail`, `admin`, `superadmin`, dst).

Tidak ada perubahan di `DetectTenant`, model, atau logika routing subdomain
tenant lainnya — memang tidak perlu, karena bagian itu sudah generik sejak
awal.

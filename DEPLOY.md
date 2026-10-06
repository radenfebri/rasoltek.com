# Checklist deployment RasolTek Hosting

Proyek ini PHP native. CSS, JavaScript, font, dan logo siap disajikan dari `assets/`: tidak perlu `npm run dev`, Node.js, atau Composer di server produksi.

## 1. Konfigurasi domain dan environment

Edit `.env` **di server produksi**, tanpa membagikan API key:

```dotenv
APP_URL=https://www.rasoltek.com
APP_ENV=production
SEO_INDEXING=true
```

- Domain utama proyek adalah `https://www.rasoltek.com`. Jangan memakai `localhost`, `.test`, alamat IP privat, atau domain portal transaksi pada `.env` produksi.
- Arahkan DNS `rasoltek.com` dan `www.rasoltek.com` ke situs yang sama; pasang sertifikat SSL untuk keduanya. `.htaccess` sudah mengarahkan host `rasoltek.com` ke `https://www.rasoltek.com` dengan 301, mempertahankan path dan query. Domain lokal tidak terkena redirect; path ACME challenge dikecualikan agar validasi SSL tetap tersedia.
- Aktifkan pemaksaan HTTPS untuk versi `www` melalui panel hosting setelah SSL siap. Aturan ini sengaja tidak ditambahkan secara generik ke `.htaccess`: jika memakai proxy/CDN, konfigurasi HTTPS perlu mengikuti proxy agar tidak terjadi loop. Jangan memasang aturan lain yang justru mengarahkan www ke non-www.
- Isi `RDASH_RESELLER_ID` dan `RDASH_API_KEY` secara privat. Whitelist IP keluar server produksi di RDASH; IP komputer lokal belum tentu sama.
- Untuk staging, gunakan `APP_ENV=staging` atau `SEO_INDEXING=false`. Lokal/staging diberi `noindex` dan `robots.txt` melarang crawling. Jangan mengunggah `.env` lokal dengan `APP_URL=http://localhost`: situs produksi akan tetap `noindex`.

## 2. Upload dan konfigurasi server

Upload PHP publik di root, `router.php`, `robots.php`, `sitemap.php`, folder `api/`, `assets/`, `components/`, `config/`, `includes/`, serta `.htaccess` untuk Apache. Buat `.env` privat di server. PHP 8.2+, ekstensi cURL, HTTPS keluar, dan direktori temporary yang bisa ditulis diperlukan untuk pencarian domain.

Tidak perlu mengunggah `node_modules/`, `src/`, `tests/`, `deploy/`, manifest npm/Composer, dokumentasi, file backup, maupun konfigurasi lokal. Aplikasi saat ini tidak menggunakan dependency Composer runtime.

- **Apache / hosting cPanel:** gunakan `.htaccess` proyek; perlu Apache 2.4, `mod_rewrite`, dan izin override. URL `/hosting`, `/vps-jakarta`, `/robots.txt`, dan `/sitemap.xml` diarahkan ke PHP tanpa ekstensi publik.
- **NGINX:** `.htaccess` tidak dibaca. Contoh portable tersedia di `deploy/nginx.conf.example`; administrator hosting perlu menyesuaikan document root dan upstream PHP-FPM. Jangan menyalin path Windows atau konfigurasi FlyEnv ke server produksi. Ini hanya contoh di proyek, tidak diterapkan otomatis.
- Jangan mengaktifkan situs bila PHP terunduh sebagai source. Pastikan PHP dieksekusi, dan `.env` serta folder include tidak bisa diakses lewat HTTP. Aturan robots.txt bukan pengganti proteksi server.

## 3. Verifikasi setelah upload

1. Buka beranda, hosting, domain, VPS Jakarta/Bogor, SSL, kontak: semuanya harus HTTP 200 dan aset tidak 404.
2. Periksa source HTML: canonical memakai domain HTTPS produksi, metadata robots **bukan** `noindex` untuk halaman publik, dan logo sharing dapat dibuka.
3. Buka `/sitemap.xml`: HTTP 200, XML berisi 11 URL publik bersih dengan domain produksi. Tanggal `lastmod` berasal dari file konten/konfigurasi; jangan mengubah tanggal setiap request.
4. Buka `/robots.txt`: HTTP 200, tidak ada `Disallow: /` untuk produksi dan ada `Sitemap: https://www.rasoltek.com/sitemap.xml`.
5. Coba alamat tidak ada: harus HTTP 404, bukan 200. Halaman 404 diberi `noindex` dan tidak dimasukkan ke sitemap.
6. Coba `/hosting.php` dan `/hosting/`: redirect 301 ke `/hosting`. `/index.php` diarahkan ke `/`. Query parameter tidak masuk canonical atau sitemap.
7. Pastikan `/.env`, `/config/app.php`, `/includes/env.php`, `/tests/seo-check.mjs`, dan file backup menghasilkan 403/404, bukan isi file.
8. Uji domain search, ekstensi domain, tombol order, tema terang/gelap, serta mobile. Form kontak membuka aplikasi email ke `admin@rasoltek.com`, bukan mengirim otomatis lewat server.
9. Tinjau dan sahkan isi Privacy Policy serta Terms of Service; saat ini kedua halaman masih menyebut dokumen awal.

## 4. Google Search Console

Setelah domain aktif, verifikasi kepemilikan di Google Search Console, lalu submit **`sitemap.xml`** melalui menu Sitemaps. Periksa beranda dan halaman produk melalui URL Inspection. Sitemap membantu discovery, tetapi tidak menjamin indexing atau posisi pencarian.

Referensi resmi: [Google: build and submit a sitemap](https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap), [canonical URL](https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls), [robots meta tags](https://developers.google.com/search/docs/crawling-indexing/robots-meta-tag).

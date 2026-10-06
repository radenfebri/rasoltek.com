# RasolTek Hosting Frontend

Frontend marketing PHP Native untuk domain, hosting, VPS Jakarta/Bogor, dan SSL RasolTek.

## Persyaratan

- PHP 8.2+
- Ekstensi cURL untuk API pencarian domain; temporary directory yang dapat ditulis
- Apache 2.4 dengan `mod_rewrite`, atau NGINX dengan PHP FastCGI dan routing ke `router.php`
- Node.js hanya opsional untuk membangun ulang CSS, bukan kebutuhan runtime

## Menjalankan lokal

```bash
php -S localhost:8000 router.php
```

Buka `http://localhost:8000`. Bila sudah memakai web server PHP lokal, cukup buka situsnya. CSS produksi sudah tersedia di `assets/css/tailwind.css`; tidak perlu `npm run dev`.

## Konfigurasi

- URL portal dan endpoint ada di `config/portal.php` dan dapat dioverride melalui environment variable.
- Fitur pencarian domain memakai endpoint lokal `api/domain-check` yang meneruskan request ke RDASH `GET /v1/domains/availability`.
- Dropdown ekstensi memakai endpoint lokal `api/domain-extensions`, mengambil seluruh halaman `GET /v1/account/prices` dan menampilkan ekstensi aktif dari respons API. Daftar bawaan di HTML hanya digunakan sementara saat memuat atau jika API tidak tersedia.
- Endpoint Swagger `GET /v1/domains` (`GetShowAllDomain`) dipakai untuk daftar domain pada akun reseller, bukan untuk cek ketersediaan domain publik.
- Isi `RDASH_RESELLER_ID` dan `RDASH_API_KEY` di file `.env`. File `.env` sudah diblokir dari akses publik oleh `.htaccess` dan masuk `.gitignore`.
- Jika memakai NGINX, `.htaccess` tidak berlaku. Administrator server perlu routing ke `router.php` dan proteksi file privat; contoh portable ada di `deploy/nginx.conf.example`. Tidak ada perubahan konfigurasi server yang diterapkan otomatis oleh proyek.
- Semua data paket berada di `config/products.php`.
- Paket hosting bersumber dari katalog portal, diverifikasi pada 4 Oktober 2026 dan disimpan di `config/hosting.php`. Spesifikasi, harga bulanan, syarat domain gratis, dan tautan pemesanan dapat diperbarui dari file ini.
- Katalog SSL dan tautan pemesanan berada di `config/ssl.php`, berdasarkan katalog portal yang diverifikasi pada 4 Oktober 2026. Perbarui file ini jika harga portal berubah.
- Alamat, telepon, dan tautan Maps footer berada di `config/company.php`.
- Font Manrope dilayani secara lokal dari `assets/fonts/`; lisensinya disertakan. Tema diinisialisasi sebelum stylesheet agar perpindahan halaman tidak berkedip.
- Ganti setiap `order_url` bernilai `#` dengan URL produk WHMCS resmi ketika ID produk tersedia.
- Harga hosting dan SSL adalah salinan katalog resmi yang diverifikasi; perbarui konfigurasinya jika harga portal berubah.
- Tinjau dokumen legal bersama pihak berwenang sebelum produksi.

## Production

Ikuti [checklist deployment](DEPLOY.md). Atur domain HTTPS utama di `APP_URL`, `APP_ENV=production`, dan `SEO_INDEXING=true`. Jangan unggah `.env` lokal tanpa mengganti `APP_URL`. CSS/font dilayani lokal; Node.js dan build di server tidak diperlukan.

SEO: title/description unik, canonical, gambar Open Graph/Twitter, structured data Organization/WebSite/WebPage/BreadcrumbList, serta `/sitemap.xml` dan `/robots.txt` dinamis. Daftar halaman ada di `config/seo.php`. Lokal/staging dan 404 tidak diindeks. Sitemap hanya berisi URL publik bersih, bukan endpoint API atau file privat.

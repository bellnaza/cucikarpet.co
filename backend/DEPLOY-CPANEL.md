# Pasang cucikarpet.co di cPanel

Satu aplikasi Laravel untuk semuanya:

| Alamat | Untuk |
|---|---|
| `https://cucikarpet.co/` | Laman web pelanggan + borang tempahan |
| `https://cucikarpet.co/admin` | Dashboard admin (order, pelanggan, analitik, emel) |
| `https://cucikarpet.co/driver` | App team pickup & delivery (buka di telefon) |

## 1. Semak hosting

Dalam cPanel → **Select PHP Version** (atau **MultiPHP Manager**):

- PHP **8.2 atau lebih baru**
- Extension aktif: `intl`, `pdo_mysql`, `mbstring`, `fileinfo`, `gd`, `zip`, `bcmath`, `openssl`, `curl`

Anda juga perlu **Terminal** atau **SSH** dalam cPanel. Jika tiada, minta hosting aktifkan.

## 2. Pangkalan data

cPanel → **MySQL Databases**:

1. Cipta database, contoh `user_cucikarpet`
2. Cipta user database dengan kata laluan yang kuat
3. Tambah user ke database dengan **ALL PRIVILEGES**

## 3. Muat naik kod

Letak folder projek **di luar** `public_html`, contoh `/home/USER/cucikarpet`.
Jangan muat naik `vendor/`, `node_modules/`, `.env` atau `database/database.sqlite`.

Pilihan A (disyorkan): Git. cPanel → **Git Version Control** → clone repo ke `/home/USER/cucikarpet`.
Pilihan B: zip folder `backend/` (tanpa folder di atas), upload melalui **File Manager**, extract.

Kemudian dalam Terminal:

```bash
cd ~/cucikarpet
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
```

## 4. Isi `.env`

Edit `~/cucikarpet/.env` dalam File Manager:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://cucikarpet.co

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=user_cucikarpet
DB_USERNAME=user_cucikarpet
DB_PASSWORD=kata-laluan-database

MAIL_MAILER=resend
MAIL_FROM_ADDRESS="order@cucikarpet.co"
RESEND_API_KEY=re_xxxxxxxx

ADMIN_EMAIL=emel-anda@cucikarpet.co
ADMIN_PASSWORD=kata-laluan-admin-yang-kuat
```

`RESEND_API_KEY` hanya disimpan di sini, di server. Jangan letak dalam kod atau hantar kepada sesiapa.

## 5. Sediakan database & fail

```bash
php artisan migrate --force
php artisan db:seed --force          # harga karpet, Team A/B, akaun admin pertama
php artisan storage:link             # foto karpet
php artisan filament:optimize
php artisan optimize
```

Selepas log masuk pertama, buang `ADMIN_PASSWORD` dari `.env`.

**Jangan** jalankan `DemoSeeder` di server: ia untuk ujian sahaja.

## 6. Halakan domain ke folder `public`

Paling mudah: cPanel → **Domains** → edit `cucikarpet.co` → tukar **Document Root** kepada `cucikarpet/public`.

Jika hosting tidak benarkan, buang isi `public_html` dan buat symlink:

```bash
rm -rf ~/public_html
ln -s ~/cucikarpet/public ~/public_html
```

## 7. Cron (wajib, untuk emel)

cPanel → **Cron Jobs** → **Once Per Minute** (`* * * * *`):

```
cd /home/USER/cucikarpet && php artisan schedule:run >> /dev/null 2>&1
```

Ini menghantar emel dalam baris gilir setiap minit. Tanpa cron, emel kekal "Dalam baris gilir".
Jika `php` dalam cron bukan versi 8.2+, guna laluan penuh, contoh `/usr/local/bin/ea-php83`.

## 8. Resend

1. Daftar di resend.com → **Domains** → Add `cucikarpet.co`
2. Masukkan rekod DNS yang Resend beri dalam cPanel → **Zone Editor** (TXT untuk SPF & DKIM, MX untuk bounce)
3. Tunggu status **Verified**
4. **API Keys** → Create, kebenaran **Sending access** → salin ke `RESEND_API_KEY` dalam `.env`
5. `php artisan config:cache`

Uji: cipta order dengan emel anda sendiri, kemudian semak menu **Emel pelanggan** dalam admin.

## 9. HTTPS

cPanel → **SSL/TLS Status** → Run AutoSSL. Fail `public/.htaccess` sudah paksa HTTPS, dan aplikasi menambah header keselamatan (HSTS hanya bila `APP_ENV=production`).

## Kemas kini kod kemudian

```bash
cd ~/cucikarpet
git pull                      # atau upload fail baru
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
php artisan filament:optimize
```

## Team pickup & delivery

Admin → **Pengguna & driver** → New: peranan **Team pickup/delivery**, pilih team.
Driver log masuk di `https://cucikarpet.co/driver` dan nampak tugasan team mereka sahaja.
Di telefon: Chrome/Safari → menu → **Add to Home Screen**.

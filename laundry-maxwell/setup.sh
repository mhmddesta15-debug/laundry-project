#!/usr/bin/env bash
# Pemakaian: bash setup.sh [nama-folder-proyek]
# Prasyarat: PHP >= 8.2, Composer, Node.js + npm, MySQL
set -e
APP="${1:-laundry-maxwell-app}"
HERE="$(cd "$(dirname "$0")" && pwd)"

composer create-project laravel/laravel "$APP"
cd "$APP"

composer require laravel/breeze --dev
php artisan breeze:install blade --no-interaction

# Timpa/Tambahkan file kustom Laundry
cp -R "$HERE/src/." .

# Sesuaikan .env ke MySQL
sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
sed -i 's/^# \?DB_HOST=.*/DB_HOST=127.0.0.1/' .env
sed -i 's/^# \?DB_PORT=.*/DB_PORT=3306/' .env
sed -i 's/^# \?DB_DATABASE=.*/DB_DATABASE=laundry/' .env
sed -i 's/^# \?DB_USERNAME=.*/DB_USERNAME=root/' .env
sed -i 's/^# \?DB_PASSWORD=.*/DB_PASSWORD=/' .env
sed -i 's/^APP_LOCALE=.*/APP_LOCALE=id/' .env || true
sed -i 's#^APP_TIMEZONE=.*##' .env || true

echo
echo "==> Selesai. Langkah berikutnya:"
echo "1) Buat database MySQL bernama 'laundry' (cek DB_* di .env)"
echo "2) cd $APP && php artisan migrate:fresh --seed"
echo "3) npm install && npm run dev   (terminal 1)"
echo "4) php artisan serve            (terminal 2)"
echo "Login admin: admin@laundry.test / password"
echo "Demo resi : LDR-DEMO-0001  | demo pelanggan: 081234567890 / 081234567890"

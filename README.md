🗂️ SIM Absensi – Laravel + Docker

Sistem Informasi Absensi menggunakan Laravel, PostgreSQL, dan Docker. Proyek ini bertujuan agar semua developer bisa menjalankan backend API Laravel tanpa perlu menginstal PHP, Composer, atau database di mesin lokal.
📦 Fitur Utama

    Laravel 10.x

    PostgreSQL 14

    Nginx (sebagai webserver)

    Docker Compose untuk orkestrasi

🚀 Cara Menjalankan Proyek
1. Clone Repository

git clone https://github.com/namarepo/sim-absensi.git
cd sim-absensi

2. Buat File .env

Salin dari contoh:

cp .env.example .env

Lalu sesuaikan beberapa variabel environment jika perlu:

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=sim-absensi
DB_USERNAME=postgres
DB_PASSWORD=root

3. Build dan Jalankan Docker

docker-compose up -d --build

4. Install Dependency Laravel

docker exec -it sim-absensi composer install

5. Generate Key dan Jalankan Migration

docker exec -it sim-absensi php artisan key:generate
docker exec -it sim-absensi php artisan migrate

🔗 Akses Proyek

Setelah semua container berjalan, buka di browser:

http://localhost:8000
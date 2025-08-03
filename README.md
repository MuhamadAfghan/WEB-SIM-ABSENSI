# SIM-ABSENSI

Selamat datang di proyek SIM-ABSENSI! Panduan ini akan membantu Anda memulai dengan menunjukkan cara mengkloning repository serta menjelaskan aturan dasar untuk berkontribusi.

---

## 📥 Tutorial Langkah demi Langkah Clone Repository

Ikuti langkah-langkah berikut untuk mengkloning repository ke komputer Anda:

1. **Instal Git (jika belum terpasang):**
   - [Unduh Git](https://git-scm.com/downloads) dan ikuti petunjuk instalasi sesuai sistem operasi Anda.

2. **Buka terminal atau command prompt Anda.**

3. **Arahkan ke direktori tempat Anda ingin menyimpan proyek:**
   ```bash
   cd path/to/your/folder
   ```

4. **Klon repository:**
   ```bash
   git clone https://github.com/MuhamadAfghan/SIM-ABSENSI.git
   ```

5. **Masuk ke folder proyek:**
   ```bash
   cd SIM-ABSENSI
   ```

6. **Instal dependensi proyek:**
   - Untuk PHP/Laravel:  
     ```bash
     composer install
     ```

7. **Atur environment Anda:**
   - Salin file environment contoh dan edit sesuai kebutuhan:
     ```bash
     cp .env.example .env
     ```
   - Edit `.env` dengan konfigurasi Anda (kredensial DB, dll).

8. **Generate application key (untuk Laravel):**
   ```bash
   php artisan key:generate
   ```

9. **Jalankan migrasi database (jika diperlukan):**
   ```bash
   php artisan migrate
   ```

10. **Jalankan server pengembangan:**
    ```bash
    php artisan serve
    ```

---

## 📚 Aturan & Panduan

Silakan ikuti aturan berikut ketika berkontribusi pada repository ini:

1. **Hormati Code of Conduct:**  
   Bersikap sopan dan saling menghargai antar kontributor.

2. **Gunakan Pesan Commit yang Jelas:**  
   Pesan commit harus deskriptif.

3. **Pesan Commit:**  
   - Gunakan feature/`nama-fitur-anda` untuk fitur baru.
   - Gunakan fix/`deskripsi-masalah` untuk perbaikan bug.
   - Gunakan docs/`update-deskripsi` untuk pembaruan dokumentasi.

4. **Pull Request:**  
   - Selalu buat pull request untuk setiap perubahan.
   - Tautkan issue terkait di deskripsi PR Anda.
   - Tambahkan deskripsi yang jelas mengenai perubahan Anda.

5. **Testing:**  
   - Pastikan kode Anda berjalan dan semua pengujian lolos sebelum mengajukan PR.

6. **Jangan Sertakan Data Sensitif:**  
   - Jangan pernah commit password, API key, atau informasi sensitif lainnya.

7. **Selalu Update:**  
   - Pull perubahan terbaru dari `main` (atau `master`) sebelum mulai mengerjakan sesuatu.

8. **Bertanya jika Bingung:**  
   - Jika ada yang tidak jelas, buat issue atau diskusikan dengan maintainer.

9. **Aturan Modifikasi Migration:**  
   - **Jangan mengubah langsung file migration yang sudah pernah merge atau sudah digunakan di production.**
   - Jika perlu melakukan perubahan skema database, selalu buat file migration baru, jangan edit migration lama.
   - Beri nama file migration baru dengan jelas sesuai perubahan yang dilakukan (misal: `add_column_to_users_table`).
   - Jelaskan perubahan skema pada deskripsi pull request.
   - Hanya modifikasi atau hapus migration lama jika diminta oleh maintainer repository dan migration tersebut dipastikan belum pernah dijalankan di environment mana pun.



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


---





Terima kasih atas kontribusi Anda!

Silakan buka [issue](https://github.com/MuhamadAfghan/SIM-ABSENSI/issues) jika membutuhkan bantuan.

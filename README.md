# sistem-approval-progress-harian

Laravel 10 Daily Progress Approval System
Sistem ini merupakan aplikasi internal untuk pencatatan dan approval progress harian dengan role-based access: Admin, Officer, PM, dan VP QHSE. Dibangun dengan Laravel 10, menggunakan PostgreSQL, dan frontend berbasis Tailwind CSS.
Kebutuhan Sistem
- PHP 8.2
- Composer
- Node.js & npm
- PostgreSQL
- Laravel 10
Langkah Instalasi
1.  Clone Proyek

git clone https://github.com/mhmdmkmri/sistem-approval-progress-harian.git
cd progress-approval

2.  Install Dependensi Backend
composer install
3.	Salin File .env dan Konfigurasi
cp .env.example .env
Edit .env dan sesuaikan bagian database seperti berikut:

APP_NAME="Progress Approval"
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=nama_database
DB_USERNAME=nama_user
DB_PASSWORD=kata_sandi

4.	Generate App Key
php artisan key:generate
5.	Migrasi dan Seeder
php artisan migrate --seed
6.	Link Storage untuk Upload Gambar
php artisan storage:link
Frontend Setup
7.	Install Node Modules
npm install
8.	uild Aset Frontend
Untuk development:
npm run dev
Untuk production:
npm run build
Akun Login Default (Seeder)
Role	Email	Password
Admin	admin@example.com	password
Officer	officer@example.com	password
PM	pm@example.com	password
VP QHSE	vp@example.com	password
Fitur Utama
- Login & autentikasi berdasarkan role
- Officer input progres harian
- Approval berjenjang: PM → VP QHSE
- Fitur reject dengan catatan (dari tabel progress_histories)
- Upload bukti (gambar)
- QR Code untuk lihat detail progres
- Riwayat lengkap approval progres
Lisensi
Proyek ini menggunakan lisensi MIT.

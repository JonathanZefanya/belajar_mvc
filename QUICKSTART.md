# Quick Start Guide - University Management System

## 🚀 Instalasi Cepat (5 Menit)

### Langkah 1: Persiapan
1. Download dan install XAMPP dari https://www.apachefriends.org/
2. Extract atau clone project ini ke folder `C:\xampp\htdocs\praktikum`

### Langkah 2: Start Services
1. Buka XAMPP Control Panel
2. Start **Apache** dan **MySQL**

### Langkah 3: Import Database
1. Buka browser, akses: http://localhost/phpmyadmin
2. Klik tab "SQL"
3. Copy-paste isi file `schema.sql` atau import file tersebut
4. Klik "Go"

### Langkah 4: Konfigurasi (Opsional)
Jika MySQL Anda menggunakan password, edit file `config/config.php`:
```php
define('DB_PASS', 'password_anda'); // Ubah sesuai password MySQL
```

### Langkah 5: Akses Aplikasi
Buka browser dan akses:
```
http://localhost/praktikum/public
```

### Langkah 6: Login
Gunakan salah satu akun berikut:

**Admin:**
- Username: `admin`
- Password: `Admin123!`

**User:**
- Username: `user`
- Password: `User123!`

## ✅ Selesai!

Anda sekarang bisa:
- ✨ Login ke sistem
- 📊 Melihat dashboard
- 🏛️ Mengelola data universitas (CRUD)
- 👥 Mengelola users (khusus admin)

## 🐛 Troubleshooting

### Error: "Database connection failed"
**Solusi:**
- Pastikan MySQL service running di XAMPP
- Cek username/password database di `config/config.php`
- Pastikan database `praktikum_mvc` sudah dibuat

### Error: "Page not found" atau "404"
**Solusi:**
1. Buka file: `C:\xampp\apache\conf\httpd.conf`
2. Cari baris: `#LoadModule rewrite_module modules/mod_rewrite.so`
3. Hapus tanda `#` di depannya
4. Save dan restart Apache

### Upload gambar gagal
**Solusi:**
- Pastikan folder `public/assets/uploads/` exist
- Untuk Linux/Mac: `chmod 777 public/assets/uploads/`
- Untuk Windows XAMPP: biasanya sudah OK

### Blank page / White screen
**Solusi:**
1. Edit file `config/config.php`
2. Ubah baris:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```
3. Refresh browser untuk lihat error message

## 📚 Dokumentasi Lengkap

- **README.md** - Dokumentasi utama
- **docs/explanations.md** - Penjelasan detail kode

## 💡 Tips

1. **Ganti Password Default** setelah login pertama kali
2. **Backup Database** secara berkala
3. **Gunakan Chrome/Firefox** untuk hasil terbaik
4. **Baca docs/explanations.md** untuk memahami cara kerja kode

## 🆘 Butuh Bantuan?

1. Baca dokumentasi lengkap di `README.md`
2. Cek `docs/explanations.md` untuk penjelasan kode
3. Review komentar inline di setiap file
4. Google error message yang muncul

---

**Happy Coding!** 🎉

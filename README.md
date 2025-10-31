# University Management System

Aplikasi web berbasis PHP Native dengan arsitektur MVC untuk mengelola data universitas. Dilengkapi dengan sistem autentikasi, role-based access control, dan fitur CRUD lengkap.

## 🚀 Fitur Utama

### Keamanan
- ✅ **Password Hashing** menggunakan `password_hash()` dengan algoritma bcrypt
- ✅ **CSRF Protection** dengan token validation pada setiap form submission
- ✅ **Session Regeneration** pada saat login untuk mencegah session fixation
- ✅ **Prepared Statements** dengan PDO untuk mencegah SQL Injection
- ✅ **Input Validation** & Sanitization
- ✅ **File Upload Validation** (tipe file, ukuran, MIME type)

### Fitur Aplikasi
- 🔐 **Authentication System** (Login, Logout, Register)
- 👥 **Role-Based Access Control** (Admin & User)
- 🏛️ **CRUD Universities** dengan upload gambar
- 👨‍💼 **User Management** (khusus Admin)
- 📊 **Dashboard** dengan statistik
- 🔍 **Search & Pagination**
- 📱 **Responsive Design** dengan Bootstrap 5

## 📋 Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Apache Web Server dengan mod_rewrite enabled
- XAMPP (recommended) atau LAMP/WAMP stack

## 🛠️ Instalasi

### 1. Clone atau Download Project

```bash
# Clone repository (jika dari Git)
git clone <repository-url>

# Atau extract ZIP file ke folder htdocs
# Path: C:\xampp\htdocs\praktikum
```

### 2. Setup Database

```bash
# 1. Buka phpMyAdmin (http://localhost/phpmyadmin)
# 2. Buat database baru atau gunakan MySQL CLI:

mysql -u root -p
```

```sql
-- Jalankan file schema.sql
source C:/xampp/htdocs/praktikum/schema.sql
```

Atau import manual melalui phpMyAdmin:
1. Buka phpMyAdmin
2. Klik "Import"
3. Pilih file `schema.sql`
4. Klik "Go"

### 3. Konfigurasi

Edit file `config/config.php` jika perlu:

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'praktikum_mvc');
define('DB_USER', 'root');
define('DB_PASS', ''); // Sesuaikan dengan password MySQL Anda

// Base URL
define('BASE_URL', 'http://localhost/praktikum');
```

### 4. Set Permissions (Linux/Mac)

```bash
# Berikan write permission ke folder uploads
chmod -R 777 public/assets/uploads/
```

Untuk Windows (XAMPP), biasanya sudah memiliki permission yang sesuai.

### 5. Akses Aplikasi

Buka browser dan akses:
```
http://localhost/praktikum/mvc/public
```

atau jika menggunakan virtual host:
```
http://praktikum.local
```

## 🔑 Akun Default

### Admin Account
- **Username:** `admin`
- **Email:** `admin@praktikum.com`
- **Password:** `Admin123!`

### User Account
- **Username:** `user`
- **Email:** `user@praktikum.com`
- **Password:** `User123!`

**⚠️ PENTING:** Segera ganti password default setelah login pertama kali!

## 📁 Struktur Folder

```
praktikum/
├── app/
│   ├── controllers/        # Controllers (AuthController, UniversityController, etc.)
│   ├── models/            # Models (User, University)
│   ├── views/             # Views (templates HTML)
│   │   ├── layouts/       # Layout templates
│   │   ├── auth/          # Login, Register views
│   │   ├── dashboard/     # Dashboard view
│   │   ├── universities/  # CRUD Universities views
│   │   └── users/         # User Management views
│   └── core/              # Core MVC classes
│       ├── App.php        # Router
│       ├── Controller.php # Base Controller
│       └── Database.php   # PDO Database wrapper
├── config/
│   └── config.php         # Configuration file
├── public/                # Publicly accessible files
│   ├── assets/
│   │   ├── css/          # Custom CSS
│   │   ├── js/           # Custom JavaScript
│   │   ├── img/          # Static images
│   │   └── uploads/      # Uploaded files
│   ├── index.php         # Entry point
│   └── .htaccess         # Apache rewrite rules
├── docs/
│   └── explanations.md   # Detailed code explanations
├── schema.sql            # Database schema
└── README.md            # This file
```

## 🎯 Penggunaan

### Login
1. Akses halaman login
2. Masukkan username/email dan password
3. Klik "Sign In"

### CRUD Universities

#### Create (Tambah)
1. Login sebagai Admin atau User
2. Klik "Universities" di menu
3. Klik tombol "Add University"
4. Isi form dengan data universitas
5. Upload gambar (opsional, max 2MB, format: JPG/PNG)
6. Klik "Save University"

#### Read (Lihat)
1. Halaman Universities menampilkan semua data dalam bentuk cards
2. Klik university card untuk melihat detail lengkap
3. Gunakan search box untuk mencari universitas

#### Update (Edit)
1. Klik tombol "Edit" pada university card atau detail page
2. Ubah data yang diperlukan
3. Upload gambar baru jika ingin mengganti (opsional)
4. Klik "Update University"

#### Delete (Hapus)
1. **Hanya Admin** yang dapat menghapus
2. Klik tombol "Delete" pada university card
3. Konfirmasi penghapusan
4. Data dan gambar akan terhapus permanent

### User Management (Admin Only)

#### Tambah User
1. Login sebagai Admin
2. Klik "Users" di menu
3. Klik "Add User"
4. Isi form dengan data user
5. Pilih role (Admin/User)
6. Klik "Create User"

#### Edit User
1. Klik tombol "Edit" pada user list
2. Ubah data yang diperlukan
3. Password bersifat opsional (kosongkan jika tidak ingin mengubah)
4. Klik "Update User"

#### Hapus User
1. Klik tombol "Delete" pada user list
2. Konfirmasi penghapusan
3. **Note:** Tidak bisa menghapus diri sendiri atau admin terakhir

## 🔒 Keamanan

### Password Hashing
```php
// Saat registrasi/create user
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Saat login
if (password_verify($inputPassword, $hashedPassword)) {
    // Password benar
}
```

### CSRF Protection
```php
// Generate token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Validasi token
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die('Invalid CSRF token');
}
```

### Session Security
```php
// Regenerate session ID saat login
session_regenerate_id(true);

// Session configuration
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true
]);
```

### File Upload Validation
```php
// Validasi ekstensi
$allowedExtensions = ['jpg', 'jpeg', 'png'];

// Validasi ukuran (max 2MB)
$maxSize = 2097152;

// Validasi MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
```

## 🎨 Design & UI

- **Framework CSS:** Bootstrap 5.3
- **Icons:** Bootstrap Icons
- **Fonts:** Google Fonts (Inter)
- **Color Scheme:** Modern gradient dengan primary color (#4f46e5)
- **Layout:** Responsive dan mobile-friendly
- **Animations:** Smooth transitions dan hover effects

## 🐛 Troubleshooting

### Error: "Database connection failed"
- Pastikan MySQL service berjalan
- Cek konfigurasi database di `config/config.php`
- Pastikan database sudah dibuat

### Error: "CSRF token invalid"
- Clear browser cache dan cookies
- Pastikan session berfungsi dengan baik

### Upload gambar gagal
- Cek permission folder `public/assets/uploads/`
- Pastikan ukuran file tidak melebihi 2MB
- Pastikan format file adalah JPG atau PNG

### Page not found / 404
- Pastikan mod_rewrite Apache sudah enabled
- Cek file `.htaccess` di folder `public/`
- Pastikan BASE_URL di config sudah benar

### Cara enable mod_rewrite di XAMPP:
1. Buka `xampp/apache/conf/httpd.conf`
2. Cari baris: `#LoadModule rewrite_module modules/mod_rewrite.so`
3. Hapus tanda `#` di depannya
4. Restart Apache

## 📚 Dokumentasi Kode

Untuk penjelasan detail tentang fungsi dan baris kode penting, lihat file:
```
docs/explanations.md
```

File tersebut berisi:
- Penjelasan arsitektur MVC
- Penjelasan setiap class dan method
- Flow diagram aplikasi
- Best practices yang digunakan

## 🔄 Update & Maintenance

### Backup Database
```bash
mysqldump -u root -p praktikum_mvc > backup.sql
```

### Update Password
Login dan akses User Management untuk mengubah password user.

### Menambah Fitur Baru
1. Buat Controller baru di `app/controllers/`
2. Buat Model baru di `app/models/` (jika perlu)
3. Buat View baru di `app/views/`
4. Update routing jika perlu

## 👨‍💻 Development

### Coding Standards
- PSR-12 untuk PHP code style
- Camel case untuk variable dan method names
- Pascal case untuk class names
- Inline comments untuk setiap function
- Type hints dan return types (jika menggunakan PHP 7.4+)

### Best Practices
- Selalu gunakan prepared statements
- Validasi semua input
- Sanitize output untuk mencegah XSS
- Gunakan CSRF token di setiap form
- Hash password dengan bcrypt
- Session regeneration setelah privilege escalation

## 📄 License

Project ini dibuat untuk keperluan pembelajaran dan praktikum.

## 🤝 Support

Jika ada pertanyaan atau issue:
1. Baca dokumentasi lengkap di `docs/explanations.md`
2. Check troubleshooting section
3. Review kode dengan komentar inline

## 📞 Contact

Dibuat dengan ❤️ untuk keperluan praktikum PHP MVC

---

**Catatan:** Aplikasi ini adalah project pembelajaran. Untuk production use, pertimbangkan:
- Menggunakan environment variables untuk konfigurasi
- Implementasi logging yang proper
- Rate limiting untuk API
- SSL/HTTPS
- Database migration tools
- Unit testing
- Continuous Integration/Deployment

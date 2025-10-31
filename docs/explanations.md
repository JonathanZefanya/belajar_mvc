# Penjelasan Detail Kode - University Management System

Dokumen ini menjelaskan fungsi dan baris-baris kode penting dalam aplikasi.

## 📑 Daftar Isi

1. [Arsitektur MVC](#arsitektur-mvc)
2. [Core Classes](#core-classes)
3. [Models](#models)
4. [Controllers](#controllers)
5. [Views](#views)
6. [Security Features](#security-features)
7. [Database Schema](#database-schema)
8. [Flow Diagram](#flow-diagram)

---

## Arsitektur MVC

### Apa itu MVC?

MVC (Model-View-Controller) adalah design pattern yang memisahkan aplikasi menjadi 3 komponen utama:

```
┌─────────────┐
│   Browser   │
└──────┬──────┘
       │ HTTP Request
       ▼
┌─────────────────────────────────────────┐
│           CONTROLLER                    │
│  - Terima request                       │
│  - Validasi input                       │
│  - Panggil Model                        │
│  - Load View                            │
└──────────┬──────────────────────────────┘
           │
    ┌──────┴──────┐
    │             │
    ▼             ▼
┌───────┐    ┌────────┐
│ MODEL │    │  VIEW  │
│ Data  │    │ Display│
└───────┘    └────────┘
```

---

## Core Classes

### 1. Database.php

**Lokasi:** `app/core/Database.php`

Class ini adalah wrapper untuk PDO yang menyediakan koneksi database dan method helper.

#### Method `connect()`
```php
public function connect() {
    $this->conn = null;
    
    try {
        // DSN (Data Source Name) untuk MySQL
        $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
```

**Penjelasan:**
- **Line 1:** DSN (Data Source Name) adalah string koneksi yang berisi informasi host, database, dan charset
- **charset=utf8mb4:** Mendukung emoji dan karakter Unicode penuh
- **try-catch:** Menangani error koneksi dengan exception handling

```php
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
```

**Penjelasan Options:**
- **ERRMODE_EXCEPTION:** Throw exception jika ada error (mudah untuk debugging)
- **FETCH_ASSOC:** Return hasil query sebagai associative array (key = column name)
- **EMULATE_PREPARES = false:** Gunakan prepared statements native MySQL (lebih aman)

#### Method `query()`
```php
public function query($query, $params = []) {
    try {
        $stmt = $this->conn->prepare($query);  // Prepare statement
        $stmt->execute($params);               // Execute dengan parameters
        return $stmt;
    } catch(PDOException $e) {
        error_log("Query Error: " . $e->getMessage());
        return false;
    }
}
```

**Penjelasan:**
- **prepare():** Mempersiapkan SQL query dengan placeholder
- **execute($params):** Menjalankan query dengan parameter yang di-bind otomatis
- **Prepared Statements:** Mencegah SQL Injection karena parameter di-escape otomatis

**Contoh Penggunaan:**
```php
// Tanpa prepared statement (BAHAYA - SQL Injection)
$query = "SELECT * FROM users WHERE username = '$username'";

// Dengan prepared statement (AMAN)
$query = "SELECT * FROM users WHERE username = :username";
$db->query($query, ['username' => $username]);
```

---

### 2. Controller.php

**Lokasi:** `app/core/Controller.php`

Base controller yang menyediakan method helper untuk semua controller.

#### Method `view()`
```php
protected function view($view, $data = [], $layout = 'default') {
    extract($data);  // Convert array to variables
    
    $viewPath = ROOT_PATH . '/app/views/' . $view . '.php';
```

**Penjelasan extract():**
```php
// Sebelum extract
$data = ['title' => 'Home', 'user' => 'John'];

// Setelah extract
$title = 'Home';
$user = 'John';
```

- **extract():** Mengubah array menjadi variable sehingga bisa diakses langsung di view
- **Keuntungan:** View code menjadi lebih clean, tidak perlu `$data['title']`

#### Method `generateCSRFToken()`
```php
protected function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
```

**Penjelasan:**
- **random_bytes(32):** Generate 32 bytes random (cryptographically secure)
- **bin2hex():** Convert bytes ke hexadecimal string (64 karakter)
- **Kenapa di session?** Token harus sama antara form dan validasi

**Flow CSRF Protection:**
```
1. Server generate token → simpan di session
2. Token dikirim ke form sebagai hidden input
3. User submit form → token ikut terkirim
4. Server validasi: token di POST === token di session?
5. Jika sama → proses, jika beda → reject
```

#### Method `validateCSRFToken()`
```php
protected function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || 
        !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    return true;
}
```

**Penjelasan:**
- **hash_equals():** Membandingkan string secara aman (timing attack resistant)
- **Kenapa tidak pakai `==`?** Comparison `==` bisa diserang dengan timing attack

---

### 3. App.php

**Lokasi:** `app/core/App.php`

Router utama yang mengatur URL routing.

#### URL Pattern
```
Format: /controller/method/param1/param2/...
Contoh: /university/edit/5

Mapping:
- controller = UniversityController
- method = edit
- params = [5]
```

#### Method `parseUrl()`
```php
private function parseUrl() {
    if (isset($_GET['url'])) {
        $url = rtrim($_GET['url'], '/');           // Hapus trailing slash
        $url = filter_var($url, FILTER_SANITIZE_URL);  // Sanitize
        $url = explode('/', $url);                 // Split by /
        return $url;
    }
    return [];
}
```

**Penjelasan:**
```php
// Input: "university/edit/5/"
// After rtrim: "university/edit/5"
// After explode: ["university", "edit", "5"]
```

---

## Models

### 1. User.php

**Lokasi:** `app/models/User.php`

Model untuk mengelola data user.

#### Method `authenticate()`
```php
public function authenticate($identifier, $password) {
    $query = "SELECT * FROM users WHERE username = :identifier OR email = :identifier LIMIT 1";
    
    $user = $this->db->single($query, ['identifier' => $identifier]);

    if ($user && password_verify($password, $user['password'])) {
        unset($user['password']);  // Jangan return password hash
        return $user;
    }

    return false;
}
```

**Penjelasan password_verify():**
```php
// Saat register
$hash = password_hash('mypassword', PASSWORD_DEFAULT);
// Result: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

// Saat login
password_verify('mypassword', $hash);  // true
password_verify('wrongpass', $hash);   // false
```

**Mengapa Aman?**
- Hash berbeda setiap kali (karena salt otomatis)
- Tidak bisa di-reverse (one-way hashing)
- Resistant terhadap brute force (slow hashing)

#### Method `create()`
```php
public function create($data) {
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, email, password, role) 
              VALUES (:username, :email, :password, :role)";
    
    $params = [
        'username' => $data['username'],
        'email' => $data['email'],
        'password' => $hashedPassword,
        'role' => $data['role'] ?? 'user'
    ];

    if ($this->db->query($query, $params)) {
        return (int)$this->db->lastInsertId();
    }

    return false;
}
```

**Penjelasan:**
- Password **TIDAK PERNAH** disimpan dalam bentuk plaintext
- Selalu hash dengan `password_hash()` sebelum simpan ke database
- `PASSWORD_DEFAULT` menggunakan algoritma terbaik saat ini (bcrypt)

---

### 2. University.php

**Lokasi:** `app/models/University.php`

Model untuk mengelola data universitas.

#### Method `uploadImage()`
```php
public function uploadImage($file, $oldImage = null) {
    // 1. Validasi error upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'File upload error'];
    }

    // 2. Validasi ukuran file (max 2MB)
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'File size exceeds 2MB'];
    }

    // 3. Validasi extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return ['success' => false, 'error' => 'Only JPG and PNG files are allowed'];
    }

    // 4. Validasi MIME type (security extra)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMimes = ['image/jpeg', 'image/png'];
    if (!in_array($mimeType, $allowedMimes)) {
        return ['success' => false, 'error' => 'Invalid file type'];
    }

    // 5. Generate unique filename
    $filename = uniqid('univ_', true) . '.' . $extension;
    $destination = UPLOAD_PATH . $filename;

    // 6. Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Hapus file lama jika ada
        if ($oldImage && file_exists(UPLOAD_PATH . $oldImage)) {
            unlink(UPLOAD_PATH . $oldImage);
        }

        return ['success' => true, 'filename' => $filename];
    }

    return ['success' => false, 'error' => 'Failed to move uploaded file'];
}
```

**Penjelasan Validasi Upload:**

1. **Error Check:**
   ```php
   UPLOAD_ERR_OK = 0      // Success
   UPLOAD_ERR_INI_SIZE    // File too large (php.ini)
   UPLOAD_ERR_FORM_SIZE   // File too large (form)
   UPLOAD_ERR_PARTIAL     // Partial upload
   UPLOAD_ERR_NO_FILE     // No file uploaded
   ```

2. **Size Validation:**
   ```php
   MAX_FILE_SIZE = 2097152  // 2MB in bytes
   // 2 * 1024 * 1024 = 2097152
   ```

3. **Extension Validation:**
   ```php
   // pathinfo() extract info dari filename
   $file = "photo.jpg";
   pathinfo($file, PATHINFO_EXTENSION);  // "jpg"
   pathinfo($file, PATHINFO_FILENAME);   // "photo"
   ```

4. **MIME Type Validation (PENTING!):**
   ```php
   // Extension bisa diubah user: evil.php → evil.jpg
   // MIME type check membaca isi file sebenarnya
   
   finfo_file() // Cek isi file, bukan nama
   ```

5. **Unique Filename:**
   ```php
   uniqid('univ_', true)
   // Result: univ_5f8a5b2c4d3e2.jpg
   // Mencegah collision jika 2 user upload file sama
   ```

---

## Controllers

### 1. AuthController.php

**Lokasi:** `app/controllers/AuthController.php`

Controller untuk authentication.

#### Method `processLogin()`
```php
public function processLogin() {
    // 1. Cek request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect('auth/login');
    }

    // 2. Validasi CSRF token
    if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $this->setFlash('error', 'Invalid CSRF token');
        $this->redirect('auth/login');
    }

    // 3. Sanitize input
    $identifier = $this->sanitize($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';

    // 4. Validasi input
    if (empty($identifier) || empty($password)) {
        $this->setFlash('error', 'Please fill all fields');
        $this->redirect('auth/login');
    }

    // 5. Authenticate user
    $user = $this->userModel->authenticate($identifier, $password);

    if ($user) {
        // 6. Regenerate session ID (PENTING!)
        session_regenerate_id(true);

        // 7. Set session data
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_time'] = time();

        // 8. Redirect ke dashboard
        $this->setFlash('success', 'Welcome back, ' . $user['username'] . '!');
        $this->redirect('dashboard');
    } else {
        $this->setFlash('error', 'Invalid username/email or password');
        $this->redirect('auth/login');
    }
}
```

**Penjelasan session_regenerate_id():**

```php
// Sebelum regenerate
session_id(); // abc123

// Setelah regenerate
session_regenerate_id(true);
session_id(); // xyz789
```

**Mengapa Penting?**
- **Session Fixation Prevention:** Attacker tidak bisa menggunakan session ID lama
- **Best Practice:** Regenerate session ID setiap ada perubahan privilege (login, logout, role change)

**Flow Session Fixation Attack (tanpa regenerate_id):**
```
1. Attacker dapat session ID: abc123
2. Attacker beri link ke victim: site.com?session=abc123
3. Victim login dengan session abc123
4. Attacker pakai session abc123 → sudah login!
```

**Flow Session Fixation Defense (dengan regenerate_id):**
```
1. Attacker dapat session ID: abc123
2. Attacker beri link ke victim: site.com?session=abc123
3. Victim login → session ID berubah jadi xyz789
4. Attacker pakai session abc123 → tidak valid!
```

---

### 2. UniversityController.php

**Lokasi:** `app/controllers/UniversityController.php`

Controller untuk CRUD universities.

#### Method `store()`
```php
public function store() {
    $this->requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect('university/create');
    }

    // Validasi CSRF
    if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $this->setFlash('error', 'Invalid CSRF token');
        $this->redirect('university/create');
    }

    // Sanitize input
    $name = $this->sanitize($_POST['name'] ?? '');
    $address = $this->sanitize($_POST['address'] ?? '');
    $description = $this->sanitize($_POST['description'] ?? '');
    $website = $this->sanitize($_POST['website'] ?? '');
    $phone = $this->sanitize($_POST['phone'] ?? '');
    $email = $this->sanitize($_POST['email'] ?? '');

    // Validasi
    $errors = [];

    if (empty($name)) {
        $errors[] = 'University name is required';
    }

    if (empty($address)) {
        $errors[] = 'Address is required';
    }

    if (!empty($email) && !$this->validateEmail($email)) {
        $errors[] = 'Invalid email format';
    }

    // Handle image upload
    $imageName = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $uploadResult = $this->universityModel->uploadImage($_FILES['image']);
        
        if ($uploadResult['success']) {
            $imageName = $uploadResult['filename'];
        } else {
            $errors[] = $uploadResult['error'];
        }
    }

    // Jika ada error
    if (!empty($errors)) {
        $_SESSION['university_errors'] = $errors;
        $_SESSION['university_data'] = $_POST;
        $this->redirect('university/create');
    }

    // Get current user
    $currentUser = $this->getCurrentUser();

    // Create university
    $universityId = $this->universityModel->create([
        'name' => $name,
        'address' => $address,
        'description' => $description,
        'image' => $imageName,
        'website' => $website,
        'phone' => $phone,
        'email' => $email,
        'created_by' => $currentUser['id']
    ]);

    if ($universityId) {
        $this->setFlash('success', 'University added successfully');
        $this->redirect('university/detail/' . $universityId);
    } else {
        $this->setFlash('error', 'Failed to add university');
        $this->redirect('university/create');
    }
}
```

**Penjelasan Method sanitize():**
```php
protected function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}
```

**Step by step:**
```php
$input = "  <script>alert('XSS')</script>John  ";

// 1. trim() - Hapus whitespace
$step1 = "  <script>alert('XSS')</script>John  ";

// 2. strip_tags() - Hapus HTML/PHP tags
$step2 = "alert('XSS')John";

// 3. htmlspecialchars() - Convert special chars
$step3 = "alert('XSS')John";

// Final: "alert('XSS')John"
```

**Mengapa Perlu Sanitize?**
- **XSS Prevention:** User tidak bisa inject script
- **Clean Data:** Hapus tag HTML yang tidak diinginkan
- **Safe Output:** Karakter special di-escape

---

## Security Features

### 1. Prepared Statements (SQL Injection Prevention)

**Vulnerable Code:**
```php
// JANGAN LAKUKAN INI!
$username = $_POST['username'];
$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);

// Attack:
// Input: admin' OR '1'='1
// Query: SELECT * FROM users WHERE username = 'admin' OR '1'='1'
// Result: Bypass login!
```

**Secure Code:**
```php
// LAKUKAN INI!
$username = $_POST['username'];
$query = "SELECT * FROM users WHERE username = :username";
$stmt = $pdo->prepare($query);
$stmt->execute(['username' => $username]);

// Attack:
// Input: admin' OR '1'='1
// Query: SELECT * FROM users WHERE username = 'admin\' OR \'1\'=\'1'
// Result: No user found (aman!)
```

### 2. CSRF Protection

**Vulnerable Code:**
```html
<!-- Form tanpa CSRF token -->
<form method="POST" action="/user/delete/5">
    <button type="submit">Delete</button>
</form>

<!-- Attack: Buat form di site jahat -->
<form method="POST" action="http://victim-site.com/user/delete/5">
    <button>Click for prize!</button>
</form>
```

**Secure Code:**
```html
<!-- Form dengan CSRF token -->
<form method="POST" action="/user/delete/5">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
    <button type="submit">Delete</button>
</form>
```

```php
// Validasi di server
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die('CSRF attack detected!');
}
```

### 3. Password Hashing

**Vulnerable:**
```php
// JANGAN!
$query = "INSERT INTO users (password) VALUES ('$password')";
// Password tersimpan: mypassword123
```

**Secure:**
```php
// LAKUKAN!
$hash = password_hash($password, PASSWORD_DEFAULT);
// Password tersimpan: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/...
```

**Karakteristik Hash:**
- **One-way:** Tidak bisa di-reverse
- **Unique salt:** Hash berbeda setiap kali
- **Slow:** Resistant terhadap brute force
- **Future-proof:** PASSWORD_DEFAULT selalu gunakan algoritma terbaik

### 4. File Upload Validation

**Layers of Security:**

```php
// Layer 1: Error check
if ($file['error'] !== UPLOAD_ERR_OK) {
    return false;
}

// Layer 2: Size check
if ($file['size'] > MAX_FILE_SIZE) {
    return false;
}

// Layer 3: Extension check
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
if (!in_array($ext, ['jpg', 'png'])) {
    return false;
}

// Layer 4: MIME type check (PENTING!)
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
if (!in_array($mime, ['image/jpeg', 'image/png'])) {
    return false;
}

// Layer 5: Rename file (prevent overwrite)
$newName = uniqid() . '.' . $ext;

// Layer 6: Store outside webroot (best practice)
move_uploaded_file($file['tmp_name'], UPLOAD_PATH . $newName);
```

---

## Database Schema

### Tabel: users

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,  -- Hash bcrypt
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
);
```

**Penjelasan:**
- **VARCHAR(255) untuk password:** Bcrypt hash = 60 karakter, 255 untuk future-proof
- **UNIQUE pada username & email:** Tidak boleh duplikat
- **ENUM untuk role:** Hanya bisa 'admin' atau 'user'
- **INDEX:** Mempercepat query WHERE/JOIN

### Tabel: universities

```sql
CREATE TABLE universities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    address TEXT NOT NULL,
    description TEXT,
    image VARCHAR(255),
    website VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(100),
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_name (name),
    INDEX idx_created_by (created_by)
);
```

**Penjelasan:**
- **TEXT vs VARCHAR:** TEXT untuk data panjang tanpa limit tertentu
- **FOREIGN KEY:** Relasi ke tabel users
- **ON DELETE CASCADE:** Jika user dihapus, universities ikut terhapus
- **INDEX pada created_by:** Mempercepat query "universities by user"

---

## Flow Diagram

### Login Flow

```
┌──────────┐
│  User    │
└────┬─────┘
     │ 1. Access /auth/login
     ▼
┌─────────────────┐
│ AuthController  │
│   login()       │
└────┬────────────┘
     │ 2. Generate CSRF token
     │ 3. Show login form
     ▼
┌─────────────────┐
│  User submits   │
│   credentials   │
└────┬────────────┘
     │ 4. POST /auth/processLogin
     ▼
┌─────────────────┐
│ AuthController  │
│ processLogin()  │
├─────────────────┤
│ ✓ Validate CSRF │
│ ✓ Sanitize input│
│ ✓ Authenticate  │
└────┬────────────┘
     │
     ├─── Valid? ───┐
     │              │
     ▼              ▼
  Success        Failure
     │              │
     │              └──> Redirect to login
     │                   with error message
     │
     ├─> session_regenerate_id()
     ├─> Set session data
     └─> Redirect to dashboard
```

### CRUD University Flow

```
CREATE:
User → /university/create → Form dengan CSRF
     → Submit → Validate → Upload image
     → Insert DB → Redirect to detail

READ:
User → /university → Get all from DB
     → Display cards → Pagination
     → Click card → /university/detail/{id}
     → Get by ID → Display detail

UPDATE:
User → /university/edit/{id} → Get by ID
     → Form with data → Submit
     → Validate → Upload new image (optional)
     → Update DB → Redirect to detail

DELETE (Admin only):
Admin → Click delete → Confirm
      → POST /university/delete/{id}
      → Validate CSRF → Delete from DB
      → Delete image file → Redirect to list
```

---

## Best Practices yang Digunakan

### 1. Separation of Concerns
- **Model:** Database logic
- **View:** Presentation logic
- **Controller:** Business logic

### 2. DRY (Don't Repeat Yourself)
- Base Controller untuk reusable methods
- Helper functions di Controller

### 3. Security First
- Prepared statements everywhere
- CSRF protection on all forms
- Password hashing
- Input validation & sanitization
- File upload validation

### 4. User Experience
- Flash messages
- Form validation feedback
- Loading states
- Responsive design
- Error handling yang informatif

### 5. Code Quality
- Inline comments
- Descriptive variable names
- Consistent naming convention
- Type hints (where applicable)
- Error handling

---

## Tips Development

### Debugging

```php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Var dump dengan format
echo '<pre>';
var_dump($variable);
echo '</pre>';

// Die and dump
dd($variable);  // Custom helper function
```

### Testing

```php
// Test database connection
try {
    $db = new Database();
    $conn = $db->connect();
    echo "Connection successful!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Test model methods
$user = new User();
$result = $user->getAll();
var_dump($result);
```

### Common Issues

1. **404 Not Found**
   - Check .htaccess
   - Verify mod_rewrite enabled
   - Check BASE_URL in config

2. **CSRF Token Invalid**
   - Clear browser cache
   - Check session started
   - Verify token in form and validation

3. **Upload Failed**
   - Check folder permissions
   - Verify max file size
   - Check MIME type validation

---

## Kesimpulan

Aplikasi ini mengimplementasikan:
- ✅ **MVC Architecture** untuk code organization
- ✅ **PDO Prepared Statements** untuk SQL injection prevention
- ✅ **Password Hashing** untuk secure authentication
- ✅ **CSRF Protection** untuk form security
- ✅ **Session Regeneration** untuk session fixation prevention
- ✅ **File Upload Validation** untuk security
- ✅ **Input Sanitization** untuk XSS prevention
- ✅ **Role-Based Access Control** untuk authorization
- ✅ **Modern UI/UX** dengan Bootstrap 5
- ✅ **Responsive Design** untuk mobile compatibility

Setiap baris kode ditulis dengan mempertimbangkan:
- **Security:** Protect dari common vulnerabilities
- **Performance:** Efficient database queries dengan indexing
- **Maintainability:** Clean code dengan komentar
- **Scalability:** Mudah ditambahkan fitur baru
- **User Experience:** Interface yang intuitif dan responsif

---

**Catatan:** Dokumen ini adalah panduan pembelajaran. Untuk production, pertimbangkan framework modern seperti Laravel atau Symfony yang sudah built-in security features lebih lengkap.

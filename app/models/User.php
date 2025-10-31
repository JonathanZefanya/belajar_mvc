<?php
/**
 * Class User Model
 * 
 * Model untuk mengelola data user dengan prepared statements.
 * Menangani authentication, user CRUD operations, dan password hashing.
 */
class User {
    private $db;
    private $conn;

    /**
     * Constructor - Initialize database connection
     */
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    /**
     * Get semua users dengan optional filter role
     * 
     * @param string|null $role Filter berdasarkan role (admin/user)
     * @return array Array of users
     */
    public function getAll($role = null) {
        $query = "SELECT id, username, email, role, created_at, updated_at FROM users";
        $params = [];

        if ($role) {
            $query .= " WHERE role = :role";
            $params['role'] = $role;
        }

        $query .= " ORDER BY created_at DESC";

        return $this->db->all($query, $params);
    }

    /**
     * Get user by ID
     * 
     * @param int $id User ID
     * @return array|false User data atau false jika tidak ditemukan
     */
    public function getById($id) {
        $query = "SELECT id, username, email, role, created_at, updated_at 
                  FROM users WHERE id = :id LIMIT 1";
        
        return $this->db->single($query, ['id' => $id]);
    }

    /**
     * Get user by username
     * 
     * @param string $username Username
     * @return array|false User data atau false jika tidak ditemukan
     */
    public function getByUsername($username) {
        $query = "SELECT * FROM users WHERE username = :username LIMIT 1";
        
        return $this->db->single($query, ['username' => $username]);
    }

    /**
     * Get user by email
     * 
     * @param string $email Email address
     * @return array|false User data atau false jika tidak ditemukan
     */
    public function getByEmail($email) {
        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
        
        return $this->db->single($query, ['email' => $email]);
    }

    /**
     * Authenticate user dengan username/email dan password
     * 
     * @param string $identifier Username atau email
     * @param string $password Password plaintext
     * @return array|false User data jika berhasil, false jika gagal
     */
    public function authenticate($identifier, $password) {
        // Cek apakah identifier adalah email atau username
        $query = "SELECT * FROM users WHERE username = :identifier1 OR email = :identifier2 LIMIT 1";
        
        $user = $this->db->single($query, [
            'identifier1' => $identifier,
            'identifier2' => $identifier
        ]);

        // Jika user ditemukan, verify password
        if ($user && password_verify($password, $user['password'])) {
            // Jangan return password hash
            unset($user['password']);
            return $user;
        }

        return false;
    }

    /**
     * Create user baru
     * 
     * @param array $data Data user (username, email, password, role)
     * @return int|false User ID jika berhasil, false jika gagal
     */
    public function create($data) {
        // Hash password menggunakan password_hash
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

    /**
     * Update user data
     * 
     * @param int $id User ID
     * @param array $data Data yang akan diupdate
     * @return bool True jika berhasil
     */
    public function update($id, $data) {
        // Build query dinamis berdasarkan data yang ada
        $fields = [];
        $params = ['id' => $id];

        if (isset($data['username'])) {
            $fields[] = "username = :username";
            $params['username'] = $data['username'];
        }

        if (isset($data['email'])) {
            $fields[] = "email = :email";
            $params['email'] = $data['email'];
        }

        if (isset($data['password'])) {
            $fields[] = "password = :password";
            $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (isset($data['role'])) {
            $fields[] = "role = :role";
            $params['role'] = $data['role'];
        }

        if (empty($fields)) {
            return false;
        }

        $query = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";

        return $this->db->query($query, $params) !== false;
    }

    /**
     * Delete user
     * 
     * @param int $id User ID
     * @return bool True jika berhasil
     */
    public function delete($id) {
        // Jangan hapus admin terakhir
        $query = "SELECT COUNT(*) as count FROM users WHERE role = 'admin' AND id != :id";
        $result = $this->db->single($query, ['id' => $id]);
        
        if ($result['count'] == 0) {
            $checkAdmin = $this->db->single("SELECT role FROM users WHERE id = :id", ['id' => $id]);
            if ($checkAdmin && $checkAdmin['role'] == 'admin') {
                return false; // Jangan hapus admin terakhir
            }
        }

        $query = "DELETE FROM users WHERE id = :id";
        return $this->db->query($query, ['id' => $id]) !== false;
    }

    /**
     * Check apakah username sudah ada
     * 
     * @param string $username Username
     * @param int|null $excludeId ID yang dikecualikan (untuk update)
     * @return bool True jika sudah ada
     */
    public function usernameExists($username, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM users WHERE username = :username";
        $params = ['username' => $username];

        if ($excludeId) {
            $query .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        $result = $this->db->single($query, $params);
        return $result['count'] > 0;
    }

    /**
     * Check apakah email sudah ada
     * 
     * @param string $email Email address
     * @param int|null $excludeId ID yang dikecualikan (untuk update)
     * @return bool True jika sudah ada
     */
    public function emailExists($email, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM users WHERE email = :email";
        $params = ['email' => $email];

        if ($excludeId) {
            $query .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        $result = $this->db->single($query, $params);
        return $result['count'] > 0;
    }

    /**
     * Get total users count
     * 
     * @param string|null $role Filter by role
     * @return int Total users
     */
    public function getTotalCount($role = null) {
        $query = "SELECT COUNT(*) as count FROM users";
        $params = [];

        if ($role) {
            $query .= " WHERE role = :role";
            $params['role'] = $role;
        }

        $result = $this->db->single($query, $params);
        return $result['count'];
    }
}

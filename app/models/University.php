<?php
/**
 * Class University Model
 * 
 * Model untuk mengelola data universitas dengan prepared statements.
 * Menangani CRUD operations untuk universitas termasuk upload gambar.
 */
class University {
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
     * Get semua universities dengan pagination
     * 
     * @param int $limit Jumlah data per halaman
     * @param int $offset Offset untuk pagination
     * @param string|null $search Keyword untuk search
     * @return array Array of universities
     */
    public function getAll($limit = null, $offset = 0, $search = null) {
        $query = "SELECT u.*, us.username as creator_name 
                  FROM universities u 
                  LEFT JOIN users us ON u.created_by = us.id";
        $params = [];

        // Add search condition
        if ($search) {
            $query .= " WHERE u.name LIKE :search OR u.address LIKE :search OR u.description LIKE :search";
            $params['search'] = "%$search%";
        }

        $query .= " ORDER BY u.created_at DESC";

        // Add pagination
        if ($limit) {
            $query .= " LIMIT :limit OFFSET :offset";
            $params['limit'] = $limit;
            $params['offset'] = $offset;
        }

        return $this->db->all($query, $params);
    }

    /**
     * Get university by ID
     * 
     * @param int $id University ID
     * @return array|false University data atau false jika tidak ditemukan
     */
    public function getById($id) {
        $query = "SELECT u.*, us.username as creator_name 
                  FROM universities u 
                  LEFT JOIN users us ON u.created_by = us.id 
                  WHERE u.id = :id LIMIT 1";
        
        return $this->db->single($query, ['id' => $id]);
    }

    /**
     * Create university baru
     * 
     * @param array $data Data university
     * @return int|false University ID jika berhasil, false jika gagal
     */
    public function create($data) {
        $query = "INSERT INTO universities 
                  (name, address, description, image, website, phone, email, created_by) 
                  VALUES 
                  (:name, :address, :description, :image, :website, :phone, :email, :created_by)";
        
        $params = [
            'name' => $data['name'],
            'address' => $data['address'],
            'description' => $data['description'] ?? null,
            'image' => $data['image'] ?? null,
            'website' => $data['website'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'created_by' => $data['created_by']
        ];

        if ($this->db->query($query, $params)) {
            return (int)$this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Update university data
     * 
     * @param int $id University ID
     * @param array $data Data yang akan diupdate
     * @return bool True jika berhasil
     */
    public function update($id, $data) {
        // Build query dinamis berdasarkan data yang ada
        $fields = [];
        $params = ['id' => $id];

        $allowedFields = ['name', 'address', 'description', 'image', 'website', 'phone', 'email'];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $query = "UPDATE universities SET " . implode(', ', $fields) . " WHERE id = :id";

        return $this->db->query($query, $params) !== false;
    }

    /**
     * Delete university
     * 
     * @param int $id University ID
     * @return bool True jika berhasil
     */
    public function delete($id) {
        // Get image untuk dihapus juga
        $university = $this->getById($id);
        
        $query = "DELETE FROM universities WHERE id = :id";
        
        if ($this->db->query($query, ['id' => $id])) {
            // Hapus file gambar jika ada
            if ($university && $university['image'] && file_exists(UPLOAD_PATH . $university['image'])) {
                unlink(UPLOAD_PATH . $university['image']);
            }
            return true;
        }

        return false;
    }

    /**
     * Get total universities count
     * 
     * @param string|null $search Keyword untuk search
     * @return int Total universities
     */
    public function getTotalCount($search = null) {
        $query = "SELECT COUNT(*) as count FROM universities";
        $params = [];

        if ($search) {
            $query .= " WHERE name LIKE :search OR address LIKE :search OR description LIKE :search";
            $params['search'] = "%$search%";
        }

        $result = $this->db->single($query, $params);
        return $result['count'];
    }

    /**
     * Upload dan validasi gambar university
     * 
     * @param array $file File dari $_FILES
     * @param string|null $oldImage Nama file lama untuk dihapus
     * @return array Result dengan key 'success' dan 'filename' atau 'error'
     */
    public function uploadImage($file, $oldImage = null) {
        // Validasi error upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'File upload error'];
        }

        // Validasi ukuran file (max 2MB)
        if ($file['size'] > MAX_FILE_SIZE) {
            return ['success' => false, 'error' => 'File size exceeds 2MB'];
        }

        // Get extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Validasi extension
        if (!in_array($extension, ALLOWED_EXTENSIONS)) {
            return ['success' => false, 'error' => 'Only JPG and PNG files are allowed'];
        }

        // Validasi tipe MIME untuk keamanan extra
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = ['image/jpeg', 'image/png'];
        if (!in_array($mimeType, $allowedMimes)) {
            return ['success' => false, 'error' => 'Invalid file type'];
        }

        // Generate unique filename
        $filename = uniqid('univ_', true) . '.' . $extension;
        $destination = UPLOAD_PATH . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Hapus file lama jika ada
            if ($oldImage && file_exists(UPLOAD_PATH . $oldImage)) {
                unlink(UPLOAD_PATH . $oldImage);
            }

            return ['success' => true, 'filename' => $filename];
        }

        return ['success' => false, 'error' => 'Failed to move uploaded file'];
    }

    /**
     * Search universities by keyword
     * 
     * @param string $keyword Search keyword
     * @param int $limit Limit results
     * @return array Array of universities
     */
    public function search($keyword, $limit = 10) {
        $query = "SELECT u.*, us.username as creator_name 
                  FROM universities u 
                  LEFT JOIN users us ON u.created_by = us.id 
                  WHERE u.name LIKE :keyword 
                     OR u.address LIKE :keyword 
                     OR u.description LIKE :keyword 
                  ORDER BY u.name ASC 
                  LIMIT :limit";

        $params = [
            'keyword' => "%$keyword%",
            'limit' => $limit
        ];

        return $this->db->all($query, $params);
    }

    /**
     * Get universities by creator
     * 
     * @param int $userId User ID
     * @return array Array of universities
     */
    public function getByCreator($userId) {
        $query = "SELECT * FROM universities WHERE created_by = :user_id ORDER BY created_at DESC";
        
        return $this->db->all($query, ['user_id' => $userId]);
    }
}

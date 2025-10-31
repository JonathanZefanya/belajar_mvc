<?php
/**
 * Class Database
 * 
 * Wrapper class untuk PDO yang menyediakan koneksi database
 * dan method helper untuk query dengan prepared statements.
 */
class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $conn;

    /**
     * Membuat koneksi ke database menggunakan PDO
     * 
     * @return PDO|null Mengembalikan PDO object atau null jika gagal
     */
    public function connect() {
        $this->conn = null;

        try {
            // DSN (Data Source Name) untuk MySQL
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            
            // Options untuk PDO
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Throw exception pada error
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // Default fetch mode associative array
                PDO::ATTR_EMULATE_PREPARES => false  // Gunakan prepared statements asli
            ];

            // Membuat koneksi PDO
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch(PDOException $e) {
            // Log error (dalam production sebaiknya log ke file)
            error_log("Connection Error: " . $e->getMessage());
            die("Database connection failed. Please contact administrator.");
        }

        return $this->conn;
    }

    /**
     * Execute query dengan prepared statement
     * 
     * @param string $query SQL query dengan placeholder
     * @param array $params Parameter untuk bind ke query
     * @return PDOStatement|bool Statement object atau false jika gagal
     */
    public function query($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            error_log("Query Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get single row dari query result
     * 
     * @param string $query SQL query
     * @param array $params Parameter untuk bind
     * @return array|false Single row data atau false
     */
    public function single($query, $params = []) {
        $stmt = $this->query($query, $params);
        return $stmt ? $stmt->fetch() : false;
    }

    /**
     * Get multiple rows dari query result
     * 
     * @param string $query SQL query
     * @param array $params Parameter untuk bind
     * @return array Array of rows
     */
    public function all($query, $params = []) {
        $stmt = $this->query($query, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }

    /**
     * Get count dari query result
     * 
     * @param string $query SQL query
     * @param array $params Parameter untuk bind
     * @return int Jumlah rows
     */
    public function rowCount($query, $params = []) {
        $stmt = $this->query($query, $params);
        return $stmt ? $stmt->rowCount() : 0;
    }

    /**
     * Get last inserted ID
     * 
     * @return string Last insert ID
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }

    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit() {
        return $this->conn->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->conn->rollback();
    }
}

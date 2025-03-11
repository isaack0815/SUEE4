<?php
class Database {
    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $conn;
    private static $instance = null;
    
    private function __construct() {
        $this->host = DB_HOST;
        $this->user = DB_USER;
        $this->pass = DB_PASS;
        $this->dbname = DB_NAME;
        
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->user,
                $this->pass,
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
        } catch (PDOException $e) {
            die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
        }
    }
    
    // Singleton-Pattern
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function select($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function selectOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, array_values($data));
        
        return $this->conn->lastInsertId();
    }
    
    public function update($table, $data, $where, $whereParams = []) {
        $setParts = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            $setParts[] = "{$key} = ?";
            $params[] = $value;
        }
        
        $setClause = implode(', ', $setParts);
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        
        $params = array_merge($params, $whereParams);
        $this->query($sql, $params);
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            
            // Parameter binden
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    // Bestimme den Datentyp für das Binding
                    $type = PDO::PARAM_STR;
                    if (is_int($value)) {
                        $type = PDO::PARAM_INT;
                    } elseif (is_bool($value)) {
                        $type = PDO::PARAM_BOOL;
                    } elseif (is_null($value)) {
                        $type = PDO::PARAM_NULL;
                    }
                    
                    // Wenn der Key ein Integer ist, ist es ein positionsbasierter Parameter
                    if (is_int($key)) {
                        $stmt->bindValue($key + 1, $value, $type);
                    } else {
                        // Benannter Parameter
                        $stmt->bindValue(':' . $key, $value, $type);
                    }
                }
            }
            
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            // Fehler protokollieren
            error_log("Database Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function lastInsertId(){
        return $this->conn->lastInsertId();
    }
    
    // Methode zum Schließen der Datenbankverbindung
    public static function close() {
        self::$instance = null;
    }

    // Statische Methoden für einfacheren Zugriff
    public static function staticSelect($sql, $params = []) {
        return self::getInstance()->select($sql, $params);
    }
    
    public static function staticSelectOne($sql, $params = []) {
        return self::getInstance()->selectOne($sql, $params);
    }
    
    public static function staticInsert($table, $data) {
        return self::getInstance()->insert($table, $data);
    }
    
    public static function staticUpdate($table, $data, $where, $whereParams = []) {
        return self::getInstance()->update($table, $data, $where, $whereParams);
    }
    
    public static function staticQuery($sql, $params = []) {
        return self::getInstance()->query($sql, $params);
    }
}


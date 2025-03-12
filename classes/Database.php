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
            $logger = Logger::getInstance();
            $logger->critical("Datenbankverbindung fehlgeschlagen: " . $e->getMessage(), 'database');
            die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
        }
    }
    
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
        
        try {
            $this->query($sql, array_values($data));
            return $this->conn->lastInsertId();
        } catch (Exception $e) {
            $logger = Logger::getInstance();
            $logger->error("Insert Error: " . $e->getMessage(), 'database', ['table' => $table, 'data' => $data]);
            throw $e;
        }
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
        try {
            return $this->query($sql, $params);
        } catch (Exception $e) {
            $logger = Logger::getInstance();
            $logger->error("Update Error: " . $e->getMessage(), 'database', ['table' => $table, 'data' => $data, 'where' => $where]);
            throw $e;
        }
    }

    /*
    // Beispiel 1: Löschen mit einfacher Bedingung
    $db->delete('users', ['id' => 5]);

    // Beispiel 2: Löschen mit mehreren Bedingungen
    $db->delete('orders', ['status' => 'cancelled', 'created_at < ?' => '2023-01-01']);

    // Beispiel 3: Löschen mit komplexer Bedingung als String
    $db->delete('products', 'price > 100 AND stock = 0');

    // Beispiel 4: Löschen mit dem speziellen 'where'-Format
    $db->delete('logs', ['where' => 'created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)']);

    // Beispiel 5: Verwenden der statischen Methode
    Database::staticDelete('temp_data', ['expired' => true]);
    */

    public function delete($table, $where = array("where" => "arg")) {
        try {
            $whereClause = "";
            $params = [];
            
            // Wenn $where ein String ist, verwenden wir ihn direkt als WHERE-Klausel
            if (is_string($where)) {
                $whereClause = $where;
            } 
            // Wenn $where ein Array ist, bauen wir die WHERE-Klausel
            else if (is_array($where)) {
                $whereParts = [];
                
                foreach ($where as $key => $value) {
                    if ($key === 'where') {
                        // Wenn der Schlüssel 'where' ist, verwenden wir den Wert direkt
                        $whereParts[] = $value;
                    } else {
                        // Sonst fügen wir eine Bedingung mit Platzhalter hinzu
                        $whereParts[] = "{$key} = ?";
                        $params[] = $value;
                    }
                }
                
                if (!empty($whereParts)) {
                    $whereClause = implode(' AND ', $whereParts);
                }
            }
            
            // SQL-Abfrage erstellen
            $sql = "DELETE FROM {$table}";
            if (!empty($whereClause)) {
                $sql .= " WHERE {$whereClause}";
            }
            
            // Debug-Ausgabe
            $logger = Logger::getInstance();
            $logger->debug("Executing DELETE query", 'database', [
                'sql' => $sql,
                'params' => $params,
                'table' => $table,
                'where' => $where
            ]);
            
            // Wenn die WHERE-Klausel ein String ist und keine Parameter hat,
            // verwenden wir die query-Methode direkt
            if (is_string($where) && empty($params)) {
                $result = $this->query($sql);
                $rowCount = $result->rowCount();
                
                $logger->debug("DELETE query result (direct)", 'database', [
                    'rowCount' => $rowCount,
                    'sql' => $sql
                ]);
                
                return $rowCount;
            }
            
            // Ansonsten bereiten wir die Abfrage vor und führen sie aus
            $stmt = $this->conn->prepare($sql);
            
            // Parameter binden
            if (!empty($params)) {
                $i = 1;
                foreach ($params as $value) {
                    $type = PDO::PARAM_STR;
                    if (is_int($value)) {
                        $type = PDO::PARAM_INT;
                    } elseif (is_bool($value)) {
                        $type = PDO::PARAM_BOOL;
                    } elseif (is_null($value)) {
                        $type = PDO::PARAM_NULL;
                    }
                    
                    $stmt->bindValue($i, $value, $type);
                    $i++;
                }
            }
            
            // Abfrage ausführen
            $result = $stmt->execute();
            
            // Anzahl der betroffenen Zeilen
            $rowCount = $stmt->rowCount();
            
            // Debug-Ausgabe
            $logger->debug("Executing query","DELETE query result (prepared)", 'database', [
                'result' => $result,
                'rowCount' => $rowCount,
                'sql' => $sql,
                'params' => $params
            ]);
            echo 'kein fehler';
            // Anzahl der betroffenen Zeilen zurückgeben
            return $rowCount;
        } catch (Exception $e) {
            echo 'fehler';
            $logger = Logger::getInstance();
            $logger->error("Delete Error: " . $e->getMessage(), 'database', [
                'table' => $table,
                'where' => $where,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    

    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    $type = PDO::PARAM_STR;
                    if (is_int($value)) {
                        $type = PDO::PARAM_INT;
                    } elseif (is_bool($value)) {
                        $type = PDO::PARAM_BOOL;
                    } elseif (is_null($value)) {
                        $type = PDO::PARAM_NULL;
                    }
                    
                    if (is_int($key)) {
                        $stmt->bindValue($key + 1, $value, $type);
                    } else {
                        $stmt->bindValue(':' . $key, $value, $type);
                    }
                }
            }
            
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            $logger = Logger::getInstance();
            $logger->error("Database Error: " . $e->getMessage(), 'database', ['query' => $sql, 'params' => $params]);
            throw new Exception("Database Error: " . $e->getMessage());
        }
    }

    public function lastInsertId(){
        return $this->conn->lastInsertId();
    }
    
    public static function close() {
        self::$instance = null;
    }

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


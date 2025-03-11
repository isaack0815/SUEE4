<?php
/**
 * Logger-Klasse für das Logging von Systemereignissen
 */
class Logger {
    private static $instance = null;
    private $db;
    private $logTable = 'system_logs';
    
    /**
     * Privater Konstruktor für Singleton-Pattern
     */
    private function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Singleton-Instanz abrufen
     * 
     * @return Logger Die Logger-Instanz
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Debug-Nachricht loggen
     * 
     * @param string $message Die Nachricht
     * @param string $context Der Kontext (z.B. 'auth', 'admin', etc.)
     * @param array $data Zusätzliche Daten
     */
    public function debug($message, $context = 'system', $data = []) {
        $this->log('debug', $message, $context, $data);
    }
    
    /**
     * Info-Nachricht loggen
     * 
     * @param string $message Die Nachricht
     * @param string $context Der Kontext (z.B. 'auth', 'admin', etc.)
     * @param array $data Zusätzliche Daten
     */
    public function info($message, $context = 'system', $data = []) {
        $this->log('info', $message, $context, $data);
    }
    
    /**
     * Warnungs-Nachricht loggen
     * 
     * @param string $message Die Nachricht
     * @param string $context Der Kontext (z.B. 'auth', 'admin', etc.)
     * @param array $data Zusätzliche Daten
     */
    public function warning($message, $context = 'system', $data = []) {
        $this->log('warning', $message, $context, $data);
    }
    
    /**
     * Fehler-Nachricht loggen
     * 
     * @param string $message Die Nachricht
     * @param string $context Der Kontext (z.B. 'auth', 'admin', etc.)
     * @param array $data Zusätzliche Daten
     */
    public function error($message, $context = 'system', $data = []) {
        $this->log('error', $message, $context, $data);
    }
    
    /**
     * Kritische Nachricht loggen
     * 
     * @param string $message Die Nachricht
     * @param string $context Der Kontext (z.B. 'auth', 'admin', etc.)
     * @param array $data Zusätzliche Daten
     */
    public function critical($message, $context = 'system', $data = []) {
        $this->log('critical', $message, $context, $data);
    }
    
    /**
     * Nachricht in die Datenbank loggen
     * 
     * @param string $level Log-Level (debug, info, warning, error, critical)
     * @param string $message Die Nachricht
     * @param string $context Der Kontext (z.B. 'auth', 'admin', etc.)
     * @param array $data Zusätzliche Daten
     */
    private function log($level, $message, $context, $data = []) {
        try {
            // Sicherstellen, dass die Log-Tabelle existiert
            $this->ensureLogTableExists();
            
            // Benutzer-ID abrufen, falls ein Benutzer angemeldet ist
            $userId = null;
            if (class_exists('Auth')) {
                $auth = new Auth($this->db);
                if ($auth->isLoggedIn()) {
                    $user = $auth->getUser();
                    $userId = $user['user_id'] ?? null;
                }
            }
            
            // IP-Adresse abrufen
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
            
            // Daten als JSON kodieren
            $jsonData = !empty($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : null;
            
            // Log in die Datenbank schreiben
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("
                INSERT INTO {$this->logTable} 
                (level, message, context, user_id, ip_address, additional_data, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([$level, $message, $context, $userId, $ip, $jsonData]);
            
            return true;
        } catch (PDOException $e) {
            // Fehler beim Loggen - in die PHP-Fehlerprotokollierung schreiben
            error_log("Fehler beim Loggen: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Logs aus der Datenbank abrufen
     * 
     * @param array $filter Filter-Optionen (level, context, user_id, date_from, date_to)
     * @param int $limit Anzahl der abzurufenden Logs
     * @param int $offset Offset für Paginierung
     * @return array Array mit Logs
     */
    public function getLogs($filter = [], $limit = 100, $offset = 0) {
        try {
            // Sicherstellen, dass die Log-Tabelle existiert
            $this->ensureLogTableExists();
            
            // SQL-Abfrage vorbereiten
            $sql = "SELECT * FROM {$this->logTable} WHERE 1=1";
            $params = [];
            
            // Filter anwenden
            if (isset($filter['level']) && !empty($filter['level'])) {
                $sql .= " AND level = ?";
                $params[] = $filter['level'];
            }
            
            if (isset($filter['context']) && !empty($filter['context'])) {
                $sql .= " AND context = ?";
                $params[] = $filter['context'];
            }
            
            if (isset($filter['user_id']) && !empty($filter['user_id'])) {
                $sql .= " AND user_id = ?";
                $params[] = $filter['user_id'];
            }
            
            if (isset($filter['date_from']) && !empty($filter['date_from'])) {
                $sql .= " AND created_at >= ?";
                $params[] = $filter['date_from'];
            }
            
            if (isset($filter['date_to']) && !empty($filter['date_to'])) {
                $sql .= " AND created_at <= ?";
                $params[] = $filter['date_to'];
            }
            
            // Sortierung und Limit
            $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $params[] = (int)$limit;
            $params[] = (int)$offset;
            
            // Abfrage ausführen
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Zusätzliche Daten dekodieren
            foreach ($logs as &$log) {
                if (isset($log['additional_data']) && !empty($log['additional_data'])) {
                    $log['additional_data'] = json_decode($log['additional_data'], true);
                } else {
                    $log['additional_data'] = [];
                }
            }
            
            return $logs;
        } catch (PDOException $e) {
            error_log("Fehler beim Abrufen der Logs: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Logs aus der Datenbank löschen
     * 
     * @param array $filter Filter-Optionen (level, context, user_id, date_from, date_to)
     * @return bool Erfolg oder Misserfolg
     */
    public function deleteLogs($filter = []) {
        try {
            // Sicherstellen, dass die Log-Tabelle existiert
            $this->ensureLogTableExists();
            
            // SQL-Abfrage vorbereiten
            $sql = "DELETE FROM {$this->logTable} WHERE 1=1";
            $params = [];
            
            // Filter anwenden
            if (isset($filter['level']) && !empty($filter['level'])) {
                $sql .= " AND level = ?";
                $params[] = $filter['level'];
            }
            
            if (isset($filter['context']) && !empty($filter['context'])) {
                $sql .= " AND context = ?";
                $params[] = $filter['context'];
            }
            
            if (isset($filter['user_id']) && !empty($filter['user_id'])) {
                $sql .= " AND user_id = ?";
                $params[] = $filter['user_id'];
            }
            
            if (isset($filter['date_from']) && !empty($filter['date_from'])) {
                $sql .= " AND created_at >= ?";
                $params[] = $filter['date_from'];
            }
            
            if (isset($filter['date_to']) && !empty($filter['date_to'])) {
                $sql .= " AND created_at <= ?";
                $params[] = $filter['date_to'];
            }
            
            // Abfrage ausführen
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            
            return true;
        } catch (PDOException $e) {
            error_log("Fehler beim Löschen der Logs: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Sicherstellen, dass die Log-Tabelle existiert
     */
    private function ensureLogTableExists() {
        try {
            $conn = $this->db->getConnection();
            
            // Überprüfen, ob die Tabelle existiert
            $stmt = $conn->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$this->logTable]);
            
            if ($stmt->rowCount() === 0) {
                // Tabelle existiert nicht, erstellen
                $sql = "
                    CREATE TABLE {$this->logTable} (
                        log_id INT(11) NOT NULL AUTO_INCREMENT,
                        level VARCHAR(20) NOT NULL,
                        message TEXT NOT NULL,
                        context VARCHAR(50) DEFAULT 'system',
                        user_id INT(11) DEFAULT NULL,
                        ip_address VARCHAR(45) DEFAULT NULL,
                        additional_data TEXT DEFAULT NULL,
                        created_at DATETIME NOT NULL,
                        PRIMARY KEY (log_id),
                        KEY level (level),
                        KEY context (context),
                        KEY user_id (user_id),
                        KEY created_at (created_at)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                ";
                
                $conn->exec($sql);
            }
        } catch (PDOException $e) {
            error_log("Fehler beim Erstellen der Log-Tabelle: " . $e->getMessage());
        }
    }
}


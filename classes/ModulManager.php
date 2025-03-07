<?php
class ModuleManager {
    private $db;
    private $dbConnection;
    private $dashboardModulesDir;
    private $systemModulesDir;
    private $tempDir;
    private $dashboard;
    
    // Konstanten für Modultypen
    const TYPE_DASHBOARD = 'dashboard';
    const TYPE_SYSTEM = 'system';
    
    /**
     * Konstruktor
     */
    public function __construct() {
        // Datenbankverbindung erstellen
        $this->db = Database::getInstance();
        $this->dbConnection = $this->db->getConnection();
        
        // Pfade initialisieren
        $this->dashboardModulesDir = __DIR__ . '/../includes/dashboard_modules/';
        $this->systemModulesDir = __DIR__ . '/../includes/system_modules/';
        $this->tempDir = __DIR__ . '/../temp/';
        
        // Dashboard-Instanz erstellen
        $this->dashboard = new Dashboard();
        
        // Temporäres Verzeichnis erstellen, falls es nicht existiert
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }
        
        // System-Modulverzeichnis erstellen, falls es nicht existiert
        if (!is_dir($this->systemModulesDir)) {
            mkdir($this->systemModulesDir, 0755, true);
        }
    }
    
    /**
     * Modul hochladen und temporär speichern
     * 
     * @param array $file $_FILES['module']
     * @param string $type Modultyp (dashboard oder system)
     * @return array Informationen zum hochgeladenen Modul oder Fehlermeldung
     */
    public function uploadModule($file, $type = self::TYPE_DASHBOARD) {
        // Überprüfen, ob es sich um eine ZIP-Datei handelt
        if ($file['type'] !== 'application/zip' && $file['type'] !== 'application/x-zip-compressed') {
            return ['success' => false, 'message' => 'Die hochgeladene Datei ist keine ZIP-Datei.'];
        }
        
        // Temporären Dateinamen generieren
        $tempFile = $this->tempDir . uniqid('module_') . '.zip';
        
        // Datei in das temporäre Verzeichnis verschieben
        if (!move_uploaded_file($file['tmp_name'], $tempFile)) {
            return ['success' => false, 'message' => 'Die Datei konnte nicht hochgeladen werden.'];
        }
        
        // ZIP-Datei extrahieren
        $extractDir = $this->tempDir . uniqid('extract_');
        mkdir($extractDir, 0755);
        
        $zip = new ZipArchive();
        if ($zip->open($tempFile) !== true) {
            return ['success' => false, 'message' => 'Die ZIP-Datei konnte nicht geöffnet werden.'];
        }
        
        $zip->extractTo($extractDir);
        $zip->close();
        
        // Überprüfen, ob info.php existiert
        if (!file_exists($extractDir . '/info.php')) {
            $this->cleanupTemp($tempFile, $extractDir);
            return ['success' => false, 'message' => 'Die Datei info.php fehlt im Modul.'];
        }
        
        // Moduldetails aus info.php laden
        include $extractDir . '/info.php';
        
        if (!isset($moduldetails) || !is_array($moduldetails)) {
            $this->cleanupTemp($tempFile, $extractDir);
            return ['success' => false, 'message' => 'Die Datei info.php enthält keine gültigen Moduldetails.'];
        }
        
        // Erforderliche Felder überprüfen
        $requiredFields = ['name', 'version', 'author', 'description', 'files'];
        foreach ($requiredFields as $field) {
            if (!isset($moduldetails[$field])) {
                $this->cleanupTemp($tempFile, $extractDir);
                return ['success' => false, 'message' => "Das Feld '$field' fehlt in den Moduldetails."];
            }
        }
        
        // Modultyp hinzufügen
        $moduldetails['type'] = $type;
        
        // Moduldetails zurückgeben
        return [
            'success' => true,
            'message' => 'Modul erfolgreich hochgeladen.',
            'details' => $moduldetails,
            'tempFile' => $tempFile,
            'extractDir' => $extractDir,
            'type' => $type
        ];
    }
    
    /**
     * Modul installieren
     * 
     * @param array $moduleInfo Informationen zum Modul
     * @return array Ergebnis der Installation
     */
    public function installModule($moduleInfo) {
        $details = $moduleInfo['details'];
        $extractDir = $moduleInfo['extractDir'];
        $type = isset($moduleInfo['type']) ? $moduleInfo['type'] : self::TYPE_DASHBOARD;
        
        // Überprüfen, ob das Modul bereits installiert ist
        $tableName = $this->getModuleTableName($type);
        $stmt = $this->dbConnection->prepare("SELECT * FROM $tableName WHERE name = ?");
        $stmt->execute([$details['name']]);
        
        if ($stmt->rowCount() > 0) {
            $module = $stmt->fetch(PDO::FETCH_ASSOC);
            // Wenn das Modul bereits installiert ist, aber eine neuere Version hochgeladen wurde
            if (isset($module['version']) && version_compare($details['version'], $module['version'], '>')) {
                return $this->updateModule($moduleInfo, $module['module_id']);
            } else {
                $this->cleanupTemp($moduleInfo['tempFile'], $extractDir);
                return ['success' => false, 'message' => 'Das Modul ist bereits installiert.'];
            }
        }
        
        // Zielverzeichnis basierend auf Modultyp bestimmen
        $modulesDir = $this->getModuleDirectory($type);
        
        // Dateien aus dem Modul extrahieren und speichern
        foreach ($details['files'] as $path => $content) {
            $fullPath = $modulesDir . basename($path);
            $decodedContent = base64_decode($content);
            
            if ($decodedContent === false) {
                $this->cleanupTemp($moduleInfo['tempFile'], $extractDir);
                return ['success' => false, 'message' => "Der Inhalt der Datei '$path' konnte nicht dekodiert werden."];
            }
            
            // Verzeichnis erstellen, falls es nicht existiert
            $dir = dirname($fullPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            
            // Datei speichern
            if (file_put_contents($fullPath, $decodedContent) === false) {
                $this->cleanupTemp($moduleInfo['tempFile'], $extractDir);
                return ['success' => false, 'message' => "Die Datei '$path' konnte nicht gespeichert werden."];
            }
        }
        
        // Installationsskript ausführen, falls vorhanden
        if (isset($details['install']) && !empty($details['install'])) {
            $installScript = $extractDir . '/' . $details['install'];
            if (file_exists($installScript)) {
                // Variablen für das Installationsskript bereitstellen
                $moduleId = $details['name'];
                $db = $this->db;  // Übergebe die Database-Instanz, nicht die PDO-Verbindung
                $moduleType = $type;
                
                // Installationsskript einbinden
                include $installScript;
            }
        }
        
        // Modul in der Datenbank registrieren
        $moduleId = $this->registerModuleInDatabase($details, $type);
        
        // Temporäre Dateien löschen
        $this->cleanupTemp($moduleInfo['tempFile'], $extractDir);
        
        return [
            'success' => true,
            'message' => 'Modul erfolgreich installiert.',
            'module_id' => $moduleId,
            'type' => $type
        ];
    }
    
    /**
     * Modul aktualisieren
     * 
     * @param array $moduleInfo Informationen zum Modul
     * @param int $moduleId ID des zu aktualisierenden Moduls
     * @return array Ergebnis der Aktualisierung
     */
    public function updateModule($moduleInfo, $moduleId) {
        $details = $moduleInfo['details'];
        $extractDir = $moduleInfo['extractDir'];
        $type = isset($moduleInfo['type']) ? $moduleInfo['type'] : self::TYPE_DASHBOARD;
        
        // Tabellennamen basierend auf Modultyp bestimmen
        $tableName = $this->getModuleTableName($type);
        
        // Aktuelles Modul aus der Datenbank abrufen
        $stmt = $this->dbConnection->prepare("SELECT * FROM $tableName WHERE module_id = ?");
        $stmt->execute([$moduleId]);
        $currentModule = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$currentModule) {
            $this->cleanupTemp($moduleInfo['tempFile'], $extractDir);
            return ['success' => false, 'message' => 'Das zu aktualisierende Modul wurde nicht gefunden.'];
        }
        
        // Überprüfen, ob die neue Version neuer ist
        if (isset($currentModule['version']) && version_compare($details['version'], $currentModule['version'], '<=')) {
            $this->cleanupTemp($moduleInfo['tempFile'], $extractDir);
            return ['success' => false, 'message' => 'Die installierte Version ist bereits aktuell oder neuer.'];
        }
        
        // Aktualisierungsskript ausführen, falls vorhanden
        if (isset($details['update']) && !empty($details['update'])) {
            $updateScript = $extractDir . '/' . $details['update'];
            if (file_exists($updateScript)) {
                // Variablen für das Aktualisierungsskript bereitstellen
                $currentVersion = isset($currentModule['version']) ? $currentModule['version'] : '1.0';
                $db = $this->db;  // Übergebe die Database-Instanz, nicht die PDO-Verbindung
                $moduleType = $type;
                
                // Aktualisierungsskript einbinden
                include $updateScript;
            }
        }
        
        // Zielverzeichnis basierend auf Modultyp bestimmen
        $modulesDir = $this->getModuleDirectory($type);
        
        // Dateien aus dem Modul extrahieren und speichern
        foreach ($details['files'] as $path => $content) {
            $fullPath = $modulesDir . basename($path);
            $decodedContent = base64_decode($content);
            
            if ($decodedContent === false) {
                $this->cleanupTemp($moduleInfo['tempFile'], $extractDir);
                return ['success' => false, 'message' => "Der Inhalt der Datei '$path' konnte nicht dekodiert werden."];
            }
            
            // Verzeichnis erstellen, falls es nicht existiert
            $dir = dirname($fullPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            
            // Datei speichern
            if (file_put_contents($fullPath, $decodedContent) === false) {
                $this->cleanupTemp($moduleInfo['tempFile'], $extractDir);
                return ['success' => false, 'message' => "Die Datei '$path' konnte nicht gespeichert werden."];
            }
        }
        
        // Modul in der Datenbank aktualisieren
        try {
            // Überprüfen, ob die Spalten 'version' und 'description' existieren
            $stmt = $this->dbConnection->prepare("SHOW COLUMNS FROM $tableName LIKE 'version'");
            $stmt->execute();
            $versionExists = $stmt->rowCount() > 0;
            
            $stmt = $this->dbConnection->prepare("SHOW COLUMNS FROM $tableName LIKE 'author'");
            $stmt->execute();
            $authorExists = $stmt->rowCount() > 0;
            
            if ($versionExists) {
                $sql = "UPDATE $tableName SET description = ?, updated_at = NOW()";
                $params = [$details['description']];
                
                if ($versionExists) {
                    $sql .= ", version = ?";
                    $params[] = $details['version'];
                }
                
                if ($authorExists) {
                    $sql .= ", author = ?";
                    $params[] = $details['author'];
                }
                
                $sql .= " WHERE module_id = ?";
                $params[] = $moduleId;
                
                $stmt = $this->dbConnection->prepare($sql);
                $stmt->execute($params);
            } else {
                // Wenn die Spalten nicht existieren, aktualisieren wir nur die Beschreibung
                $stmt = $this->dbConnection->prepare("
                    UPDATE $tableName 
                    SET description = ?, updated_at = NOW() 
                    WHERE module_id = ?
                ");
                $stmt->execute([$details['description'], $moduleId]);
            }
        } catch (PDOException $e) {
            $this->cleanupTemp($moduleInfo['tempFile'], $extractDir);
            return ['success' => false, 'message' => 'Fehler beim Aktualisieren des Moduls: ' . $e->getMessage()];
        }
        
        // Temporäre Dateien löschen
        $this->cleanupTemp($moduleInfo['tempFile'], $extractDir);
        
        return [
            'success' => true,
            'message' => 'Modul erfolgreich aktualisiert.',
            'module_id' => $moduleId,
            'type' => $type
        ];
    }
    
    /**
     * Modul deinstallieren
     * 
     * @param int $moduleId ID des zu deinstallierenden Moduls
     * @param string $type Modultyp (dashboard oder system)
     * @return array Ergebnis der Deinstallation
     */
    public function uninstallModule($moduleId, $type = self::TYPE_DASHBOARD) {
        // Tabellennamen basierend auf Modultyp bestimmen
        $tableName = $this->getModuleTableName($type);
        
        // Modul aus der Datenbank abrufen
        $stmt = $this->dbConnection->prepare("SELECT * FROM $tableName WHERE module_id = ?");
        $stmt->execute([$moduleId]);
        $module = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$module) {
            return ['success' => false, 'message' => 'Das zu deinstallierende Modul wurde nicht gefunden.'];
        }
        
        // Zielverzeichnis basierend auf Modultyp bestimmen
        $modulesDir = $this->getModuleDirectory($type);
        
        // Überprüfen, ob eine Deinstallationsdatei existiert
        $uninstallFile = $modulesDir . $module['name'] . '/uninstall.php';
        if (file_exists($uninstallFile)) {
            // Variablen für das Deinstallationsskript bereitstellen
            $db = $this->db;  // Übergebe die Database-Instanz, nicht die PDO-Verbindung
            $moduleType = $type;
            
            // Deinstallationsskript einbinden
            include $uninstallFile;
        }
        
        // Modul-Dateien löschen
        $moduleDir = $modulesDir . $module['name'];
        if (is_dir($moduleDir)) {
            $this->deleteDirectory($moduleDir);
        }
        
        // Modul aus der Datenbank entfernen
        $stmt = $this->dbConnection->prepare("DELETE FROM $tableName WHERE module_id = ?");
        $stmt->execute([$moduleId]);
        
        // Benutzereinstellungen für dieses Modul entfernen, falls es sich um ein Dashboard-Modul handelt
        if ($type === self::TYPE_DASHBOARD) {
            $stmt = $this->dbConnection->prepare("DELETE FROM user_dashboard_settings WHERE module_id = ?");
            $stmt->execute([$moduleId]);
        }
        
        return [
            'success' => true,
            'message' => 'Modul erfolgreich deinstalliert.',
            'type' => $type
        ];
    }
    
    /**
     * Modul in der Datenbank registrieren
     * 
     * @param array $details Moduldetails
     * @param string $type Modultyp (dashboard oder system)
     * @return int ID des registrierten Moduls
     */
    private function registerModuleInDatabase($details, $type = self::TYPE_DASHBOARD) {
        // Tabellennamen basierend auf Modultyp bestimmen
        $tableName = $this->getModuleTableName($type);
        
        // Hauptdatei des Moduls ermitteln (erste Datei im Array)
        $filePaths = array_keys($details['files']);
        $filePath = basename(reset($filePaths));
        
        // Icon aus den Details extrahieren oder Standardwert verwenden
        $icon = isset($details['icon']) ? $details['icon'] : 'box';
        
        try {
            // Überprüfen, ob die Tabelle existiert, falls nicht, erstellen
            $this->ensureModuleTableExists($type);
            
            // Überprüfen, ob die Spalten 'version' und 'author' existieren
            $stmt = $this->dbConnection->prepare("SHOW COLUMNS FROM $tableName LIKE 'version'");
            $stmt->execute();
            $versionExists = $stmt->rowCount() > 0;
            
            $stmt = $this->dbConnection->prepare("SHOW COLUMNS FROM $tableName LIKE 'author'");
            $stmt->execute();
            $authorExists = $stmt->rowCount() > 0;
            
            // SQL-Abfrage basierend auf den vorhandenen Spalten erstellen
            if ($versionExists && $authorExists) {
                // Beide Spalten existieren
                $stmt = $this->dbConnection->prepare("
                    INSERT INTO $tableName 
                    (name, description, icon, file_path, version, author, is_active, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, 1, NOW(), NOW())
                ");
                
                $stmt->execute([
                    $details['name'],
                    $details['description'],
                    $icon,
                    $filePath,
                    $details['version'],
                    $details['author']
                ]);
            } else if ($versionExists) {
                // Nur 'version' existiert
                $stmt = $this->dbConnection->prepare("
                    INSERT INTO $tableName 
                    (name, description, icon, file_path, version, is_active, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())
                ");
                
                $stmt->execute([
                    $details['name'],
                    $details['description'],
                    $icon,
                    $filePath,
                    $details['version']
                ]);
            } else if ($authorExists) {
                // Nur 'author' existiert
                $stmt = $this->dbConnection->prepare("
                    INSERT INTO $tableName 
                    (name, description, icon, file_path, author, is_active, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())
                ");
                
                $stmt->execute([
                    $details['name'],
                    $details['description'],
                    $icon,
                    $filePath,
                    $details['author']
                ]);
            } else {
                // Keine der Spalten existiert
                $stmt = $this->dbConnection->prepare("
                    INSERT INTO $tableName 
                    (name, description, icon, file_path, is_active, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, 1, NOW(), NOW())
                ");
                
                $stmt->execute([
                    $details['name'],
                    $details['description'],
                    $icon,
                    $filePath
                ]);
            }
            
            return $this->dbConnection->lastInsertId();
        } catch (PDOException $e) {
            // Fehler protokollieren
            error_log("Error in registerModuleInDatabase: " . $e->getMessage());
            
            // Fallback: Nur die grundlegenden Felder einfügen
            $stmt = $this->dbConnection->prepare("
                INSERT INTO $tableName 
                (name, description, icon, file_path, is_active, created_at, updated_at) 
                VALUES (?, ?, ?, ?, 1, NOW(), NOW())
            ");
            
            $stmt->execute([
                $details['name'],
                $details['description'],
                $icon,
                $filePath
            ]);
            
            return $this->dbConnection->lastInsertId();
        }
    }
    
    /**
     * Stellt sicher, dass die Modultabelle existiert
     * 
     * @param string $type Modultyp (dashboard oder system)
     */
    private function ensureModuleTableExists($type) {
        $tableName = $this->getModuleTableName($type);
        
        // Überprüfen, ob die Tabelle existiert
        $stmt = $this->dbConnection->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$tableName]);
        
        if ($stmt->rowCount() === 0) {
            // Tabelle existiert nicht, erstellen
            $sql = "
                CREATE TABLE $tableName (
                    module_id INT(11) NOT NULL AUTO_INCREMENT,
                    name VARCHAR(255) NOT NULL,
                    description TEXT,
                    icon VARCHAR(50) DEFAULT 'box',
                    file_path VARCHAR(255) NOT NULL,
                    version VARCHAR(50),
                    author VARCHAR(255),
                    is_active TINYINT(1) DEFAULT 1,
                    created_at DATETIME,
                    updated_at DATETIME,
                    PRIMARY KEY (module_id),
                    UNIQUE KEY name (name)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ";
            
            $this->dbConnection->exec($sql);
        }
    }
    
    /**
     * Temporäre Dateien löschen
     * 
     * @param string $tempFile Pfad zur temporären ZIP-Datei
     * @param string $extractDir Pfad zum Extraktionsverzeichnis
     */
    private function cleanupTemp($tempFile, $extractDir) {
        // Temporäre ZIP-Datei löschen
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
        
        // Extraktionsverzeichnis löschen
        if (is_dir($extractDir)) {
            $this->deleteDirectory($extractDir);
        }
    }
    
    /**
     * Verzeichnis rekursiv löschen
     * 
     * @param string $dir Pfad zum zu löschenden Verzeichnis
     */
    private function deleteDirectory($dir) {
        if (!is_dir($dir)) {
            return;
        }
        
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object === '.' || $object === '..') {
                continue;
            }
            
            $path = $dir . '/' . $object;
            
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
        
        rmdir($dir);
    }
    
    /**
     * Alle installierten Module abrufen
     * 
     * @param string $type Modultyp (dashboard oder system)
     * @return array Liste der installierten Module
     */
    public function getAllModules($type = self::TYPE_DASHBOARD) {
        try {
            $tableName = $this->getModuleTableName($type);
            
            // Überprüfen, ob die Tabelle existiert
            $stmt = $this->dbConnection->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$tableName]);
            
            if ($stmt->rowCount() === 0) {
                // Tabelle existiert nicht
                return [];
            }
            
            $stmt = $this->dbConnection->query("SELECT * FROM $tableName ORDER BY name");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getAllModules: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Modul aktivieren oder deaktivieren
     * 
     * @param int $moduleId ID des Moduls
     * @param bool $active Aktivierungsstatus
     * @param string $type Modultyp (dashboard oder system)
     * @return array Ergebnis der Aktivierung/Deaktivierung
     */
    public function toggleModuleActive($moduleId, $active, $type = self::TYPE_DASHBOARD) {
        $tableName = $this->getModuleTableName($type);
        
        $stmt = $this->dbConnection->prepare("UPDATE $tableName SET is_active = ? WHERE module_id = ?");
        $stmt->execute([$active ? 1 : 0, $moduleId]);
        
        return [
            'success' => true,
            'message' => $active ? 'Modul erfolgreich aktiviert.' : 'Modul erfolgreich deaktiviert.',
            'type' => $type
        ];
    }
    
    /**
     * Gibt den Tabellennamen für den angegebenen Modultyp zurück
     * 
     * @param string $type Modultyp (dashboard oder system)
     * @return string Tabellenname
     */
    private function getModuleTableName($type) {
        return $type === self::TYPE_SYSTEM ? 'system_modules' : 'dashboard_modules';
    }
    
    /**
     * Gibt das Verzeichnis für den angegebenen Modultyp zurück
     * 
     * @param string $type Modultyp (dashboard oder system)
     * @return string Verzeichnispfad
     */
    private function getModuleDirectory($type) {
        return $type === self::TYPE_SYSTEM ? $this->systemModulesDir : $this->dashboardModulesDir;
    }
}
?>


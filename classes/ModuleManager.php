<?php
/**
 * ModuleManager Klasse
 * 
 * Verwaltet das Hochladen, Installieren, Aktualisieren und Deinstallieren von Modulen
 */
class ModuleManager {
    private $db;
    private $dbConnection;
    private $uploadDir;
    private $moduleDir;
    private $tempDir;
    private $lang;
    private $error;
    private $logger;
    
    // Konstanten für Modultypen
    const TYPE_DASHBOARD = 'dashboard';
    const TYPE_SYSTEM = 'system';
    
    /**
     * Konstruktor
     */
    public function __construct($db = null, $lang = null) {
        // Datenbankverbindung erstellen
        if ($db === null) {
            $this->db = Database::getInstance();
            $this->dbConnection = $this->db->getConnection();
        } else {
            $this->db = $db;
            $this->dbConnection = $db->getConnection();
        }
        
        $this->lang = $lang;
        $this->error = null;
        $this->logger = Logger::getInstance();
        
        // Pfade initialisieren
        $this->uploadDir = __DIR__ . '/../upload/modules/';
        $this->moduleDir = $_SERVER['DOCUMENT_ROOT'];
        $this->tempDir = __DIR__ . '/../temp/';
        
        // Verzeichnisse erstellen, falls sie nicht existieren
        $this->ensureDirectoriesExist();
    }
    
    /**
     * Stellt sicher, dass alle benötigten Verzeichnisse existieren
     */
    private function ensureDirectoriesExist() {
        $directories = [$this->uploadDir, $this->moduleDir, $this->tempDir];
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }
    
    /**
     * Gibt den letzten Fehler zurück
     */
    public function getError() {
        return $this->error;
    }
    
    /**
     * Modul hochladen und in der Datenbank registrieren
     */
    public function uploadModule($file, $type = self::TYPE_DASHBOARD) {
        try {
            // Überprüfen, ob eine Datei hochgeladen wurde
            if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
                throw new Exception("Keine Datei hochgeladen");
            }
            
            // Überprüfen, ob es sich um eine ZIP-Datei handelt
            $fileInfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $fileInfo->file($file['tmp_name']);
            
            if ($mimeType !== 'application/zip' && $mimeType !== 'application/x-zip-compressed') {
                throw new Exception("Die hochgeladene Datei ist keine ZIP-Datei");
            }
            
            // Eindeutigen Dateinamen generieren
            $uniqueFilename = uniqid('module_') . '.zip';
            $uploadPath = $this->uploadDir . $uniqueFilename;
            
            // Datei in das Upload-Verzeichnis verschieben
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                throw new Exception("Die Datei konnte nicht hochgeladen werden");
            }
            
            // ZIP-Datei öffnen und info.php extrahieren
            $zip = new ZipArchive();
            if ($zip->open($uploadPath) !== true) {
                throw new Exception("Die ZIP-Datei konnte nicht geöffnet werden");
            }
            
            $infoContent = $zip->getFromName('info.php');
            $zip->close();
            
            if ($infoContent === false) {
                throw new Exception("Die Datei info.php fehlt im Modul");
            }
            
            // Moduldetails aus info.php laden
            $moduleDetails = eval('?>' . $infoContent);
            
            if (!is_array($moduleDetails) || !isset($moduleDetails['name'])) {
                throw new Exception("Ungültige info.php Datei");
            }
            
            // Modul in der Datenbank registrieren
            $moduleId = $this->registerModuleInDatabase($moduleDetails, $uniqueFilename, $type);
            
            $this->logger->info("Modul erfolgreich hochgeladen", "module", [
                'module' => $moduleDetails['name'],
                'id' => $moduleId,
                'type' => $type
            ]);
            
            return [
                'success' => true,
                'message' => 'Modul erfolgreich hochgeladen.',
                'module_id' => $moduleId,
                'type' => $type
            ];
            
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->logger->error("Modul-Upload fehlgeschlagen", "module", ['error' => $this->error]);
            return ['success' => false, 'message' => $this->error];
        }
    }

    /**
     * Modul in der Datenbank registrieren
     */
    private function registerModuleInDatabase($moduleDetails, $filePath, $type = self::TYPE_DASHBOARD) {
        $tableName = $this->getModuleTableName($type);
        $stmt = $this->dbConnection->prepare("
            INSERT INTO $tableName 
            (name, description, version, author, file_path, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
        
        $stmt->execute([
            $moduleDetails['name'],
            $moduleDetails['description'] ?? '',
            $moduleDetails['version'] ?? '1.0',
            $moduleDetails['author'] ?? '',
            $filePath
        ]);
        
        return $this->dbConnection->lastInsertId();
    }
    
    /**
     * Modul installieren
     */
    public function installModule($moduleId, $type = self::TYPE_DASHBOARD) {
        try {
            $tableName = $this->getModuleTableName($type);
            $stmt = $this->dbConnection->prepare("SELECT * FROM $tableName WHERE module_id = ?");
            $stmt->execute([$moduleId]);
            $module = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$module) {
                throw new Exception("Modul nicht gefunden");
            }
            
            $zipPath = $this->uploadDir . $module['file_path'];
            
            if (!file_exists($zipPath)) {
                throw new Exception("ZIP-Datei nicht gefunden");
            }
            
            $zip = new ZipArchive();
            if ($zip->open($zipPath) !== true) {
                throw new Exception("Fehler beim Öffnen der ZIP-Datei");
            }
            
            // Modulverzeichnis erstellen
            $moduleDir = $this->moduleDir;
            if (!is_dir($moduleDir)) {
                mkdir($moduleDir, 0755, true);
            }
            
            // Dateien extrahieren
            $zip->extractTo($moduleDir);
            $zip->close();
            
            // Installationsskript ausführen, falls vorhanden
            $installScript = $moduleDir . '/install/install.php';
            if (file_exists($installScript)) {
                $db = $this->db;
                include $installScript;
            }
            
            // Modul als installiert markieren
            $stmt = $this->dbConnection->prepare("
                UPDATE $tableName 
                SET installed = 1, installed_at = NOW() 
                WHERE module_id = ?
            ");
            $stmt->execute([$moduleId]);
            
            $this->logger->info("Modul erfolgreich installiert", "module", [
                'module' => $module['name'],
                'id' => $moduleId,
                'type' => $type
            ]);
            
            return [
                'success' => true,
                'message' => 'Modul erfolgreich installiert.',
                'module_id' => $moduleId,
                'type' => $type
            ];
            
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->logger->error("Modul-Installation fehlgeschlagen", "module", [
                'id' => $moduleId,
                'type' => $type,
                'error' => $this->error
            ]);
            return ['success' => false, 'message' => $this->error];
        }
    }
    
    /**
     * Modul deinstallieren
     */
    public function uninstallModule($moduleId, $type = self::TYPE_DASHBOARD) {
        try {
            $tableName = $this->getModuleTableName($type);
            $stmt = $this->dbConnection->prepare("SELECT * FROM $tableName WHERE module_id = ?");
            $stmt->execute([$moduleId]);
            $module = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$module) {
                throw new Exception("Modul nicht gefunden");
            }
            
            $moduleDir = $this->moduleDir . $module['name'];
            
            // Deinstallationsskript ausführen, falls vorhanden
            $uninstallScript = $moduleDir . '/uninstall/uninstall.php';
            if (file_exists($uninstallScript)) {
                define('UNINSTALL_SCRIPT', true);
                $db = $this->db;
                include $uninstallScript;
            }
            
            // Modulverzeichnis löschen
            $this->deleteDirectory($moduleDir);
            
            // Modul als deinstalliert markieren
            $stmt = $this->dbConnection->prepare("
                UPDATE $tableName 
                SET installed = 0, installed_at = NULL 
                WHERE module_id = ?
            ");
            $stmt->execute([$moduleId]);
            
            $this->logger->info("Modul erfolgreich deinstalliert", "module", [
                'module' => $module['name'],
                'id' => $moduleId,
                'type' => $type
            ]);
            
            return [
                'success' => true,
                'message' => 'Modul erfolgreich deinstalliert.',
                'module_id' => $moduleId,
                'type' => $type
            ];
            
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->logger->error("Modul-Deinstallation fehlgeschlagen", "module", [
                'id' => $moduleId,
                'type' => $type,
                'error' => $this->error
            ]);
            return ['success' => false, 'message' => $this->error];
        }
    }
    
    /**
     * Modul löschen
     */
    public function deleteModule($moduleId, $type = self::TYPE_DASHBOARD) {
        try {
            $tableName = $this->getModuleTableName($type);
            $stmt = $this->dbConnection->prepare("SELECT * FROM $tableName WHERE module_id = ?");
            $stmt->execute([$moduleId]);
            $module = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$module) {
                throw new Exception("Modul nicht gefunden");
            }
            
            // ZIP-Datei löschen
            $zipPath = $this->uploadDir . $module['file_path'];
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
            
            // Modul aus der Datenbank löschen
            $stmt = $this->dbConnection->prepare("DELETE FROM $tableName WHERE module_id = ?");
            $stmt->execute([$moduleId]);
            
            $this->logger->info("Modul erfolgreich gelöscht", "module", [
                'module' => $module['name'],
                'id' => $moduleId,
                'type' => $type
            ]);
            
            return [
                'success' => true,
                'message' => 'Modul erfolgreich gelöscht.',
                'module_id' => $moduleId,
                'type' => $type
            ];
            
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->logger->error("Modul-Löschung fehlgeschlagen", "module", [
                'id' => $moduleId,
                'type' => $type,
                'error' => $this->error
            ]);
            return ['success' => false, 'message' => $this->error];
        }
    }
    
    /**
     * Alle Module abrufen
     */
    public function getAllModules($type = self::TYPE_DASHBOARD) {
        $tableName = $this->getModuleTableName($type);
        $stmt = $this->dbConnection->query("SELECT * FROM $tableName ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Modul aktivieren oder deaktivieren
     */
    public function toggleModuleActive($moduleId, $active, $type = self::TYPE_DASHBOARD) {
        try {
            $tableName = $this->getModuleTableName($type);
            $stmt = $this->dbConnection->prepare("
                UPDATE $tableName 
                SET is_active = ? 
                WHERE module_id = ?
            ");
            $stmt->execute([$active ? 1 : 0, $moduleId]);
            
            $action = $active ? "aktiviert" : "deaktiviert";
            $this->logger->info("Modul $action", "module", [
                'id' => $moduleId,
                'type' => $type,
                'active' => $active
            ]);
            
            return [
                'success' => true,
                'message' => "Modul erfolgreich $action.",
                'module_id' => $moduleId,
                'type' => $type
            ];
            
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->logger->error("Modul-Aktivierung/Deaktivierung fehlgeschlagen", "module", [
                'id' => $moduleId,
                'type' => $type,
                'active' => $active,
                'error' => $this->error
            ]);
            return ['success' => false, 'message' => $this->error];
        }
    }
    
    /**
     * Hilfsmethode: Gibt den Tabellennamen für den angegebenen Modultyp zurück
     */
    private function getModuleTableName($type) {
        return $type === self::TYPE_SYSTEM ? 'system_modules' : 'dashboard_modules';
    }
    
    /**
     * Hilfsmethode: Verzeichnis rekursiv löschen
     */
    private function deleteDirectory($dir) {
        if (!is_dir($dir)) {
            return;
        }
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir . "/" . $object)) {
                    $this->deleteDirectory($dir . "/" . $object);
                } else {
                    unlink($dir . "/" . $object);
                }
            }
        }
        rmdir($dir);
    }
}


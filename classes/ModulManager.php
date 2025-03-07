<?php
class ModuleManager {
    private $db;
    private $modulesDir;
    private $tempDir;
    private $dashboard;
    
    /**
     * Konstruktor
     */
    public function __construct() {
        global $db;
        $this->db = $db;
        $this->modulesDir = __DIR__ . '/../includes/dashboard_modules/';
        $this->tempDir = __DIR__ . '/../temp/';
        $this->dashboard = new Dashboard();
        
        // Temporäres Verzeichnis erstellen, falls es nicht existiert
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }
    }
    
    /**
     * Modul hochladen und temporär speichern
     * 
     * @param array $file $_FILES['module']
     * @return array Informationen zum hochgeladenen Modul oder Fehlermeldung
     */
    public function uploadModule($file) {
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
        
        // Moduldetails zurückgeben
        return [
            'success' => true,
            'message' => 'Modul erfolgreich hochgeladen.',
            'details' => $moduldetails,
            'tempFile' => $tempFile,
            'extractDir' => $extractDir
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
        
        // Überprüfen, ob das Modul bereits installiert ist
        $stmt = $this->db->prepare("SELECT * FROM dashboard_modules WHERE name = ?");
        $stmt->execute([$details['name']]);
        
        if ($stmt->rowCount() > 0) {
            $module = $stmt->fetch(PDO::FETCH_ASSOC);
            // Wenn das Modul bereits installiert ist, aber eine neuere Version hochgeladen wurde
            if (version_compare($details['version'], $module['version'], '>')) {
                return $this->updateModule($moduleInfo, $module['module_id']);
            } else {
                $this->cleanupTemp($moduleInfo['tempFile'], $extractDir);
                return ['success' => false, 'message' => 'Das Modul ist bereits installiert.'];
            }
        }
        
        // Dateien aus dem Modul extrahieren und speichern
        foreach ($details['files'] as $path => $content) {
            $fullPath = $this->modulesDir . basename($path);
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
                include $installScript;
            }
        }
        
        // Modul in der Datenbank registrieren
        $moduleId = $this->registerModuleInDatabase($details);
        
        // Temporäre Dateien löschen
        $this->cleanupTemp($moduleInfo['tempFile'], $extractDir);
        
        return [
            'success' => true,
            'message' => 'Modul erfolgreich installiert.',
            'module_id' => $moduleId
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
        
        // Aktuelles Modul aus der Datenbank abrufen
        $stmt = $this->db->prepare("SELECT * FROM dashboard_modules WHERE module_id = ?");
        $stmt->execute([$moduleId]);
        $currentModule = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$currentModule) {
            $this->cleanupTemp($moduleInfo['tempFile'], $extractDir);
            return ['success' => false, 'message' => 'Das zu aktualisierende Modul wurde nicht gefunden.'];
        }
        
        // Überprüfen, ob die neue Version neuer ist
        if (version_compare($details['version'], $currentModule['version'], '<=')) {
            $this->cleanupTemp($moduleInfo['tempFile'], $extractDir);
            return ['success' => false, 'message' => 'Die installierte Version ist bereits aktuell oder neuer.'];
        }
        
        // Aktualisierungsskript ausführen, falls vorhanden
        if (isset($details['update']) && !empty($details['update'])) {
            $updateScript = $extractDir . '/' . $details['update'];
            if (file_exists($updateScript)) {
                // Aktuelle Modulversion an das Skript übergeben
                $currentVersion = $currentModule['version'];
                include $updateScript;
            }
        }
        
        // Dateien aus dem Modul extrahieren und speichern
        foreach ($details['files'] as $path => $content) {
            $fullPath = $this->modulesDir . basename($path);
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
        $stmt = $this->db->prepare("
            UPDATE dashboard_modules 
            SET version = ?, description = ?, updated_at = NOW() 
            WHERE module_id = ?
        ");
        $stmt->execute([$details['version'], $details['description'], $moduleId]);
        
        // Temporäre Dateien löschen
        $this->cleanupTemp($moduleInfo['tempFile'], $extractDir);
        
        return [
            'success' => true,
            'message' => 'Modul erfolgreich aktualisiert.',
            'module_id' => $moduleId
        ];
    }
    
    /**
     * Modul deinstallieren
     * 
     * @param int $moduleId ID des zu deinstallierenden Moduls
     * @return array Ergebnis der Deinstallation
     */
    public function uninstallModule($moduleId) {
        // Modul aus der Datenbank abrufen
        $stmt = $this->db->prepare("SELECT * FROM dashboard_modules WHERE module_id = ?");
        $stmt->execute([$moduleId]);
        $module = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$module) {
            return ['success' => false, 'message' => 'Das zu deinstallierende Modul wurde nicht gefunden.'];
        }
        
        // Überprüfen, ob eine Deinstallationsdatei existiert
        $uninstallFile = $this->modulesDir . $module['name'] . '/uninstall.php';
        if (file_exists($uninstallFile)) {
            include $uninstallFile;
        }
        
        // Modul-Dateien löschen
        $moduleDir = $this->modulesDir . $module['name'];
        if (is_dir($moduleDir)) {
            $this->deleteDirectory($moduleDir);
        }
        
        // Modul aus der Datenbank entfernen
        $stmt = $this->db->prepare("DELETE FROM dashboard_modules WHERE module_id = ?");
        $stmt->execute([$moduleId]);
        
        // Benutzereinstellungen für dieses Modul entfernen
        $stmt = $this->db->prepare("DELETE FROM user_dashboard_settings WHERE module_id = ?");
        $stmt->execute([$moduleId]);
        
        return [
            'success' => true,
            'message' => 'Modul erfolgreich deinstalliert.'
        ];
    }
    
    /**
     * Modul in der Datenbank registrieren
     * 
     * @param array $details Moduldetails
     * @return int ID des registrierten Moduls
     */
    private function registerModuleInDatabase($details) {
        // Hauptdatei des Moduls ermitteln (erste Datei im Array)
        $filePaths = array_keys($details['files']);
        $filePath = basename(reset($filePaths));
        
        // Icon aus den Details extrahieren oder Standardwert verwenden
        $icon = isset($details['icon']) ? $details['icon'] : 'box';
        
        // Modul in der Datenbank registrieren
        $stmt = $this->db->prepare("
            INSERT INTO dashboard_modules 
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
        
        return $this->db->lastInsertId();
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
     * @return array Liste der installierten Module
     */
    public function getAllModules() {
        $stmt = $this->db->query("SELECT * FROM dashboard_modules ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Modul aktivieren oder deaktivieren
     * 
     * @param int $moduleId ID des Moduls
     * @param bool $active Aktivierungsstatus
     * @return array Ergebnis der Aktivierung/Deaktivierung
     */
    public function toggleModuleActive($moduleId, $active) {
        $stmt = $this->db->prepare("UPDATE dashboard_modules SET is_active = ? WHERE module_id = ?");
        $stmt->execute([$active ? 1 : 0, $moduleId]);
        
        return [
            'success' => true,
            'message' => $active ? 'Modul erfolgreich aktiviert.' : 'Modul erfolgreich deaktiviert.'
        ];
    }
}
?>


<?php
class Dashboard {
   private $db;
   
   public function __construct() {
       $this->db = Database::getInstance();
   }
   
   /**
    * Alle verfügbaren Dashboard-Module abrufen
    * 
    * @return array Liste der Module
    */
   public function getAllModules() {
       $sql = "SELECT * FROM dashboard_modules WHERE is_active = 1 ORDER BY name";
       $result = $this->db->select($sql);
    
       // Debug-Ausgabe
       error_log("getAllModules: " . count($result) . " Module gefunden");
    
       return $result;
   }
   
   /**
    * Dashboard-Modul anhand der ID abrufen
    * 
    * @param string $moduleId Modul-ID
    * @return array|null Moduldaten oder null
    */
   public function getModuleById($moduleId) {
       $sql = "SELECT * FROM dashboard_modules WHERE module_id = ?";
       return $this->db->selectOne($sql, [$moduleId]);
   }
   
   /**
    * Dashboard-Einstellungen eines Benutzers abrufen
    * 
    * @param int $userId Benutzer-ID
    * @return array Liste der Moduleinstellungen
    */
   public function getUserDashboardSettings($userId) {
       // Debug-Ausgabe
       error_log("getUserDashboardSettings für User $userId");
       
       $sql = "SELECT uds.*, dm.name, dm.description, dm.icon, dm.file_path 
               FROM user_dashboard_settings uds
               JOIN dashboard_modules dm ON uds.module_id = dm.module_id
               WHERE uds.user_id = ? AND dm.is_active = 1
               ORDER BY uds.position";
       
       $result = $this->db->select($sql, [$userId]);
       
       // Debug-Ausgabe
       error_log("Gefundene Module: " . count($result));
       
       return $result;
   }
   
   /**
    * Dashboard-Einstellungen für ein bestimmtes Modul abrufen
    * 
    * @param int $userId Benutzer-ID
    * @param string $moduleId Modul-ID
    * @return array|null Moduleinstellungen oder null
    */
   public function getUserModuleSettings($userId, $moduleId) {
       $sql = "SELECT * FROM user_dashboard_settings WHERE user_id = ? AND module_id = ?";
       return $this->db->selectOne($sql, [$userId, $moduleId]);
   }
   
   /**
    * Dashboard-Einstellungen für ein Modul speichern
    * 
    * @param int $userId Benutzer-ID
    * @param string $moduleId Modul-ID
    * @param array $settings Moduleinstellungen (position, grid_x, grid_y, grid_width, grid_height, size, is_visible)
    * @return bool Erfolg
    */
   public function saveModuleSettings($userId, $moduleId, $settings) {
       // Prüfen, ob Einstellungen bereits existieren
       $sql = "SELECT * FROM user_dashboard_settings WHERE user_id = ? AND module_id = ?";
       $existing = $this->db->selectOne($sql, [$userId, $moduleId]);
       
       $now = date('Y-m-d H:i:s');
       
       // Standardwerte setzen, falls nicht angegeben
       $position = $settings['position'] ?? 0;
       $gridX = $settings['grid_x'] ?? 0;
       $gridY = $settings['grid_y'] ?? 0;
       $gridWidth = $settings['grid_width'] ?? 3;
       $gridHeight = $settings['grid_height'] ?? 2;
       $size = $settings['size'] ?? 'medium';
       $isVisible = isset($settings['is_visible']) ? ($settings['is_visible'] ? 1 : 0) : 1;
       
       // Debug-Ausgabe
       error_log("Modul $moduleId - Werte: pos=$position, x=$gridX, y=$gridY, w=$gridWidth, h=$gridHeight, size=$size, visible=$isVisible");
       
       try {
           if ($existing) {
               // Einstellungen aktualisieren
               $result = $this->db->update('user_dashboard_settings', 
                   [
                       'position' => $position,
                       'grid_x' => $gridX,
                       'grid_y' => $gridY,
                       'grid_width' => $gridWidth,
                       'grid_height' => $gridHeight,
                       'size' => $size,
                       'is_visible' => $isVisible,
                       'updated_at' => $now
                   ],
                   'user_id = ? AND module_id = ?',
                   [$userId, $moduleId]
               );
               error_log("Update für Modul $moduleId: " . ($result ? "erfolgreich" : "fehlgeschlagen"));
               return $result;
           } else {
               // Neue Einstellungen erstellen
               $result = $this->db->insert('user_dashboard_settings', [
                   'user_id' => $userId,
                   'module_id' => $moduleId,
                   'position' => $position,
                   'grid_x' => $gridX,
                   'grid_y' => $gridY,
                   'grid_width' => $gridWidth,
                   'grid_height' => $gridHeight,
                   'size' => $size,
                   'is_visible' => $isVisible,
                   'created_at' => $now,
                   'updated_at' => $now
               ]);
               error_log("Insert für Modul $moduleId: " . ($result ? "erfolgreich" : "fehlgeschlagen"));
               return $result;
           }
       } catch (Exception $e) {
           error_log("Fehler beim Speichern von Modul $moduleId: " . $e->getMessage());
           return false;
       }
   }
   
   /**
    * Mehrere Dashboard-Einstellungen auf einmal speichern
    * 
    * @param int $userId Benutzer-ID
    * @param array $settings Liste der Moduleinstellungen
    * @return bool Erfolg
    */
   public function saveUserDashboardSettings($userId, $settings) {
       $success = true;
       
       // Debug-Ausgabe
       error_log("saveUserDashboardSettings für User $userId mit " . count($settings) . " Modulen");
       
       foreach ($settings as $moduleId => $moduleSettings) {
           // Debug-Ausgabe
           error_log("Speichere Modul $moduleId: " . json_encode($moduleSettings));
           
           $result = $this->saveModuleSettings($userId, $moduleId, $moduleSettings);
           
           if (!$result) {
               error_log("Fehler beim Speichern von Modul $moduleId");
               $success = false;
           }
       }
       
       return $success;
   }
   
   /**
    * Dashboard-Module für einen Benutzer initialisieren
    * 
    * @param int $userId Benutzer-ID
    * @return bool Erfolg
    */
   public function initializeUserDashboard($userId) {
       // Debug-Ausgabe
       error_log("initializeUserDashboard für User $userId");
       
       // Alle verfügbaren Module abrufen
       $modules = $this->getAllModules();
       
       // Debug-Ausgabe
       error_log("Verfügbare Module: " . count($modules));
       
       // Prüfen, ob der Benutzer bereits Einstellungen hat
       $sql = "SELECT COUNT(*) as count FROM user_dashboard_settings WHERE user_id = ?";
       $result = $this->db->selectOne($sql, [$userId]);
       
       if ($result && $result['count'] > 0) {
           // Benutzer hat bereits Einstellungen
           error_log("Benutzer hat bereits " . $result['count'] . " Moduleinstellungen");
           return true;
       }
       
       // Standardeinstellungen für jeden Modul erstellen
       $position = 0;
       $gridY = 0;
       $success = true;
       
       foreach ($modules as $module) {
           // Einfaches Grid-Layout erstellen (2 Module pro Zeile)
           $gridX = ($position % 2) * 6;
           if ($position % 2 == 0 && $position > 0) {
               $gridY += 2; // Neue Zeile beginnen
           }
           
           error_log("Initialisiere Modul " . $module['module_id'] . " für User $userId");
           
           $result = $this->saveModuleSettings(
               $userId,
               $module['module_id'],
               [
                   'position' => $position,
                   'grid_x' => $gridX,
                   'grid_y' => $gridY,
                   'grid_width' => 6,
                   'grid_height' => 2,
                   'size' => 'medium',
                   'is_visible' => 1
               ]
           );
           
           if (!$result) {
               error_log("Fehler beim Initialisieren von Modul " . $module['module_id']);
               $success = false;
           }
           
           $position++;
       }
       
       return $success;
   }
   
   /**
    * Dashboard-Modul registrieren
    * 
    * @param string $moduleId Modul-ID
    * @param string $name Modulname
    * @param string $description Beschreibung
    * @param string $icon Icon
    * @param string $filePath Pfad zur PHP-Datei, die das Modul rendert
    * @return bool Erfolg
    */
   public function registerModule($moduleId, $name, $description = '', $icon = '', $filePath = '') {
       // Prüfen, ob Modul bereits existiert
       $sql = "SELECT * FROM dashboard_modules WHERE module_id = ?";
       $existing = $this->db->selectOne($sql, [$moduleId]);
       
       $now = date('Y-m-d H:i:s');
       
       if ($existing) {
           // Modul aktualisieren
           return $this->db->update('dashboard_modules', 
               [
                   'name' => $name,
                   'description' => $description,
                   'icon' => $icon,
                   'file_path' => $filePath,
                   'updated_at' => $now
               ],
               'module_id = ?',
               [$moduleId]
           );
       } else {
           // Neues Modul erstellen
           return $this->db->insert('dashboard_modules', [
               'module_id' => $moduleId,
               'name' => $name,
               'description' => $description,
               'icon' => $icon,
               'file_path' => $filePath,
               'is_active' => 1,
               'created_at' => $now,
               'updated_at' => $now
           ]);
       }
   }
   
   /**
    * Dashboard-Layout zurücksetzen
    * 
    * @param int $userId Benutzer-ID
    * @return bool Erfolg
    */
   public function resetDashboardLayout($userId) {
       // Bestehende Einstellungen löschen
       $this->db->query("DELETE FROM user_dashboard_settings WHERE user_id = ?", [$userId]);
       
       // Neue Standardeinstellungen erstellen
       return $this->initializeUserDashboard($userId);
   }
}
?>


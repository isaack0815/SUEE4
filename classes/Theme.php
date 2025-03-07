<?php
/**
 * Theme-Klasse zur Verwaltung von Themes
 */
class Theme {
    private $db;
    
    /**
     * Konstruktor
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Aktuelles Theme abrufen
     * 
     * @return string Name des aktuellen Themes
     */
    public function getCurrentTheme() {
        // Standardmäßig das Standard-Theme zurückgeben
        return 'default';
    }
    
    /**
     * Theme für einen Benutzer abrufen
     * 
     * @param int $userId Benutzer-ID
     * @return string Name des Themes für den Benutzer
     */
    public function getUserTheme($userId) {
        $stmt = $this->db->prepare("SELECT theme FROM user_settings WHERE user_id = ?");
        $stmt->execute([$userId]);
        
        if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['theme'];
        }
        
        // Wenn kein Theme für den Benutzer gefunden wurde, das Standard-Theme zurückgeben
        return $this->getCurrentTheme();
    }
    
    /**
     * Theme für einen Benutzer setzen
     * 
     * @param int $userId Benutzer-ID
     * @param string $theme Name des Themes
     * @return bool Erfolg der Operation
     */
    public function setUserTheme($userId, $theme) {
        // Überprüfen, ob bereits Einstellungen für den Benutzer existieren
        $stmt = $this->db->prepare("SELECT * FROM user_settings WHERE user_id = ?");
        $stmt->execute([$userId]);
        
        if ($stmt->rowCount() > 0) {
            // Einstellungen aktualisieren
            $stmt = $this->db->prepare("UPDATE user_settings SET theme = ? WHERE user_id = ?");
            return $stmt->execute([$theme, $userId]);
        } else {
            // Neue Einstellungen erstellen
            $stmt = $this->db->prepare("INSERT INTO user_settings (user_id, theme) VALUES (?, ?)");
            return $stmt->execute([$userId, $theme]);
        }
    }
    
    /**
     * Alle verfügbaren Themes abrufen
     * 
     * @return array Liste der verfügbaren Themes
     */
    public function getAllThemes() {
        // Hier könnten Sie die Themes aus einem Verzeichnis oder einer Datenbank abrufen
        return [
            'default' => 'Standard',
            'dark' => 'Dunkel',
            'light' => 'Hell',
            'blue' => 'Blau',
            'green' => 'Grün'
        ];
    }
    
    /**
     * CSS-Datei für ein Theme abrufen
     * 
     * @param string $theme Name des Themes
     * @return string Pfad zur CSS-Datei
     */
    public function getThemeCssFile($theme) {
        // Pfad zur CSS-Datei für das angegebene Theme zurückgeben
        return "css/themes/{$theme}.css";
    }
}
?>


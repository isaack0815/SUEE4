<?php
class UserPreferences {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Benutzereinstellung speichern
     * 
     * @param int $userId Benutzer-ID
     * @param string $key Einstellungsschlüssel
     * @param mixed $value Einstellungswert
     * @return bool Erfolg
     */
    public function savePreference($userId, $key, $value) {
        // Prüfen, ob Einstellung bereits existiert
        $sql = "SELECT * FROM user_preferences WHERE user_id = ? AND preference_key = ?";
        $existing = $this->db->selectOne($sql, [$userId, $key]);
        
        $now = date('Y-m-d H:i:s');
        
        if ($existing) {
            // Einstellung aktualisieren
            return $this->db->update('user_preferences', 
                [
                    'preference_value' => $value,
                    'updated_at' => $now
                ],
                'user_id = ? AND preference_key = ?',
                [$userId, $key]
            );
        } else {
            // Neue Einstellung erstellen
            return $this->db->insert('user_preferences', [
                'user_id' => $userId,
                'preference_key' => $key,
                'preference_value' => $value,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }
    }
    
    /**
     * Benutzereinstellung abrufen
     * 
     * @param int $userId Benutzer-ID
     * @param string $key Einstellungsschlüssel
     * @param mixed $default Standardwert, falls keine Einstellung gefunden wurde
     * @return mixed Einstellungswert
     */
    public function getPreference($userId, $key, $default = null) {
        $sql = "SELECT preference_value FROM user_preferences WHERE user_id = ? AND preference_key = ?";
        $result = $this->db->selectOne($sql, [$userId, $key]);
        
        return $result ? $result['preference_value'] : $default;
    }
    
    /**
     * Alle Einstellungen eines Benutzers abrufen
     * 
     * @param int $userId Benutzer-ID
     * @return array Einstellungen als assoziatives Array
     */
    public function getAllPreferences($userId) {
        $sql = "SELECT preference_key, preference_value FROM user_preferences WHERE user_id = ?";
        $results = $this->db->select($sql, [$userId]);
        
        $preferences = [];
        foreach ($results as $row) {
            $preferences[$row['preference_key']] = $row['preference_value'];
        }
        
        return $preferences;
    }
    
    /**
     * Mehrere Einstellungen auf einmal speichern
     * 
     * @param int $userId Benutzer-ID
     * @param array $preferences Einstellungen als assoziatives Array
     * @return bool Erfolg
     */
    public function savePreferences($userId, $preferences) {
        $success = true;
        
        foreach ($preferences as $key => $value) {
            $result = $this->savePreference($userId, $key, $value);
            if (!$result) {
                $success = false;
            }
        }
        
        return $success;
    }
}
?>


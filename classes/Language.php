<?php
class Language {
    private static $instance = null;
    private $db;
    private $currentLanguage;
    private $translations = [];
    
    private function __construct() {
        $this->db = Database::getInstance();
        $this->setLanguage();
        $this->loadTranslations();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function setLanguage() {
        // Sprache aus Session, Cookie oder Browser-Einstellungen ermitteln
        if (isset($_GET['lang']) && $this->isValidLanguage($_GET['lang'])) {
            $this->currentLanguage = $_GET['lang'];
            $_SESSION['lang'] = $this->currentLanguage;
        } elseif (isset($_SESSION['lang']) && $this->isValidLanguage($_SESSION['lang'])) {
            $this->currentLanguage = $_SESSION['lang'];
        } elseif (isset($_COOKIE['lang']) && $this->isValidLanguage($_COOKIE['lang'])) {
            $this->currentLanguage = $_COOKIE['lang'];
            $_SESSION['lang'] = $this->currentLanguage;
        } else {
            // Browsersprache ermitteln
            $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en', 0, 2);
            if ($this->isValidLanguage($browserLang)) {
                $this->currentLanguage = $browserLang;
            } else {
                // Standardsprache verwenden
                $this->currentLanguage = 'de'; // Standardsprache
            }
            $_SESSION['lang'] = $this->currentLanguage;
        }
        
        // Cookie setzen (30 Tage gültig)
        setcookie('lang', $this->currentLanguage, time() + 60 * 60 * 24 * 30, '/');
    }
    
    private function isValidLanguage($lang) {
        $availableLanguages = $this->getAvailableLanguages();
        return in_array($lang, $availableLanguages);
    }
    
    public function getAvailableLanguages() {
        $sql = "SELECT DISTINCT lang_code FROM language_strings ORDER BY lang_code";
        $result = $this->db->select($sql);
        
        $languages = [];
        foreach ($result as $row) {
            $languages[] = $row['lang_code'];
        }
        
        return $languages;
    }
    
    private function loadTranslations() {
        $sql = "SELECT lang_key, lang_value FROM language_strings WHERE lang_code = ?";
        $result = $this->db->select($sql, [$this->currentLanguage]);
        
        foreach ($result as $row) {
            $this->translations[$row['lang_key']] = $row['lang_value'];
        }
    }
    
    public function translate($key, $params = []) {
        if (isset($this->translations[$key])) {
            $translation = $this->translations[$key];
            
            // Parameter ersetzen
            foreach ($params as $param => $value) {
                $translation = str_replace('{' . $param . '}', $value, $translation);
            }
            
            return $translation;
        }
        
        // Fallback: Schlüssel zurückgeben, wenn keine Übersetzung gefunden wurde
        return $key;
    }
    
    public function getCurrentLanguage() {
        return $this->currentLanguage;
    }
    
    /**
     * Sprachschlüssel abrufen
     * 
     * @param string $filter Sprachfilter ('all' oder Sprachcode)
     * @param string $search Suchbegriff
     * @param int $limit Anzahl der Ergebnisse
     * @param int $offset Offset für Paginierung
     * @return array Liste der Sprachschlüssel
     */
    public function getLanguageKeys($filter = 'all', $search = '', $limit = 20, $offset = 0) {
        // Alle eindeutigen Sprachschlüssel abrufen
        $params = [];
        $whereClause = "";
        
        if ($search) {
            $whereClause = "WHERE lang_key LIKE ? OR lang_value LIKE ?";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        // LIMIT und OFFSET direkt in die SQL-Abfrage einfügen
        // Sicherstellen, dass es sich um Ganzzahlen handelt
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        $sql = "SELECT DISTINCT lang_key FROM language_strings $whereClause ORDER BY lang_key LIMIT $limit OFFSET $offset";
        
        $result = $this->db->select($sql, $params);
        
        $keys = [];
        foreach ($result as $row) {
            $langKey = $row['lang_key'];
            
            // Werte für alle Sprachen oder eine bestimmte Sprache abrufen
            $values = [];
            
            if ($filter === 'all') {
                $languages = $this->getAvailableLanguages();
                foreach ($languages as $langCode) {
                    $values[$langCode] = $this->getTranslation($langKey, $langCode);
                }
            } else {
                $values[$filter] = $this->getTranslation($langKey, $filter);
            }
            
            $keys[] = [
                'lang_key' => $langKey,
                'values' => $values
            ];
        }
        
        return $keys;
    }
    
    /**
     * Anzahl der Sprachschlüssel zählen
     * 
     * @param string $filter Sprachfilter ('all' oder Sprachcode)
     * @param string $search Suchbegriff
     * @return int Anzahl der Sprachschlüssel
     */
    public function countLanguageKeys($filter = 'all', $search = '') {
        $params = [];
        $whereClause = "";
        
        if ($search) {
            $whereClause = "WHERE lang_key LIKE ? OR lang_value LIKE ?";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $sql = "SELECT COUNT(DISTINCT lang_key) as count FROM language_strings $whereClause";
        $result = $this->db->selectOne($sql, $params);
        
        return $result['count'];
    }
    
    /**
     * Einzelnen Sprachschlüssel abrufen
     * 
     * @param string $key Sprachschlüssel
     * @return array|null Sprachschlüsseldaten oder null
     */
    public function getLanguageKey($key) {
        $sql = "SELECT * FROM language_strings WHERE lang_key = ?";
        $result = $this->db->select($sql, [$key]);
        
        if (empty($result)) {
            return null;
        }
        
        $values = [];
        foreach ($result as $row) {
            $values[$row['lang_code']] = $row['lang_value'];
        }
        
        return [
            'lang_key' => $key,
            'values' => $values
        ];
    }
    
    /**
     * Übersetzung für einen Sprachschlüssel und eine Sprache abrufen
     * 
     * @param string $key Sprachschlüssel
     * @param string $langCode Sprachcode
     * @return string|null Übersetzung oder null
     */
    private function getTranslation($key, $langCode) {
        $sql = "SELECT lang_value FROM language_strings WHERE lang_key = ? AND lang_code = ?";
        $result = $this->db->selectOne($sql, [$key, $langCode]);
        
        return $result ? $result['lang_value'] : null;
    }
    
    /**
     * Neue Sprache hinzufügen
     * 
     * @param string $langCode Sprachcode
     * @param string $langName Sprachname
     * @return array Ergebnis der Operation
     */
    public function addLanguage($langCode, $langName) {
        // Prüfen, ob Sprache bereits existiert
        $languages = $this->getAvailableLanguages();
        if (in_array($langCode, $languages)) {
            return ['success' => false, 'message' => 'language_exists'];
        }
        
        // Sprachname als Übersetzung hinzufügen
        $this->db->insert('language_strings', [
            'lang_code' => $langCode,
            'lang_key' => 'lang_' . $langCode,
            'lang_value' => $langName
        ]);
        
        // Standardübersetzungen für die neue Sprache hinzufügen
        $defaultKeys = $this->getDefaultLanguageKeys();
        foreach ($defaultKeys as $key) {
            // Prüfen, ob bereits eine Übersetzung existiert
            $sql = "SELECT * FROM language_strings WHERE lang_key = ? AND lang_code = ?";
            $existing = $this->db->selectOne($sql, [$key, $langCode]);
            
            if (!$existing) {
                // Standardwert aus der Standardsprache verwenden
                $defaultValue = $this->getTranslation($key, 'de') ?: $key;
                
                $this->db->insert('language_strings', [
                    'lang_code' => $langCode,
                    'lang_key' => $key,
                    'lang_value' => $defaultValue
                ]);
            }
        }
        
        return ['success' => true];
    }
    
    /**
     * Standardsprachschlüssel abrufen
     * 
     * @return array Liste der Standardsprachschlüssel
     */
    private function getDefaultLanguageKeys() {
        $sql = "SELECT DISTINCT lang_key FROM language_strings WHERE lang_code = 'de'";
        $result = $this->db->select($sql);
        
        $keys = [];
        foreach ($result as $row) {
            $keys[] = $row['lang_key'];
        }
        
        return $keys;
    }
    
    /**
     * Neuen Sprachschlüssel hinzufügen
     * 
     * @param string $key Sprachschlüssel
     * @param array $values Übersetzungen für verschiedene Sprachen
     * @return array Ergebnis der Operation
     */
    public function addLanguageKey($key, $values) {
        // Prüfen, ob Schlüssel bereits existiert
        $existing = $this->getLanguageKey($key);
        if ($existing) {
            return ['success' => false, 'message' => 'key_exists'];
        }
        
        // Übersetzungen für jede Sprache hinzufügen
        foreach ($values as $langCode => $value) {
            $this->db->insert('language_strings', [
                'lang_code' => $langCode,
                'lang_key' => $key,
                'lang_value' => $value
            ]);
        }
        
        return ['success' => true];
    }
    
    /**
     * Sprachschlüssel aktualisieren
     * 
     * @param string $key Sprachschlüssel
     * @param array $values Übersetzungen für verschiedene Sprachen
     * @return array Ergebnis der Operation
     */
    public function updateLanguageKey($key, $values) {
        // Prüfen, ob Schlüssel existiert
        $existing = $this->getLanguageKey($key);
        if (!$existing) {
            return ['success' => false, 'message' => 'key_not_found'];
        }
        
        // Übersetzungen für jede Sprache aktualisieren
        foreach ($values as $langCode => $value) {
            // Prüfen, ob bereits eine Übersetzung existiert
            $sql = "SELECT * FROM language_strings WHERE lang_key = ? AND lang_code = ?";
            $existingTranslation = $this->db->selectOne($sql, [$key, $langCode]);
            
            if ($existingTranslation) {
                // Übersetzung aktualisieren
                $this->db->update('language_strings', 
                    ['lang_value' => $value],
                    'lang_key = ? AND lang_code = ?',
                    [$key, $langCode]
                );
            } else {
                // Neue Übersetzung hinzufügen
                $this->db->insert('language_strings', [
                    'lang_code' => $langCode,
                    'lang_key' => $key,
                    'lang_value' => $value
                ]);
            }
        }
        
        return ['success' => true];
    }
    
    /**
     * Sprachschlüssel löschen
     * 
     * @param string $key Sprachschlüssel
     * @return array Ergebnis der Operation
     */
    public function deleteLanguageKey($key) {
        // Prüfen, ob Schlüssel existiert
        $existing = $this->getLanguageKey($key);
        if (!$existing) {
            return ['success' => false, 'message' => 'key_not_found'];
        }
        
        // Alle Übersetzungen für diesen Schlüssel löschen
        $this->db->query("DELETE FROM language_strings WHERE lang_key = ?", [$key]);
        
        return ['success' => true];
    }
}
?>


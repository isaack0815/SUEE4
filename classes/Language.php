<?php
class Language {
    private $db;
    private $currentLang;
    private $translations = [];
    private static $instance = null;
    
    private function __construct() {
        $this->db = Database::getInstance();
        
        // Aktuelle Sprache bestimmen
        $this->setLanguage();
        
        // Übersetzungen laden
        $this->loadTranslations();
    }
    
    // Singleton-Pattern
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Language();
        }
        return self::$instance;
    }
    
    private function setLanguage() {
        $availableLanguages = unserialize(AVAILABLE_LANGUAGES);
        
        // Sprache aus Session verwenden, falls vorhanden
        if (isset($_SESSION['language']) && in_array($_SESSION['language'], $availableLanguages)) {
            $this->currentLang = $_SESSION['language'];
        } 
        // Sprache aus GET-Parameter verwenden, falls vorhanden
        else if (isset($_GET['lang']) && in_array($_GET['lang'], $availableLanguages)) {
            $this->currentLang = $_GET['lang'];
            $_SESSION['language'] = $this->currentLang;
        } 
        // Browser-Sprache verwenden, falls verfügbar
        else if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            if (in_array($browserLang, $availableLanguages)) {
                $this->currentLang = $browserLang;
                $_SESSION['language'] = $this->currentLang;
            } else {
                $this->currentLang = DEFAULT_LANGUAGE;
                $_SESSION['language'] = $this->currentLang;
            }
        } 
        // Standardsprache verwenden
        else {
            $this->currentLang = DEFAULT_LANGUAGE;
            $_SESSION['language'] = $this->currentLang;
        }
    }
    
    private function loadTranslations() {
        $sql = "SELECT lang_key, lang_value FROM language_strings WHERE lang_code = ?";
        $result = $this->db->select($sql, [$this->currentLang]);
        
        foreach ($result as $row) {
            $this->translations[$row['lang_key']] = $row['lang_value'];
        }
    }
    
    public function getCurrentLanguage() {
        return $this->currentLang;
    }
    
    public function getAvailableLanguages() {
        return unserialize(AVAILABLE_LANGUAGES);
    }
    
    public function translate($key, $placeholders = []) {
        if (isset($this->translations[$key])) {
            $translation = $this->translations[$key];
            
            // Platzhalter ersetzen
            foreach ($placeholders as $placeholder => $value) {
                $translation = str_replace('{' . $placeholder . '}', $value, $translation);
            }
            
            return $translation;
        }
        
        // Fallback: Schlüssel zurückgeben, wenn keine Übersetzung gefunden wurde
        return $key;
    }
    
    public function getAllTranslations() {
        return $this->translations;
    }
}

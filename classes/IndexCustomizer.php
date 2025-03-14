<?php
class IndexCustomizer {
    private $db;
    private $language;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->language = Language::getInstance();
    }
    
    /**
     * Holt alle Sektionen für die Startseite mit Inhalten für eine bestimmte Sprache
     * 
     * @param string $languageCode Sprachcode
     * @return array Liste der Sektionen
     */
    public function getAllSections($languageCode = null) {
        $sections = $this->getSections();
        
        // Wenn kein Sprachcode angegeben wurde, gib alle Sektionen zurück
        if ($languageCode === null) {
            return $sections;
        }
        
        // Filtere die Inhalte für die angegebene Sprache
        foreach ($sections as &$section) {
            if (isset($section['contents'][$languageCode])) {
                $section['content'] = $section['contents'][$languageCode];
            } else {
                $section['content'] = [
                    'title' => '',
                    'content' => ''
                ];
            }
            
            // Für Kartensektionen auch die Karteninhalte filtern
            if ($section['type'] == 'cards' && isset($section['cards'])) {
                foreach ($section['cards'] as &$card) {
                    if (isset($card['contents'][$languageCode])) {
                        $card['content'] = $card['contents'][$languageCode];
                    } else {
                        $card['content'] = [
                            'title' => '',
                            'content' => '',
                            'button_text' => '',
                            'button_url' => ''
                        ];
                    }
                }
            }
        }
        
        return $sections;
    }
    
    /**
     * Holt alle Sektionen für die Startseite
     * 
     * @return array Liste der Sektionen
     */
    public function getSections() {
        $sql = "SELECT * FROM index_sections ORDER BY position ASC";
        $sections = $this->db->select($sql);
        
        if (!is_array($sections)) {
            return [];
        }
        
        $result = [];
        foreach ($sections as $section) {
            // Inhalte für alle Sprachen abrufen
            $sql = "SELECT * FROM index_section_translations WHERE section_id = ?";
            $translations = $this->db->select($sql, [$section['id']]);
            
            $contents = [];
            foreach ($translations as $translation) {
                $contents[$translation['lang_code']] = $translation;
            }
            
            $section['contents'] = $contents;
            
            // Karten für Kartensektionen abrufen
            if ($section['type'] == 'cards') {
                $section['cards'] = $this->getCards($section['id']);
            }
            
            $result[] = $section;
        }
        
        return $result;
    }
    
    /**
     * Holt eine einzelne Sektion anhand ihrer ID
     * 
     * @param int $sectionId ID der Sektion
     * @return array|null Sektionsdaten oder null, wenn nicht gefunden
     */
    public function getSection($sectionId) {
        $db = Database::getInstance();
        
        // Grundlegende Sektionsdaten abrufen
        $sql = "SELECT id, type, position, active, background_color, text_color, position_vertical 
                FROM index_sections 
                WHERE id = ?";
        $section = $db->selectOne($sql, [$sectionId]);
        
        if (!$section) {
            return null;
        }
        
        // Inhalte für alle Sprachen abrufen
        $sql = "SELECT lang_code, title, subtitle, content 
                FROM index_section_translations 
                WHERE section_id = ?";
        $translations = $db->select($sql, [$sectionId]);
        
        // Inhalte nach Sprache organisieren
        $section['contents'] = [];
        if ($translations) {
            foreach ($translations as $translation) {
                $langCode = $translation['lang_code'];
                $section['contents'][$langCode] = [
                    'title' => $translation['title'],
                    'subtitle' => $translation['subtitle'],
                    'content' => $translation['content']
                ];
            }
        }
        
        return $section;
    }
    
    /**
     * Holt alle Karten für eine Sektion
     * 
     * @param int $sectionId ID der Sektion
     * @return array Liste der Karten
     */
    public function getCards($sectionId) {
        $sql = "SELECT * FROM index_cards WHERE section_id = ? ORDER BY position ASC";
        $cards = $this->db->select($sql, [$sectionId]);
        
        if (!is_array($cards)) {
            return [];
        }
        
        $result = [];
        foreach ($cards as $card) {
            // Inhalte für alle Sprachen abrufen
            $sql = "SELECT * FROM index_card_translations WHERE card_id = ?";
            $translations = $this->db->select($sql, [$card['id']]);
            
            $contents = [];
            foreach ($translations as $translation) {
                $contents[$translation['lang_code']] = $translation;
            }
            
            $card['contents'] = $contents;
            $result[] = $card;
        }
        
        return $result;
    }
    
    /**
     * Holt eine einzelne Karte anhand ihrer ID
     * 
     * @param int $cardId ID der Karte
     * @return array|null Kartendaten oder null, wenn nicht gefunden
     */
    public function getCard($cardId) {
        $sql = "SELECT * FROM index_cards WHERE id = ?";
        $card = $this->db->selectOne($sql, [$cardId]);
        
        if (!$card) {
            return null;
        }
        
        // Inhalte für alle Sprachen abrufen
        $sql = "SELECT * FROM index_card_translations WHERE card_id = ?";
        $translations = $this->db->select($sql, [$cardId]);
        
        $contents = [];
        foreach ($translations as $translation) {
            $contents[$translation['lang_code']] = $translation;
        }
        
        $card['contents'] = $contents;
        
        return $card;
    }
    
    /**
     * Erstellt eine neue Sektion
     * 
     * @param array $sectionData Sektionsdaten
     * @param array $contents Inhalte für verschiedene Sprachen
     * @return int|bool ID der neuen Sektion oder false bei Fehler
     */
    public function createSection($sectionData, $contents) {
        // Höchste Position ermitteln
        $sql = "SELECT MAX(position) as max_pos FROM index_sections";
        $result = $this->db->selectOne($sql);
        $position = $result && isset($result['max_pos']) ? ($result['max_pos'] + 1) : 1;
        
        // Sektion erstellen
        $sectionId = $this->db->insert('index_sections', [
            'type' => $sectionData['type'],
            'active' => $sectionData['active'] ?? 1,
            'position' => $position,
            'background_color' => $sectionData['background_color'] ?? null,
            'text_color' => $sectionData['text_color'] ?? null
        ]);
        
        if (!$sectionId) {
            return false;
        }
        
        // Inhalte für verschiedene Sprachen erstellen
        foreach ($contents as $langCode => $content) {
            $this->db->insert('index_section_translations', [
                'section_id' => $sectionId,
                'lang_code' => $langCode,
                'title' => $content['title'] ?? '',
                'content' => $content['content'] ?? ''
            ]);
        }
        
        return $sectionId;
    }
    
    /**
     * Aktualisiert eine Sektion
     * 
     * @param int $sectionId Sektions-ID
     * @param array $sectionData Sektionsdaten
     * @param array $contents Inhalte für verschiedene Sprachen
     * @return bool Erfolg
     */
    public function updateSection($sectionId, $sectionData, $contents) {
        // Sektion aktualisieren
        $updateData = [];
        
        if (isset($sectionData['type'])) {
            $updateData['type'] = $sectionData['type'];
        }
        
        if (isset($sectionData['active'])) {
            $updateData['active'] = $sectionData['active'];
        }
        
        if (isset($sectionData['background_color'])) {
            $updateData['background_color'] = $sectionData['background_color'];
        }
        
        if (isset($sectionData['text_color'])) {
            $updateData['text_color'] = $sectionData['text_color'];
        }
        
        if (!empty($updateData)) {
            $this->db->update('index_sections', $updateData, 'id = ?', [$sectionId]);
        }
        
        // Inhalte für verschiedene Sprachen aktualisieren
        foreach ($contents as $langCode => $content) {
            // Prüfen, ob bereits ein Inhalt existiert
            $sql = "SELECT * FROM index_section_translations WHERE section_id = ? AND lang_code = ?";
            $existingContent = $this->db->selectOne($sql, [$sectionId, $langCode]);
            
            if ($existingContent) {
                // Inhalt aktualisieren
                $this->db->update('index_section_translations', [
                    'title' => $content['title'] ?? '',
                    'content' => $content['content'] ?? ''
                ], 'section_id = ? AND lang_code = ?', [$sectionId, $langCode]);
            } else {
                // Neuen Inhalt erstellen
                $this->db->insert('index_section_translations', [
                    'section_id' => $sectionId,
                    'lang_code' => $langCode,
                    'title' => $content['title'] ?? '',
                    'content' => $content['content'] ?? ''
                ]);
            }
        }
        
        return true;
    }
    
    /**
     * Löscht eine Sektion
     * 
     * @param int $sectionId Sektions-ID
     * @return bool Erfolg
     */
    public function deleteSection($sectionId) {
        $db = Database::getInstance();
        
        try {
            // Beginne eine Transaktion
            $db->getConnection()->beginTransaction();
            
            // Lösche zuerst die Übersetzungen
            $db->delete('index_section_translations', ['section_id' => $sectionId]);
            
            // Lösche dann die Sektion selbst
            $db->delete('index_sections', ['id' => $sectionId]);
            
            // Commit der Transaktion
            $db->getConnection()->commit();
            
            return true;
        } catch (Exception $e) {
            // Rollback bei Fehler
            $db->getConnection()->rollBack();
            $logger = Logger::getInstance();
            $logger->error("Fehler beim Löschen der Sektion: " . $e->getMessage(), "index_customizer");
            return false;
        }
    }
    
    /**
     * Aktualisiert die Position einer Sektion
     * 
     * @param int $sectionId Sektions-ID
     * @param int $position Neue Position
     * @return bool Erfolg
     */
    public function updateSectionPosition($sectionId, $position) {
        return $this->db->update('index_sections', [
            'position' => $position
        ], 'id = ?', [$sectionId]);
    }
    
    /**
     * Erstellt eine neue Karte
     * 
     * @param array $cardData Kartendaten
     * @param array $contents Inhalte für verschiedene Sprachen
     * @return int|bool ID der neuen Karte oder false bei Fehler
     */
    public function createCard($cardData, $contents) {
        // Höchste Position ermitteln
        $sql = "SELECT MAX(position) as max_pos FROM index_cards WHERE section_id = ?";
        $result = $this->db->selectOne($sql, [$cardData['section_id']]);
        $position = $result && isset($result['max_pos']) ? ($result['max_pos'] + 1) : 1;
        
        // Karte erstellen
        $cardId = $this->db->insert('index_cards', [
            'section_id' => $cardData['section_id'],
            'position' => $position,
            'icon' => $cardData['icon'] ?? null,
            'icon_color' => $cardData['icon_color'] ?? null,
            'background_color' => $cardData['background_color'] ?? null,
            'text_color' => $cardData['text_color'] ?? null
        ]);
        
        if (!$cardId) {
            return false;
        }
        
        // Inhalte für verschiedene Sprachen erstellen
        foreach ($contents as $langCode => $content) {
            $this->db->insert('index_card_translations', [
                'card_id' => $cardId,
                'lang_code' => $langCode,
                'title' => $content['title'] ?? '',
                'content' => $content['content'] ?? '',
                'button_text' => $content['button_text'] ?? null,
                'button_url' => $content['button_url'] ?? null
            ]);
        }
        
        return $cardId;
    }
    
    /**
     * Aktualisiert eine Karte
     * 
     * @param int $cardId Karten-ID
     * @param array $cardData Kartendaten
     * @param array $contents Inhalte für verschiedene Sprachen
     * @return bool Erfolg
     */
    public function updateCard($cardId, $cardData, $contents) {
        // Karte aktualisieren
        $updateData = [];
        
        if (isset($cardData['icon'])) {
            $updateData['icon'] = $cardData['icon'];
        }
        
        if (isset($cardData['icon_color'])) {
            $updateData['icon_color'] = $cardData['icon_color'];
        }
        
        if (isset($cardData['background_color'])) {
            $updateData['background_color'] = $cardData['background_color'];
        }
        
        if (isset($cardData['text_color'])) {
            $updateData['text_color'] = $cardData['text_color'];
        }
        
        if (!empty($updateData)) {
            $this->db->update('index_cards', $updateData, 'id = ?', [$cardId]);
        }
        
        // Inhalte für verschiedene Sprachen aktualisieren
        foreach ($contents as $langCode => $content) {
            // Prüfen, ob bereits ein Inhalt existiert
            $sql = "SELECT * FROM index_card_translations WHERE card_id = ? AND lang_code = ?";
            $existingContent = $this->db->selectOne($sql, [$cardId, $langCode]);
            
            if ($existingContent) {
                // Inhalt aktualisieren
                $this->db->update('index_card_translations', [
                    'title' => $content['title'] ?? '',
                    'content' => $content['content'] ?? '',
                    'button_text' => $content['button_text'] ?? null,
                    'button_url' => $content['button_url'] ?? null
                ], 'card_id = ? AND lang_code = ?', [$cardId, $langCode]);
            } else {
                // Neuen Inhalt erstellen
                $this->db->insert('index_card_translations', [
                    'card_id' => $cardId,
                    'lang_code' => $langCode,
                    'title' => $content['title'] ?? '',
                    'content' => $content['content'] ?? '',
                    'button_text' => $content['button_text'] ?? null,
                    'button_url' => $content['button_url'] ?? null
                ]);
            }
        }
        
        return true;
    }
    
    /**
     * Löscht eine Karte
     * 
     * @param int $cardId Karten-ID
     * @return bool Erfolg
     */
    public function deleteCard($cardId) {
        // Karteninhalte löschen
        $this->db->delete('index_card_translations', 'card_id = ?', [$cardId]);
        
        // Karte löschen
        return $this->db->delete('index_cards', 'id = ?', [$cardId]);
    }
    
    /**
     * Aktualisiert die Position einer Karte
     * 
     * @param int $cardId Karten-ID
     * @param int $position Neue Position
     * @return bool Erfolg
     */
    public function updateCardPosition($cardId, $position) {
        return $this->db->update('index_cards', [
            'position' => $position
        ], 'id = ?', [$cardId]);
    }

    /**
     * Speichert den Inhalt einer Sektion für eine bestimmte Sprache
     * 
     * @param int $sectionId Sektions-ID
     * @param string $languageCode Sprachcode
     * @param string $title Titel
     * @param string $content Inhalt
     * @param array $settings Zusätzliche Einstellungen
     * @return bool Erfolg
     */
    public function saveSectionContent($sectionId, $languageCode, $title, $content, $settings = null) {
        // Prüfen, ob bereits ein Inhalt existiert
        $sql = "SELECT * FROM index_section_translations WHERE section_id = ? AND lang_code = ?";
        $existingContent = $this->db->selectOne($sql, [$sectionId, $languageCode]);
        
        $data = [
            'title' => $title,
            'content' => $content
        ];
        
        // Zusätzliche Einstellungen hinzufügen, wenn vorhanden
        if (is_array($settings)) {
            foreach ($settings as $key => $value) {
                $data[$key] = $value;
            }
        }
        
        if ($existingContent) {
            // Inhalt aktualisieren
            return $this->db->update('index_section_translations', $data, 'section_id = ? AND lang_code = ?', [$sectionId, $languageCode]);
        } else {
            // Neuen Inhalt erstellen
            $data['section_id'] = $sectionId;
            $data['lang_code'] = $languageCode;
            return $this->db->insert('index_section_translations', $data) ? true : false;
        }
    }

    /**
     * Speichert den Inhalt einer Karte für eine bestimmte Sprache
     * 
     * @param int $cardId Karten-ID
     * @param string $languageCode Sprachcode
     * @param string $title Titel
     * @param string $content Inhalt
     * @param string $buttonText Button-Text
     * @param string $buttonLink Button-Link
     * @return bool Erfolg
     */
    public function saveCardContent($cardId, $languageCode, $title, $content, $buttonText = null, $buttonLink = null) {
        // Prüfen, ob bereits ein Inhalt existiert
        $sql = "SELECT * FROM index_card_translations WHERE card_id = ? AND lang_code = ?";
        $existingContent = $this->db->selectOne($sql, [$cardId, $languageCode]);
        
        $data = [
            'title' => $title,
            'content' => $content,
            'button_text' => $buttonText,
            'button_url' => $buttonLink
        ];
        
        if ($existingContent) {
            // Inhalt aktualisieren
            return $this->db->update('index_card_translations', $data, 'card_id = ? AND lang_code = ?', [$cardId, $languageCode]);
        } else {
            // Neuen Inhalt erstellen
            $data['card_id'] = $cardId;
            $data['lang_code'] = $languageCode;
            return $this->db->insert('index_card_translations', $data) ? true : false;
        }
    }

    /**
     * Aktualisiert die Positionen mehrerer Sektionen
     * 
     * @param array $positions Array mit Sektions-IDs und Positionen
     * @return bool Erfolg
     */
    public function updateSectionPositions($positions) {
        if (!is_array($positions) || empty($positions)) {
            return false;
        }
        
        $success = true;
        foreach ($positions as $id => $position) {
            $result = $this->updateSectionPosition($id, $position);
            if (!$result) {
                $success = false;
            }
        }
        
        return $success;
    }

    /**
     * Aktualisiert die Positionen mehrerer Karten
     * 
     * @param array $positions Array mit Karten-IDs und Positionen
     * @return bool Erfolg
     */
    public function updateCardPositions($positions) {
        if (!is_array($positions) || empty($positions)) {
            return false;
        }
        
        $success = true;
        foreach ($positions as $id => $position) {
            $result = $this->updateCardPosition($id, $position);
            if (!$result) {
                $success = false;
            }
        }
        
        return $success;
    }
}
?>


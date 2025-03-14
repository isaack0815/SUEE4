<?php
require_once '../../init.php';
require_once '../../includes/auth_check.php';
require_once '../../classes/IndexCustomizer.php';

// Benutzer-Authentifizierung prüfen
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Keine Berechtigung']);
    exit;
}

// IndexCustomizer-Instanz erstellen
$indexCustomizer = new IndexCustomizer();
$language = Language::getInstance();
$logger = Logger::getInstance();
$db = Database::getInstance();

// Anfrage-Methode prüfen
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // GET-Parameter verarbeiten
    $action = $_GET['action'] ?? '';
    $response = ['success' => false, 'message' => 'Unbekannte Aktion'];

    switch ($action) {
        case 'getAvailableModules':
            // Verfügbare Module aus der Datenbank abrufen
            try {
                $sql = "SELECT id, type, background_color, text_color FROM index_sections 
                        WHERE active = 1 
                        GROUP BY type 
                        ORDER BY position";
                $moduleTypes = $db->select($sql);
                
                $modules = [];
                $currentLang = $language->getCurrentLanguage();
                
                foreach ($moduleTypes as $type) {
                    // Titel und Beschreibung für den Modultyp aus der Übersetzungstabelle holen
                    $sql = "SELECT title, content FROM index_section_translations 
                            WHERE section_id IN (SELECT id FROM index_sections WHERE type = ?) 
                            AND lang_code = ? 
                            LIMIT 1";
                    $moduleInfo = $db->selectOne($sql, [$type['type'], $currentLang]);
                    
                    if (!$moduleInfo) {
                        // Fallback: Direkt aus der Inhaltstabelle holen
                        $sql = "SELECT title, content FROM index_section_content 
                                WHERE section_id IN (SELECT id FROM index_sections WHERE type = ?) 
                                AND language_code = ? 
                                LIMIT 1";
                        $moduleInfo = $db->selectOne($sql, [$type['type'], $currentLang]);
                    }
                    
                    $modules[] = [
                        'id' => $type['id'],
                        'title' => ucfirst($type['type']),  // Fallback-Titel
                        'description' => '',  // Leere Standardbeschreibung
                        'background_color' => $type['background_color'],
                        'text_color' => $type['text_color'],
                        'settings' => []
                    ];
                    
                    // Wenn Informationen gefunden wurden, diese verwenden
                    if ($moduleInfo) {
                        $lastIndex = count($modules) - 1;
                        $modules[$lastIndex]['title'] = $moduleInfo['title'] ?? ucfirst($type['type']);
                        $modules[$lastIndex]['description'] = strip_tags($moduleInfo['content'] ?? '');
                    }
                }
                
                $response = [
                    'success' => true,
                    'modules' => $modules
                ];
            } catch (Exception $e) {
                $logger->error("Fehler beim Laden der verfügbaren Module: " . $e->getMessage(), "admin");
                $response = [
                    'success' => false,
                    'message' => 'Fehler beim Laden der verfügbaren Module: ' . $e->getMessage()
                ];
            }
            break;
            
        case 'getAvailableModuleDetails':
            // Details eines verfügbaren Moduls abrufen
            if (isset($_GET['moduleId'])) {
                $moduleId = (int)$_GET['moduleId'];
                
                try {
                    // Modul aus der Datenbank abrufen
                    $sql = "SELECT id, type, background_color, text_color FROM index_sections WHERE id = ? AND active = 1";
                    $moduleData = $db->selectOne($sql, [$moduleId]);
                    
                    if ($moduleData) {
                        $currentLang = $language->getCurrentLanguage();
                        
                        // Titel und Beschreibung für das Modul aus der Übersetzungstabelle holen
                        $sql = "SELECT title, subtitle, content FROM index_section_translations 
                                WHERE section_id = ? AND lang_code = ?";
                        $moduleContent = $db->selectOne($sql, [$moduleData['id'], $currentLang]);
                        
                        if (!$moduleContent) {
                            // Fallback: Direkt aus der Inhaltstabelle holen
                            $sql = "SELECT title, content, settings FROM index_section_content 
                                    WHERE section_id = ? AND language_code = ?";
                            $moduleContent = $db->selectOne($sql, [$moduleData['id'], $currentLang]);
                        }
                        
                        $module = [
                            'id' => $moduleData['id'],
                            'title' => $moduleContent['title'] ?? ucfirst($moduleData['type']),
                            'description' => strip_tags($moduleContent['content'] ?? ''),
                            'background_color' => $moduleData['background_color'],
                            'text_color' => $moduleData['text_color'],
                            'settings' => []
                        ];
                        
                        // Zusätzliche Einstellungen je nach Modultyp
                        if ($moduleData['type'] === 'hero') {
                            $module['settings']['subtitle'] = $moduleContent['subtitle'] ?? '';
                            $module['settings']['image'] = $moduleContent['settings'] ?? '';
                        } else if ($moduleData['type'] === 'cards') {
                            // Karten-Einstellungen abrufen
                            $sql = "SELECT COUNT(*) as card_count FROM index_cards WHERE section_id = ?";
                            $cardCount = $db->selectOne($sql, [$moduleData['id']]);
                            $module['settings']['card_count'] = $cardCount['card_count'] ?? 3;
                        }
                        
                        $response = [
                            'success' => true,
                            'module' => $module
                        ];
                    } else {
                        $response = [
                            'success' => false,
                            'message' => 'Modul nicht gefunden'
                        ];
                    }
                } catch (Exception $e) {
                    $logger->error("Fehler beim Laden der Moduldetails: " . $e->getMessage(), "admin");
                    $response = [
                        'success' => false,
                        'message' => 'Fehler beim Laden der Moduldetails: ' . $e->getMessage()
                    ];
                }
            }
            break;
            
        case 'getActiveModules':
            // Aktive Module für die Startseite abrufen
            $sections = $indexCustomizer->getAllSections($language->getCurrentLanguage());
            $activeModules = [];
            
            foreach ($sections as $section) {
                // Hole die position_vertical aus der Datenbank
                $sql = "SELECT position_vertical FROM index_sections WHERE id = ?";
                $positionVerticalData = $db->selectOne($sql, [$section['id']]);
                $positionVertical = isset($positionVerticalData['position_vertical']) ? (int)$positionVerticalData['position_vertical'] : 1; // Standard: Mitte
                
                $moduleData = [
                    'id' => $section['id'],
                    'title' => $section['contents'][$language->getCurrentLanguage()]['title'] ?? 'Ohne Titel',
                    'content' => $section['contents'][$language->getCurrentLanguage()]['content'] ?? '',
                    'position' => $section['position'],
                    'position_vertical' => $positionVertical, // Neue Eigenschaft
                    'type' => $section['type'],
                    'active' => $section['active']
                ];
                
                $activeModules[] = $moduleData;
            }
            
            $response = [
                'success' => true,
                'modules' => $activeModules
            ];
            break;
            
        case 'getModuleConfig':
            if (isset($_GET['moduleId'])) {
                $moduleId = (int)$_GET['moduleId'];
                $section = $indexCustomizer->getSection($moduleId);
                    
                if ($section) {
                    // Konfiguration für das Modul abrufen
                    $config = [
                        'type' => $section['type'],
                        'background_color' => $section['background_color'] ?? '',
                        'text_color' => $section['text_color'] ?? '',
                        'contents' => [] // Hier speichern wir die Inhalte für alle Sprachen
                    ];
                        
                    // Alle verfügbaren Sprachen abrufen
                    $availableLanguages = [];
                    $langQuery = "SELECT DISTINCT lang_code FROM language_strings ORDER BY lang_code";
                    $langResult = $db->select($langQuery);
                        
                    if ($langResult && count($langResult) > 0) {
                        foreach ($langResult as $langRow) {
                            $availableLanguages[] = $langRow['lang_code'];
                        }
                    }
                        
                    // Inhalte für alle Sprachen abrufen
                    foreach ($availableLanguages as $langCode) {
                        // Prüfen, ob Inhalte für diese Sprache existieren
                        if (isset($section['contents'][$langCode])) {
                            $config['contents'][$langCode] = [
                                'title' => $section['contents'][$langCode]['title'] ?? '',
                                'subtitle' => $section['contents'][$langCode]['subtitle'] ?? '',
                                'content' => $section['contents'][$langCode]['content'] ?? ''
                            ];
                        } else {
                            // Leere Standardwerte für diese Sprache
                            $config['contents'][$langCode] = [
                                'title' => '',
                                'subtitle' => '',
                                'content' => ''
                            ];
                        }
                    }
                        
                    $response = [
                        'success' => true,
                        'config' => $config
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Modul nicht gefunden'
                    ];
                }
            }
            break;
            
        case 'getSectionData':
            if (isset($_GET['id'])) {
                $sectionId = (int)$_GET['id'];
                $section = $indexCustomizer->getSection($sectionId);
                
                if ($section) {
                    $response = [
                        'success' => true,
                        'section' => $section
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Sektion nicht gefunden'
                    ];
                }
            }
            break;
        
            case 'getAvailableLanguages':
                // Verfügbare Sprachen abrufen
                $languages = [];
                
                // Abfrage, um nur die tatsächlichen Sprachcodes zu erhalten
                // Wir suchen nach den Sprachcodes direkt in der lang_code Spalte
                $query = "SELECT DISTINCT lang_code FROM language_strings ORDER BY lang_code";
                $result = $db->select($query);
                
                if ($result && count($result) > 0) {
                    foreach ($result as $row) {
                        $langCode = $row['lang_code'];
                        
                        if (!empty($langCode)) {
                            // Sprachnamen basierend auf dem Sprachcode definieren
                            $langName = '';
                            switch ($langCode) {
                                case 'de':
                                    $langName = 'Deutsch';
                                    break;
                                case 'en':
                                    $langName = 'English';
                                    break;
                                case 'fr':
                                    $langName = 'Français';
                                    break;
                                // Weitere Sprachen hier hinzufügen
                                default:
                                    $langName = ucfirst($langCode);
                            }
                            
                            $languages[] = [
                                'code' => $langCode,
                                'name' => $langName
                            ];
                        }
                    }
                }
                
                // Wenn keine Sprachen gefunden wurden, mindestens Deutsch als Standard zurückgeben
                if (empty($languages)) {
                    $languages[] = [
                        'code' => 'de',
                        'name' => 'Deutsch'
                    ];
                }
                
                // Antwort senden
                $response = [
                    'success' => true,
                    'languages' => $languages
                ];
                break;
    }
    
    // JSON-Antwort senden
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // JSON-Daten empfangen
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    
    // Wenn keine JSON-Daten, dann POST-Parameter verwenden
    if (!$data) {
        $data = $_POST;
    }
    
    if (!isset($data['action'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage']);
        exit;
    }
    
    // Aktionen verarbeiten
    $action = $data['action'];
    $response = ['success' => false, 'message' => 'Unbekannte Aktion'];
    
    switch ($action) {
        case 'saveAvailableModuleChanges':
            if (isset($data['moduleId']) && isset($data['title'])) {
                $moduleId = (int)$data['moduleId'];
                $title = $data['title'];
                $description = $data['description'] ?? '';
                $settings = $data['settings'] ?? [];
                
                try {
                    // Modul aus der Datenbank abrufen
                    $sql = "SELECT id, type FROM index_sections WHERE id = ? AND active = 1";
                    $moduleData = $db->selectOne($sql, [$moduleId]);
                    
                    if ($moduleData) {
                        $sectionId = $moduleData['id'];
                        $currentLang = $language->getCurrentLanguage();
                        
                        // Prüfen, ob ein Eintrag in der Übersetzungstabelle existiert
                        $sql = "SELECT 1 FROM index_section_translations WHERE section_id = ? AND lang_code = ?";
                        $translationExists = $db->selectOne($sql, [$sectionId, $currentLang]);
                        
                        if ($translationExists) {
                            // Übersetzung aktualisieren
                            $sql = "UPDATE index_section_translations 
                                    SET title = ?, content = ? 
                                    WHERE section_id = ? AND lang_code = ?";
                            $db->query($sql, [$title, $description, $sectionId, $currentLang]);
                        } else {
                            // Neue Übersetzung einfügen
                            $sql = "INSERT INTO index_section_translations (section_id, lang_code, title, content) 
                                    VALUES (?, ?, ?, ?)";
                            $db->query($sql, [$sectionId, $currentLang, $title, $description]);
                        }
                        
                        // Zusätzliche Einstellungen je nach Modultyp speichern
                        if ($moduleData['type'] === 'hero' && isset($settings['subtitle'])) {
                            $sql = "UPDATE index_section_translations 
                                    SET subtitle = ? 
                                    WHERE section_id = ? AND lang_code = ?";
                            $db->query($sql, [$settings['subtitle'], $sectionId, $currentLang]);
                        }
                        
                        $response = [
                            'success' => true,
                            'message' => 'Modul erfolgreich aktualisiert'
                        ];
                        $logger->info("Admin hat verfügbares Modul aktualisiert: $moduleId", "admin");
                    } else {
                        $response = [
                            'success' => false,
                            'message' => 'Modul nicht gefunden'
                        ];
                    }
                } catch (Exception $e) {
                    $logger->error("Fehler beim Speichern der Moduländerungen: " . $e->getMessage(), "admin");
                    $response = [
                        'success' => false,
                        'message' => 'Fehler beim Speichern der Moduländerungen: ' . $e->getMessage()
                    ];
                }
            }
            break;
            
        case 'createSection':
            if (isset($data['section']) && isset($data['contents'])) {
                $sectionData = [
                    'type' => $data['section']['type'] ?? 'text',
                    'active' => isset($data['section']['active']) ? (int)$data['section']['active'] : 1,
                    'background_color' => $data['section']['background_color'] ?? null,
                    'text_color' => $data['section']['text_color'] ?? null
                ];
                
                $sectionId = $indexCustomizer->createSection($sectionData, $data['contents']);
                
                if ($sectionId) {
                    $response = [
                        'success' => true,
                        'message' => 'Sektion erfolgreich erstellt',
                        'section_id' => $sectionId
                    ];
                    $logger->info("Admin hat neue Startseiten-Sektion erstellt: $sectionId", "admin");
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Fehler beim Erstellen der Sektion'
                    ];
                }
            }
            break;
            
        case 'updateSection':
            if (isset($data['section']['id']) && isset($data['contents'])) {
                $sectionId = (int)$data['section']['id'];
                $sectionData = [
                    'type' => $data['section']['type'] ?? null,
                    'active' => isset($data['section']['active']) ? (int)$data['section']['active'] : null,
                    'background_color' => $data['section']['background_color'] ?? null,
                    'text_color' => $data['section']['text_color'] ?? null
                ];
                
                $success = $indexCustomizer->updateSection($sectionId, $sectionData, $data['contents']);
                
                if ($success) {
                    $response = [
                        'success' => true,
                        'message' => 'Sektion erfolgreich aktualisiert'
                    ];
                    $logger->info("Admin hat Startseiten-Sektion aktualisiert: $sectionId", "admin");
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Fehler beim Aktualisieren der Sektion'
                    ];
                }
            }
            break;
            
        case 'deleteSection':
            if (isset($data['id'])) {
                $sectionId = (int)$data['id'];
                $success = $indexCustomizer->deleteSection($sectionId);
                
                if ($success) {
                    $response = [
                        'success' => true,
                        'message' => 'Sektion erfolgreich gelöscht'
                    ];
                    $logger->info("Admin hat Startseiten-Sektion gelöscht: $sectionId", "admin");
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Fehler beim Löschen der Sektion'
                    ];
                }
            }
            break;
            
        case 'addModule':
            if (isset($data['moduleId']) && isset($data['column'])) {
                $moduleType = $data['moduleId'];
                $column = $data['column'];
                $positionVertical = isset($data['position_vertical']) ? (int)$data['position_vertical'] : 1; // Standard: Mitte
                
                // Neue Sektion erstellen
                $sectionData = [
                    'type' => $moduleType,
                    'active' => 1
                ];
                
                // Inhalte für alle verfügbaren Sprachen erstellen
                $contents = [];
                $languages = $language->getAvailableLanguages();
                
                for($i = 0; $i < count($languages)-1; $i++) {
                    $contents[$languages[$i]] = [
                        'title' => 'Neuer ' . ucfirst($moduleType) . '-Bereich',
                        'content' => 'Inhalt für den ' . ucfirst($moduleType) . '-Bereich'
                    ];
                }
                
                $sectionId = $indexCustomizer->createSection($sectionData, $contents);
                
                if ($sectionId) {
                    // position_vertical aktualisieren
                    $sql = "UPDATE index_sections SET position_vertical = ? WHERE id = ?";
                    $db->query($sql, [$positionVertical, $sectionId]);
                    
                    $response = [
                        'success' => true,
                        'message' => 'Modul erfolgreich hinzugefügt',
                        'module_id' => $sectionId
                    ];
                    $logger->info("Admin hat neues Startseiten-Modul hinzugefügt: $moduleType", "admin");
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Fehler beim Hinzufügen des Moduls'
                    ];
                }
            }
            break;
            
        case 'removeModule':
            if (isset($data['moduleId'])) {
                $moduleId = (int)$data['moduleId'];
                $success = $indexCustomizer->deleteSection($moduleId);
                
                if ($success) {
                    $response = [
                        'success' => true,
                        'message' => 'Modul erfolgreich entfernt'
                    ];
                    $logger->info("Admin hat Startseiten-Modul entfernt: $moduleId", "admin");
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Fehler beim Entfernen des Moduls'
                    ];
                }
            }
            break;
            
        case 'updateModulePosition':
            if (isset($data['moduleId']) && isset($data['toPositionVertical'])) {
                $moduleId = (int)$data['moduleId'];
                $fromPositionVertical = isset($data['fromPositionVertical']) ? (int)$data['fromPositionVertical'] : 1; // Standard: Mitte
                $toPositionVertical = (int)$data['toPositionVertical'];
                $position = isset($data['position']) ? (int)$data['position'] : 0;
                
                // Hier die position_vertical in der Datenbank aktualisieren
                $sql = "UPDATE index_sections SET position_vertical = ? WHERE id = ?";
                $db->query($sql, [$toPositionVertical, $moduleId]);
                
                // Position aktualisieren
                $success = $indexCustomizer->updateSectionPosition($moduleId, $position);
                
                if ($success) {
                    $response = [
                        'success' => true,
                        'message' => 'Modulposition erfolgreich aktualisiert'
                    ];
                    $logger->info("Admin hat Startseiten-Modulposition aktualisiert: $moduleId", "admin");
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Fehler beim Aktualisieren der Modulposition'
                    ];
                }
            }
            break;
            
        case 'saveModuleConfig':
            if (isset($data['moduleId']) && isset($data['config'])) {
                $moduleId = (int)$data['moduleId'];
                $config = $data['config'];
                
                // Sektionsdaten aktualisieren
                $sectionData = [
                    'background_color' => $config['background_color'] ?? null,
                    'text_color' => $config['text_color'] ?? null
                ];
                
                // Inhalte für die aktuelle Sprache aktualisieren
                $currentLang = $language->getCurrentLanguage();
                $contents = [
                    $currentLang => [
                        'title' => $config['title'] ?? '',
                        'content' => $config['content'] ?? ''
                    ]
                ];
                
                $success = $indexCustomizer->updateSection($moduleId, $sectionData, $contents);
                
                if ($success) {
                    $response = [
                        'success' => true,
                        'message' => 'Modulkonfiguration erfolgreich gespeichert'
                    ];
                    $logger->info("Admin hat Startseiten-Modulkonfiguration aktualisiert: $moduleId", "admin");
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Fehler beim Speichern der Modulkonfiguration'
                    ];
                }
            }
            break;
            
        case 'updateSectionPositions':
            if (isset($data['positions']) && is_array($data['positions'])) {
                $success = true;
                
                foreach ($data['positions'] as $position) {
                    if (isset($position['id']) && isset($position['position'])) {
                        $sectionId = (int)$position['id'];
                        $pos = (int)$position['position'];
                        
                        if (!$indexCustomizer->updateSectionPosition($sectionId, $pos)) {
                            $success = false;
                        }
                    }
                }
                
                if ($success) {
                    $response = [
                        'success' => true,
                        'message' => 'Sektionspositionen erfolgreich aktualisiert'
                    ];
                    $logger->info("Admin hat Startseiten-Sektionspositionen aktualisiert", "admin");
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Fehler beim Aktualisieren der Sektionspositionen'
                    ];
                }
            }
            break;

            case 'getAvailableLanguages':
                // Verfügbare Sprachen abrufen
                $languages = [];
                        
                // Abfrage, um alle verfügbaren Sprachen aus der Datenbank zu holen
                // Wir suchen nach Einträgen, die mit 'lang_' beginnen und extrahieren den ISO-Code
                $query = "SELECT DISTINCT lang_key FROM language_strings WHERE lang_key LIKE 'lang_%' ORDER BY lang_key";
                $result = $db->select($query);
                        
                if ($result && count($result) > 0) {
                    for($i = 0; $i < count($result)-1; $i++) {
                        $row = $result[$i];
                        // ISO-Code aus dem lang_key extrahieren (z.B. 'lang_de' -> 'de')
                        $langKey = $row['lang_key'];
                        $isoCode = substr($langKey, 5); // Entfernt 'lang_' vom Anfang
                        
                        if (!empty($isoCode)) {
                            // Sprachnamen basierend auf dem ISO-Code definieren
                            $langName = '';
                            switch ($isoCode) {
                                case 'de':
                                    $langName = 'Deutsch';
                                    break;
                                case 'en':
                                    $langName = 'English';
                                    break;
                                case 'fr':
                                    $langName = 'Français';
                                    break;
                                // Weitere Sprachen hier hinzufügen
                                default:
                                    $langName = ucfirst($isoCode);
                            }
                            
                            $languages[] = [
                                'code' => $isoCode,
                                'name' => $langName
                            ];
                        }
                    }
                }
                        
                // Wenn keine Sprachen gefunden wurden, mindestens Deutsch als Standard zurückgeben
                if (empty($languages)) {
                    $languages[] = [
                        'code' => 'de',
                        'name' => 'Deutsch'
                    ];
                }
                    
                // Antwort senden
                $response = [
                    'success' => true,
                    'languages' => $languages
                ];
                break;
    }
    
    // JSON-Antwort senden
    header('Content-Type: application/json');
    echo json_encode($response);
}
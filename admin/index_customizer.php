<?php
require_once '../init.php';
require_once '../includes/auth_check.php';
require_once '../classes/IndexCustomizer.php';


if (!$user->isLoggedIn() || !$user->isAdmin()) {
    $_SESSION['error_message'] = 'no_permission';
    header('Location: ../index.php');
    exit;
}
$language = Language::getInstance();
$indexCustomizer = new IndexCustomizer();

// Logger-Instanz abrufen
$logger = Logger::getInstance();
$logger->info("Admin hat die Startseiten-Anpassung aufgerufen", "admin");

// Menü laden
$menu = new Menu();
$adminMenu = $menu->getMenuItems('admin');

// Aktiven Menüpunkt setzen
$smarty->assign('activeMenuItem', 'index_customizer');

// Language-Instanz erstellen
$language = Language::getInstance();
$languageCode = $language->getCurrentLanguage();

// IndexCustomizer-Instanz erstellen
$indexCustomizer = new IndexCustomizer();
$sections = $indexCustomizer->getAllSections($languageCode);

// Verfügbare Sprachen abrufen
$availableLanguages = $language->getAvailableLanguages();

// AJAX-Anfragen verarbeiten
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => 'Unbekannte Aktion'];
    
    switch ($_POST['action']) {
        case 'create_section':
            $type = $_POST['type'] ?? 'text';
            $position = $_POST['position'] ?? 0;
            
            $sectionId = $indexCustomizer->createSection($type, $position);
            if ($sectionId) {
                $response = [
                    'success' => true,
                    'message' => 'Sektion erfolgreich erstellt',
                    'section_id' => $sectionId
                ];
                $logger = Logger::getInstance();
                $logger->info("Admin hat neue Startseiten-Sektion erstellt: $sectionId", "admin");
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Fehler beim Erstellen der Sektion'
                ];
            }
            break;
            
        case 'update_section':
            $sectionId = $_POST['section_id'] ?? 0;
            $data = [
                'type' => $_POST['type'] ?? 'text',
                'active' => isset($_POST['active']) ? (int)$_POST['active'] : 1
            ];
            
            if ($indexCustomizer->updateSection($sectionId, $data)) {
                $response = [
                    'success' => true,
                    'message' => 'Sektion erfolgreich aktualisiert'
                ];
                $logger = Logger::getInstance();
                $logger->info("Admin hat Startseiten-Sektion aktualisiert: $sectionId", "admin");
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Fehler beim Aktualisieren der Sektion'
                ];
            }
            break;
            
        case 'delete_section':
            $sectionId = $_POST['section_id'] ?? 0;
            
            if ($indexCustomizer->deleteSection($sectionId)) {
                $response = [
                    'success' => true,
                    'message' => 'Sektion erfolgreich gelöscht'
                ];
                $logger = Logger::getInstance();
                $logger->info("Admin hat Startseiten-Sektion gelöscht: $sectionId", "admin");
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Fehler beim Löschen der Sektion'
                ];
            }
            break;
            
        case 'save_section_content':
            $sectionId = $_POST['section_id'] ?? 0;
            $languageCode = $_POST['language_code'] ?? '';
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $settings = $_POST['settings'] ?? null;
            
            if ($indexCustomizer->saveSectionContent($sectionId, $languageCode, $title, $content, $settings)) {
                $response = [
                    'success' => true,
                    'message' => 'Sektionsinhalt erfolgreich gespeichert'
                ];
                $logger = Logger::getInstance();
                $logger->info("Admin hat Inhalt für Startseiten-Sektion $sectionId in Sprache $languageCode aktualisiert", "admin");
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Fehler beim Speichern des Sektionsinhalts'
                ];
            }
            break;
            
        case 'create_card':
            $sectionId = $_POST['section_id'] ?? 0;
            $position = $_POST['position'] ?? 0;
            $icon = $_POST['icon'] ?? null;
            $bgColor = $_POST['bg_color'] ?? null;
            $textColor = $_POST['text_color'] ?? null;
            
            $cardId = $indexCustomizer->createCard($sectionId, $position, $icon, $bgColor, $textColor);
            if ($cardId) {
                $response = [
                    'success' => true,
                    'message' => 'Karte erfolgreich erstellt',
                    'card_id' => $cardId
                ];
                $logger = Logger::getInstance();
                $logger->info("Admin hat neue Karte für Startseiten-Sektion $sectionId erstellt: $cardId", "admin");
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Fehler beim Erstellen der Karte'
                ];
            }
            break;
            
        case 'update_card':
            $cardId = $_POST['card_id'] ?? 0;
            $data = [
                'icon' => $_POST['icon'] ?? null,
                'bg_color' => $_POST['bg_color'] ?? null,
                'text_color' => $_POST['text_color'] ?? null,
                'active' => isset($_POST['active']) ? (int)$_POST['active'] : 1
            ];
            
            if ($indexCustomizer->updateCard($cardId, $data)) {
                $response = [
                    'success' => true,
                    'message' => 'Karte erfolgreich aktualisiert'
                ];
                $logger = Logger::getInstance();
                $logger->info("Admin hat Karte aktualisiert: $cardId", "admin");
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Fehler beim Aktualisieren der Karte'
                ];
            }
            break;
            
        case 'delete_card':
            $cardId = $_POST['card_id'] ?? 0;
            
            if ($indexCustomizer->deleteCard($cardId)) {
                $response = [
                    'success' => true,
                    'message' => 'Karte erfolgreich gelöscht'
                ];
                $logger = Logger::getInstance();
                $logger->info("Admin hat Karte gelöscht: $cardId", "admin");
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Fehler beim Löschen der Karte'
                ];
            }
            break;
            
        case 'save_card_content':
            $cardId = $_POST['card_id'] ?? 0;
            $languageCode = $_POST['language_code'] ?? '';
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $buttonText = $_POST['button_text'] ?? null;
            $buttonLink = $_POST['button_link'] ?? null;
            
            if ($indexCustomizer->saveCardContent($cardId, $languageCode, $title, $content, $buttonText, $buttonLink)) {
                $response = [
                    'success' => true,
                    'message' => 'Karteninhalt erfolgreich gespeichert'
                ];
                $logger = Logger::getInstance();
                $logger->info("Admin hat Inhalt für Karte $cardId in Sprache $languageCode aktualisiert", "admin");
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Fehler beim Speichern des Karteninhalts'
                ];
            }
            break;
            
        case 'update_section_positions':
            $positions = $_POST['positions'] ?? [];
            
            if ($indexCustomizer->updateSectionPositions($positions)) {
                $response = [
                    'success' => true,
                    'message' => 'Sektionspositionen erfolgreich aktualisiert'
                ];
                $logger = Logger::getInstance();
                $logger->info("Admin hat Startseiten-Sektionspositionen aktualisiert", "admin");
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Fehler beim Aktualisieren der Sektionspositionen'
                ];
            }
            break;
            
        case 'update_card_positions':
            $positions = $_POST['positions'] ?? [];
            
            if ($indexCustomizer->updateCardPositions($positions)) {
                $response = [
                    'success' => true,
                    'message' => 'Kartenpositionen erfolgreich aktualisiert'
                ];
                $logger = Logger::getInstance();
                $logger->info("Admin hat Kartenpositionen aktualisiert", "admin");
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Fehler beim Aktualisieren der Kartenpositionen'
                ];
            }
            break;
    }
    
    echo json_encode($response);
    exit;
}

// Template anzeigen
$smarty->assign('adminMenu', $adminMenu);
$smarty->display('admin/index_customizer.tpl');


<?php
require_once '../../init.php';
require_once '../../includes/auth_check.php';

// Prüfen, ob Benutzer eingeloggt ist und die erforderlichen Rechte hat
if (!checkPermission($user, 'language.view')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'unauthorized']);
    exit;
}

// Sprachverwaltung initialisieren
$language = Language::getInstance();

// HTTP-Methode bestimmen
$method = $_SERVER['REQUEST_METHOD'];

// Antwort vorbereiten
$response = ['success' => false, 'message' => 'invalid_request'];

switch ($method) {
    case 'GET':
        // Aktion bestimmen
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    
        if ($action === 'list') {
            // Paginierungsparameter
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
            $offset = ($page - 1) * $limit;
            
            // Filter- und Suchparameter
            $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            
            // Sprachschlüssel abrufen - Direkte Übergabe der Integer-Werte
            $keys = $language->getLanguageKeys($filter, $search, (int)$limit, (int)$offset);
            $total = $language->countLanguageKeys($filter, $search);
            
            $response = [
                'success' => true,
                'data' => $keys,
                'total' => $total,
                'page' => $page,
                'limit' => $limit
            ];
        } elseif ($action === 'get_key' && isset($_GET['key'])) {
            // Einzelnen Sprachschlüssel abrufen
            $key = $_GET['key'];
            $keyData = $language->getLanguageKey($key);
            
            if ($keyData) {
                $response = ['success' => true, 'data' => $keyData];
            } else {
                $response = ['success' => false, 'message' => 'key_not_found'];
            }
        } else {
            $response = ['success' => false, 'message' => 'invalid_action'];
        }
        break;
        
    case 'POST':
        // Prüfen, ob Benutzer die erforderlichen Rechte hat
        if (!checkPermission($user, 'language.create')) {
            $response = ['success' => false, 'message' => 'no_permission'];
            break;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Aktion bestimmen
        $action = isset($_GET['action']) ? $_GET['action'] : 'add_language';
        
        if ($action === 'add_language') {
            // Neue Sprache hinzufügen
            if (!isset($data['lang_code']) || empty($data['lang_code'])) {
                $response = ['success' => false, 'message' => 'lang_code_required'];
                break;
            }
            
            if (!isset($data['lang_name']) || empty($data['lang_name'])) {
                $response = ['success' => false, 'message' => 'lang_name_required'];
                break;
            }
            
            $result = $language->addLanguage($data['lang_code'], $data['lang_name']);
            $response = $result;
        } elseif ($action === 'add_key') {
            // Neuen Sprachschlüssel hinzufügen
            if (!isset($data['lang_key']) || empty($data['lang_key'])) {
                $response = ['success' => false, 'message' => 'lang_key_required'];
                break;
            }
            
            if (!isset($data['values']) || !is_array($data['values'])) {
                $response = ['success' => false, 'message' => 'values_required'];
                break;
            }
            
            $result = $language->addLanguageKey($data['lang_key'], $data['values']);
            $response = $result;
        } else {
            $response = ['success' => false, 'message' => 'invalid_action'];
        }
        break;
        
    case 'PUT':
        // Prüfen, ob Benutzer die erforderlichen Rechte hat
        if (!checkPermission($user, 'language.edit')) {
            $response = ['success' => false, 'message' => 'no_permission'];
            break;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Aktion bestimmen
        $action = isset($_GET['action']) ? $_GET['action'] : 'update_key';
        
        if ($action === 'update_key') {
            // Sprachschlüssel aktualisieren
            if (!isset($data['lang_key']) || empty($data['lang_key'])) {
                $response = ['success' => false, 'message' => 'lang_key_required'];
                break;
            }
            
            if (!isset($data['values']) || !is_array($data['values'])) {
                $response = ['success' => false, 'message' => 'values_required'];
                break;
            }
            
            $result = $language->updateLanguageKey($data['lang_key'], $data['values']);
            $response = $result;
        } else {
            $response = ['success' => false, 'message' => 'invalid_action'];
        }
        break;
        
    case 'DELETE':
        // Prüfen, ob Benutzer die erforderlichen Rechte hat
        if (!checkPermission($user, 'language.delete')) {
            $response = ['success' => false, 'message' => 'no_permission'];
            break;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Aktion bestimmen
        $action = isset($_GET['action']) ? $_GET['action'] : 'delete_key';
        
        if ($action === 'delete_key') {
            // Sprachschlüssel löschen
            if (!isset($data['lang_key']) || empty($data['lang_key'])) {
                $response = ['success' => false, 'message' => 'lang_key_required'];
                break;
            }
            
            $result = $language->deleteLanguageKey($data['lang_key']);
            $response = $result;
        } else {
            $response = ['success' => false, 'message' => 'invalid_action'];
        }
        break;
}

// Antwort senden
header('Content-Type: application/json');
echo json_encode($response);
?>

